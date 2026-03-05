<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreFirstTimerRequest;
use App\Http\Requests\UpdateFirstTimerRequest;
use App\Models\FirstTimer;
use App\Models\Church;
use App\Models\ChurchGroup;
use App\Models\Bringer;
use App\Services\FirstTimerService;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Exports\FirstTimerExport;


class FirstTimerController extends Controller
{
    protected $firstTimerService;
    protected $contactCheckService;

    public function __construct(FirstTimerService $firstTimerService, \App\Services\ContactCheckService $contactCheckService)
    {
        $this->firstTimerService = $firstTimerService;
        $this->contactCheckService = $contactCheckService;
    }

    public function index()
    {
        $data = $this->getFirstTimerData();
        return view('first-timers.index', $data);
    }

    protected function getFirstTimerData()
    {
        $user = auth()->user();

        $query = FirstTimer::with(['church.churchGroup.churchCategory', 'pcf.churchGroup.churchCategory', 'bringer', 'attendanceLogs']);

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

        $allFirstTimers = $query->latest()->get();

        $availableChurches = $allFirstTimers->pluck('church')->filter()->unique('id')->sortBy('name');
        $availablePcfs = $allFirstTimers->pluck('pcf')->filter()->unique('id')->sortBy('name');

        $groupedFirstTimers = [];

        foreach ($allFirstTimers as $ft) {
            $categoryName = 'Unassigned';
            $groupName = 'Unassigned';
            $entityName = 'Unassigned';

            if ($ft->pcf && $ft->pcf->churchGroup) {
                $categoryName = strtoupper($ft->pcf->churchGroup->churchCategory->name ?? 'Unassigned');
                $groupName = strtoupper($ft->pcf->churchGroup->group_name ?? 'Unassigned');
                $entityName = strtoupper($ft->pcf->name);
            } elseif ($ft->church && $ft->church->churchGroup) {
                $categoryName = strtoupper($ft->church->churchGroup->churchCategory->name ?? 'Unassigned');
                $groupName = strtoupper($ft->church->churchGroup->group_name ?? 'Unassigned');
                $entityName = strtoupper($ft->church->name);
            }

            if (!isset($groupedFirstTimers[$categoryName])) {
                $groupedFirstTimers[$categoryName] = [];
            }
            if (!isset($groupedFirstTimers[$categoryName][$groupName])) {
                $groupedFirstTimers[$categoryName][$groupName] = [];
            }
            if (!isset($groupedFirstTimers[$categoryName][$groupName][$entityName])) {
                $groupedFirstTimers[$categoryName][$groupName][$entityName] = [];
            }

            $groupedFirstTimers[$categoryName][$groupName][$entityName][] = $ft;
        }

        // Sort categories to ensure ZONAL CHURCH is first
        uksort($groupedFirstTimers, function ($a, $b) {
            if ($a === 'ZONAL CHURCH')
                return -1;
            if ($b === 'ZONAL CHURCH')
                return 1;
            return strcmp($a, $b);
        });

        return [
            'groupedFirstTimers' => $groupedFirstTimers,
            'availableChurches' => $availableChurches,
            'availablePcfs' => $availablePcfs
        ];
    }

    public function downloadExcel()
    {
        $data = $this->getFirstTimerData();
        return Excel::download(new FirstTimerExport($data['groupedFirstTimers']), 'first_timers_' . now()->format('Y-m-d') . '.xlsx');
    }

    public function downloadPdf()
    {
        $data = $this->getFirstTimerData();
        $pdf = Pdf::loadView('first-timers.pdf', [
            'groupedFirstTimers' => $data['groupedFirstTimers'],
            'title' => 'First Timers Register'
        ])->setPaper('a4', 'landscape');

        return $pdf->download('first_timers_' . now()->format('Y-m-d') . '.pdf');
    }


    public function create()
    {
        $churchGroups = ChurchGroup::with('churchCategory')->get();
        $bringers = Bringer::all();
        return view('first-timers.create', compact('churchGroups', 'bringers'));
    }

    public function store(StoreFirstTimerRequest $request)
    {
        $firstTimer = $this->firstTimerService->registerFirstTimer($request->validated());

        if ($request->wantsJson()) {
            return response()->json($firstTimer, 201);
        }

        return redirect()->route('first-timers.index')->with('success', 'First timer registered successfully!');
    }

    public function show(FirstTimer $firstTimer)
    {
        return response()->json($firstTimer->load(['church', 'pcf', 'bringer', 'attendanceLogs']));
    }

    public function edit(FirstTimer $firstTimer)
    {
        $churchGroups = ChurchGroup::with('churchCategory')->get();
        $bringers = Bringer::all();
        return view('first-timers.edit', compact('firstTimer', 'churchGroups', 'bringers'));
    }

    public function update(UpdateFirstTimerRequest $request, FirstTimer $firstTimer)
    {
        $oldDateOfVisit = $firstTimer->date_of_visit;
        $this->firstTimerService->updateFirstTimer($firstTimer, $request->validated());

        // If date_of_visit changed, move the attendance log to the new date
        if ($request->has('date_of_visit') && $oldDateOfVisit && $firstTimer->date_of_visit) {
            $oldDateStr = $oldDateOfVisit->toDateString();
            $newDateStr = $firstTimer->date_of_visit->toDateString();

            if ($oldDateStr !== $newDateStr) {
                $oldLog = \App\Models\AttendanceLog::where('first_timer_id', $firstTimer->id)
                    ->whereDate('service_date', $oldDateStr)
                    ->first();

                if ($oldLog) {
                    $oldLog->update(['service_date' => $newDateStr]);
                } else {
                    // Create a log for the new date if none existed
                    \App\Models\AttendanceLog::firstOrCreate([
                        'first_timer_id' => $firstTimer->id,
                        'service_date' => $newDateStr,
                    ], ['marked_by' => auth()->id()]);
                }
            }
        }

        if ($request->wantsJson()) {
            return response()->json($firstTimer);
        }

        return redirect()->route('first-timers.index')->with('success', 'First timer updated successfully!');
    }

    public function destroy(FirstTimer $firstTimer)
    {
        $firstTimer->delete();
        return response()->json(null, 204);
    }

    public function checkContact(Request $request)
    {
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_write_close();
        }

        $contact = $request->query('contact');
        $excludeId = $request->query('exclude_id');
        $excludeType = $request->query('exclude_type') ?? 'visitor';

        \Illuminate\Support\Facades\Log::info('FirstTimer Contact Check Start', [
            'contact' => $contact,
            'exclude_id' => $excludeId,
            'exclude_type' => $excludeType,
            'ip' => $request->ip()
        ]);

        $result = $this->contactCheckService->checkDuplicate($contact, $excludeId, $excludeType);

        \Illuminate\Support\Facades\Log::info('FirstTimer Contact Check End', $result);

        return response()->json($result);
    }
}
