<?php

namespace App\Http\Controllers;

use App\Models\RetainedMember;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Exports\RetainedMemberExport;


class RetainedMemberController extends Controller
{
    public function index(Request $request)
    {
        $data = $this->getRetainedMemberData();
        return view('retained-members.index', $data);
    }

    protected function getRetainedMemberData()
    {
        $user = auth()->user();

        $query = RetainedMember::with(['church.churchGroup.churchCategory', 'pcf.churchGroup.churchCategory', 'bringer', 'attendanceLogs']);

        if ($user->hasRole('Admin')) {
            $church = $user->church();
            if ($church) {
                $pcfIds = \App\Models\Pcf::where('church_group_id', $church->church_group_id)->pluck('id');
                $query->where(function ($q) use ($church, $pcfIds) {
                    $q->where('church_id', $church->id)
                        ->orWhereIn('pcf_id', $pcfIds);
                });
            }
        } elseif ($user->hasRole('Official')) {
            $pcfIds = $user->pcfs()->pluck('id');
            $query->whereIn('pcf_id', $pcfIds);
        }

        $allMembers = $query->latest()->get();

        $availableChurches = $allMembers->pluck('church')->filter()->unique('id')->sortBy('name');
        $availablePcfs = $allMembers->pluck('pcf')->filter()->unique('id')->sortBy('name');

        $bringers = \App\Models\Bringer::with(['firstTimers', 'retainedMembers'])->get()->map(function ($b) {
            $b->pcf_ids = $b->firstTimers->pluck('pcf_id')
                ->merge($b->retainedMembers->pluck('pcf_id'))
                ->filter()
                ->unique()
                ->values();
            return $b;
        });

        $groupedMembers = [];

        foreach ($allMembers as $member) {
            $categoryName = 'Unassigned';
            $groupName = 'Unassigned';
            $entityName = 'Unassigned';

            if ($member->pcf && $member->pcf->churchGroup) {
                $categoryName = strtoupper($member->pcf->churchGroup->churchCategory->name ?? 'Unassigned');
                $groupName = strtoupper($member->pcf->churchGroup->group_name ?? 'Unassigned');
                $entityName = strtoupper($member->pcf->name);
            } elseif ($member->church && $member->church->churchGroup) {
                $categoryName = strtoupper($member->church->churchGroup->churchCategory->name ?? 'Unassigned');
                $groupName = strtoupper($member->church->churchGroup->group_name ?? 'Unassigned');
                $entityName = strtoupper($member->church->name);
            }

            if (!isset($groupedMembers[$categoryName])) {
                $groupedMembers[$categoryName] = [];
            }
            if (!isset($groupedMembers[$categoryName][$groupName])) {
                $groupedMembers[$categoryName][$groupName] = [];
            }
            if (!isset($groupedMembers[$categoryName][$groupName][$entityName])) {
                $groupedMembers[$categoryName][$groupName][$entityName] = [];
            }

            $groupedMembers[$categoryName][$groupName][$entityName][] = $member;
        }

        // Sort categories to ensure ZONAL CHURCH is first
        uksort($groupedMembers, function ($a, $b) {
            if ($a === 'ZONAL CHURCH')
                return -1;
            if ($b === 'ZONAL CHURCH')
                return 1;
            return strcmp($a, $b);
        });

        // Pre-calculate earliest visit dates
        $allMemberIds = $allMembers->pluck('id');
        $earliestLogDates = \App\Models\AttendanceLog::whereIn('retained_member_id', $allMemberIds)
            ->selectRaw('retained_member_id, MIN(service_date) as earliest_log')
            ->groupBy('retained_member_id')
            ->pluck('earliest_log', 'retained_member_id');

        foreach ($allMembers as $member) {
            $candidates = collect();
            if ($member->date_of_visit) {
                $candidates->push(\Carbon\Carbon::parse($member->date_of_visit)->toDateString());
            }
            if (isset($earliestLogDates[$member->id])) {
                $candidates->push(\Carbon\Carbon::parse($earliestLogDates[$member->id])->toDateString());
            }
            $member->global_first_visit = $candidates->sort()->first();
        }

        return [
            'groupedMembers' => $groupedMembers,
            'availableChurches' => $availableChurches,
            'availablePcfs' => $availablePcfs,
            'bringers' => $bringers,
            'unacknowledgedCount' => $allMembers->where('acknowledged', false)->count()
        ];
    }

    public function downloadExcel()
    {
        $data = $this->getRetainedMemberData();
        return Excel::download(new RetainedMemberExport($data['groupedMembers']), 'retained_members_' . now()->format('Y-m-d') . '.xlsx');
    }

