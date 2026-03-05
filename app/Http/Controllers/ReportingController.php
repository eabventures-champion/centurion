<?php

namespace App\Http\Controllers;

use App\Models\Church;
use App\Models\ChurchGroup;
use App\Models\FirstTimer;
use App\Models\Pcf;
use App\Models\RetainedMember;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;

class ReportingController extends Controller
{
    public function index(Request $request)
    {
        $data = $this->getReportingData($request);
        return view('reporting.index', $data);
    }

    public function downloadExcel(Request $request)
    {
        $data = $this->getReportingData($request);
        $filename = 'weekly_report_' . $data['weekStart']->format('Y_m_d') . '.xlsx';
        return Excel::download(new \App\Exports\ReportingExport($data), $filename);
    }

    public function downloadPdf(Request $request)
    {
        $data = $this->getReportingData($request);
        $pdf = Pdf::loadView('reporting.pdf', $data);
        $filename = 'weekly_report_' . $data['weekStart']->format('Y_m_d') . '.pdf';
        return $pdf->download($filename);
    }

    private function getReportingData(Request $request)
    {
        $user = auth()->user();
        $weekStart = $request->has('week_start')
            ? Carbon::parse($request->week_start)->startOfWeek(Carbon::SUNDAY)
            : Carbon::now()->startOfWeek(Carbon::SUNDAY);
        $weekEnd = (clone $weekStart)->endOfWeek(Carbon::SATURDAY);

        // 1. Data Retrieval: First Timers + Retained Members (by original date_of_visit)
        $firstTimersQuery = FirstTimer::whereBetween('date_of_visit', [$weekStart, $weekEnd]);
        $retainedMembersQuery = RetainedMember::whereBetween('date_of_visit', [$weekStart, $weekEnd]);

        // 2. Permission Scoping
        if ($user->hasRole('Official')) {
            $officialPcfIds = $user->pcfs()->pluck('id');
            $firstTimersQuery->whereIn('pcf_id', $officialPcfIds);
            $retainedMembersQuery->whereIn('pcf_id', $officialPcfIds);
        } elseif ($user->hasRole('Admin')) {
            $church = $user->church();
            if ($church) {
                $pcfIds = Pcf::where('church_group_id', $church->church_group_id)->pluck('id');
                $firstTimersQuery->where(function ($q) use ($church, $pcfIds) {
                    $q->where('church_id', $church->id)->orWhereIn('pcf_id', $pcfIds);
                });
                $retainedMembersQuery->where(function ($q) use ($church, $pcfIds) {
                    $q->where('church_id', $church->id)->orWhereIn('pcf_id', $pcfIds);
                });
            }
        }

        $allVisitors = collect($firstTimersQuery->get())->concat($retainedMembersQuery->get());

        // 3. Aggregate by PCF
        $pcfData = Pcf::query();
        if ($user->hasRole('Official')) {
            $pcfData->whereIn('id', $user->pcfs()->pluck('id'));
        } elseif ($user->hasRole('Admin')) {
            $pcfData->where('church_group_id', $user->church()->church_group_id);
        }

        $pcfs = $pcfData->with('churchGroup')->get()->map(function ($pcf) use ($allVisitors) {
            $pcf->visitor_count = $allVisitors->where('pcf_id', $pcf->id)->count();
            return $pcf;
        })->sortByDesc('visitor_count');

        // 4. Aggregate by Church
        $churchData = Church::query();
        if ($user->hasRole('Admin')) {
            $churchData->where('id', $user->church()->id);
        } elseif ($user->hasRole('Official')) {
            $churchData->whereRaw('1=0');
        }

        $churches = $churchData->with('churchGroup')->get()->map(function ($church) use ($allVisitors) {
            $church->visitor_count = $allVisitors->where('church_id', $church->id)->count();
            return $church;
        })->sortByDesc('visitor_count');

        // 5. Group Summations
        $groups = ChurchGroup::with(['pcfs', 'churches', 'churchCategory'])->get()->map(function ($group) use ($pcfs, $churches) {
            $group->pcf_total = $pcfs->where('church_group_id', $group->id)->sum('visitor_count');
            $group->church_total = $churches->where('church_group_id', $group->id)->sum('visitor_count');

            // Determine focus based on category (LAA/AVENOR are under ZONAL CHURCH)
            $group->is_pcf_focused = ($group->churchCategory && $group->churchCategory->name === 'ZONAL CHURCH');

            // Grand total matches focus
            $group->grand_total = $group->is_pcf_focused ? $group->pcf_total : $group->church_total;

            return $group;
        })->filter(fn($g) => $g->grand_total > 0 || $user->hasRole('Super Admin'));

        return [
            'weekStart' => $weekStart,
            'weekEnd' => $weekEnd,
            'pcfs' => $pcfs,
            'churches' => $churches,
            'groups' => $groups,
            'totalFirstTimers' => $allVisitors->count()
        ];
    }
}