    public function downloadPdf()
    {
        $data = $this->getRetainedMemberData();
        $pdf = Pdf::loadView('retained-members.pdf', [
            'groupedMembers' => $data['groupedMembers'],
            'title' => 'Retained Members'
        ])->setPaper('a4', 'landscape');

        return $pdf->download('retained_members_' . now()->format('Y-m-d') . '.pdf');
    }


    public function show(RetainedMember $retainedMember)
    {
        $retainedMember->load(['church', 'pcf', 'bringer', 'attendanceLogs']);

        // Explicitly calculate global_first_visit for the JSON response
        $earliestLog = $retainedMember->attendanceLogs()->min('service_date');
        $candidates = collect();
        if ($retainedMember->date_of_visit) {
            $candidates->push(\Carbon\Carbon::parse($retainedMember->date_of_visit)->toDateString());
        }
        if ($earliestLog) {
            $candidates->push(\Carbon\Carbon::parse($earliestLog)->toDateString());
        }
        $retainedMember->global_first_visit = $candidates->sort()->first();

        return response()->json($retainedMember);
    }

    public function update(Request $request, RetainedMember $retainedMember)
    {
        $validated = $request->validate([
            'full_name' => 'required|string|max:255',
            'email' => 'nullable|email',
            'primary_contact' => 'required|string',
            'alternate_contact' => 'nullable|string',
            'gender' => 'required|in:Male,Female',
            'date_of_birth' => 'nullable|string',
            'residential_address' => 'required|string',
            'occupation' => 'nullable|string',
            'marital_status' => 'required|string',
            'born_again' => 'boolean',
            'water_baptism' => 'boolean',
            'prayer_requests' => 'nullable|string',
            'church_id' => 'nullable|exists:churches,id',
            'pcf_id' => 'nullable|exists:pcfs,id',
            'bringer_id' => 'nullable|exists:bringers,id',
        ]);

        $retainedMember->update($validated);

        return response()->json(['success' => true]);
    }

    public function toggleAttendance(Request $request)
    {
        $validated = $request->validate([
            'retained_member_id' => 'required|exists:retained_members,id',
            'service_date' => 'required|date',
        ]);

        $member = RetainedMember::findOrFail($validated['retained_member_id']);
        $date = \Carbon\Carbon::parse($validated['service_date'])->toDateString();

        // Cannot toggle the true initial (earliest) visit
        if ($member->earliest_visit_date === $date) {
            return response()->json(['error' => 'Initial visit cannot be untoggled.'], 422);
        }

        $log = \App\Models\AttendanceLog::where('retained_member_id', $member->id)
            ->whereDate('service_date', $date)
            ->first();

        $reverted = false;

        \Illuminate\Support\Facades\DB::transaction(function () use ($member, $log, $date, &$reverted) {
            if ($log) {
                $log->delete();
                $member->decrement('service_count');

                if ($member->service_count < 3) {
                    // Revert to First Timer
                    $data = $member->toArray();
                    unset($data['id'], $data['retained_date'], $data['earliest_visit_date']);
                    $data['locked'] = false;

                    $firstTimer = \App\Models\FirstTimer::create($data);

                    // Re-link all attendance logs
                    \App\Models\AttendanceLog::where('retained_member_id', $member->id)
                        ->update([
                            'first_timer_id' => $firstTimer->id,
                            'retained_member_id' => null
                        ]);

                    $member->delete();
                    $reverted = true;
                }
            } else {
                \App\Models\AttendanceLog::create([
                    'retained_member_id' => $member->id,
                    'service_date' => $date,
                    'marked_by' => auth()->id(),
                ]);
                $member->increment('service_count');
            }
        });

        return response()->json([
            'success' => true,
            'count' => $reverted ? 0 : $member->fresh()?->service_count ?? 0, // 0 indicates moved to FT
            'reverted' => $reverted,
            'message' => $reverted ? 'Member attendance corrected and moved back to First Timers.' : 'Attendance updated.'
        ]);
    }

    public function acknowledge(RetainedMember $retainedMember)
    {
        $retainedMember->update(['acknowledged' => true]);
        return response()->json(['success' => true]);
    }

    public function acknowledgeAll()
    {
        $user = auth()->user();
        $query = RetainedMember::where('acknowledged', false);

        if ($user->hasRole('Admin')) {
            $church = $user->church();
            if ($church) {
                $pcfIds = \App\Models\Pcf::where('church_group_id', $church->church_group_id)->pluck('id');
                $query->where(function ($q) use ($church, $pcfIds) {
                    $q->where('church_id', $church->id)->orWhereIn('pcf_id', $pcfIds);
                });
            }
        } elseif ($user->hasRole('Official')) {
            $pcfIds = $user->pcfs()->pluck('id');
            $query->whereIn('pcf_id', $pcfIds);
        }

        $query->update(['acknowledged' => true]);

        return response()->json(['success' => true]);
    }
}
