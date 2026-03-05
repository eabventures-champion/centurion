<?php

namespace App\Http\Controllers;

use App\Models\FirstTimer;
use App\Models\AttendanceLog;
use App\Models\Pcf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AttendanceController extends Controller
{
    /**
     * Display the list of PCFs.
     */
    public function index(Request $request)
    {
        $user = auth()->user();

        $month = $request->query('month', now()->month);
        $year = now()->year;

        // Validation for month (Jan to current)
        if ($month > now()->month || $month < 1) {
            $month = now()->month;
        }

        $churchQuery = \App\Models\Church::withCount([
            'firstTimers' => fn($q) => $q->where('locked', false)
        ])->whereHas('firstTimers', fn($q) => $q->where('locked', false));

        $pcfQuery = Pcf::withCount([
            'firstTimers' => fn($q) => $q->where('locked', false)
        ])->whereHas('firstTimers', fn($q) => $q->where('locked', false));

        if ($user->hasRole('Admin')) {
            $church = $user->church();
            if ($church) {
                $churchQuery->where('id', $church->id);
                $pcfQuery->where('church_group_id', $church->church_group_id);
            } else {
                $churchQuery->whereRaw('1=0');
                $pcfQuery->whereRaw('1=0');
            }
        } elseif ($user->hasRole('Official')) {
            $churchQuery->whereRaw('1=0'); // Officials don't see church attendance
            $pcfQuery->where('official_id', $user->id);
        }

        $churches = $churchQuery->orderBy('name')->get();
        $pcfs = $pcfQuery->orderBy('name')->get();

        return view('attendance.index', compact('churches', 'pcfs', 'month'));
    }

    /**
     * Show attendance for a specific PCF.
     */
    public function showPcfAttendance(Request $request, Pcf $pcf)
    {
        $user = auth()->user();
        $month = (int) $request->query('month', now()->month);
        $year = now()->year;

        if ($month > now()->month || $month < 1) {
            $month = now()->month;
        }

        if ($user->hasRole('Admin')) {
            $adminChurch = $user->church();
            if (!$adminChurch || $pcf->church_group_id !== $adminChurch->church_group_id) {
                abort(403);
            }
        } elseif ($user->hasRole('Official')) {
            if ($pcf->official_id !== $user->id) {
                abort(403);
            }
        }

        $firstTimers = FirstTimer::where('pcf_id', $pcf->id)
            ->where('locked', false)
            ->with([
                'attendanceLogs' => function ($q) use ($month, $year) {
                    $q->whereMonth('service_date', $month)->whereYear('service_date', $year);
                },
                'church'
            ])
            ->get();

        // Compute the true earliest visit date for each first timer (across ALL months)
        $earliestLogDates = AttendanceLog::whereIn('first_timer_id', $firstTimers->pluck('id'))
            ->selectRaw('first_timer_id, MIN(service_date) as earliest_log')
            ->groupBy('first_timer_id')
            ->pluck('earliest_log', 'first_timer_id');

        foreach ($firstTimers as $ft) {
            $candidates = collect();
            if ($ft->date_of_visit)
                $candidates->push(Carbon::parse($ft->date_of_visit)->toDateString());
            if (isset($earliestLogDates[$ft->id]))
                $candidates->push(Carbon::parse($earliestLogDates[$ft->id])->toDateString());
            $ft->global_first_visit = $candidates->sort()->first();
        }

        $dates = self::getServiceDates($month, $year, $pcf, 'PCF', $firstTimers, (array) $request->query('custom_dates', []));

        return view('attendance.pcf-attendance', [
            'entity' => $pcf,
            'type' => 'PCF',
            'firstTimers' => $firstTimers,
            'serviceDates' => $dates,
            'selectedMonth' => $month,
            'customDates' => (array) $request->query('custom_dates', [])
        ]);
    }

    /**
     * Show attendance for a specific Church.
     */
    public function showChurchAttendance(Request $request, \App\Models\Church $church)
    {
        $user = auth()->user();
        $month = (int) $request->query('month', now()->month);
        $year = now()->year;

        if ($month > now()->month || $month < 1) {
            $month = now()->month;
        }

        if ($user->hasRole('Admin')) {
            $adminChurch = $user->church();
            if (!$adminChurch || $church->id !== $adminChurch->id) {
                abort(403);
            }
        } elseif ($user->hasRole('Official')) {
            abort(403);
        }

        $firstTimers = FirstTimer::where('church_id', $church->id)
            ->where('locked', false)
            ->with([
                'attendanceLogs' => function ($q) use ($month, $year) {
                    $q->whereMonth('service_date', $month)->whereYear('service_date', $year);
                }
            ])
            ->get();

        // Compute the true earliest visit date for each first timer (across ALL months)
        $earliestLogDates = AttendanceLog::whereIn('first_timer_id', $firstTimers->pluck('id'))
            ->selectRaw('first_timer_id, MIN(service_date) as earliest_log')
            ->groupBy('first_timer_id')
            ->pluck('earliest_log', 'first_timer_id');

        foreach ($firstTimers as $ft) {
            $candidates = collect();
            if ($ft->date_of_visit)
                $candidates->push(Carbon::parse($ft->date_of_visit)->toDateString());
            if (isset($earliestLogDates[$ft->id]))
                $candidates->push(Carbon::parse($earliestLogDates[$ft->id])->toDateString());
            $ft->global_first_visit = $candidates->sort()->first();
        }

        $dates = self::getServiceDates($month, $year, $church, 'Church', $firstTimers, (array) $request->query('custom_dates', []));

        return view('attendance.pcf-attendance', [
            'entity' => $church,
            'type' => 'Church',
            'firstTimers' => $firstTimers,
            'serviceDates' => $dates,
            'selectedMonth' => $month,
            'customDates' => (array) $request->query('custom_dates', [])
        ]);
    }

    /**
     * Helper to get Sundays and any existing log dates for a month.
     */
    public static function getServiceDates($month, $year, $entity, $type, $firstTimers = null, $customDates = [])
    {
        $dates = collect();
        $date = Carbon::create($year, $month, 1);
        $today = Carbon::today();
        $firstTimers = $firstTimers ?? collect();

        // Get all Sundays (up to today)
        while ($date->month == $month) {
            if ($date->isSunday() && $date->lte($today)) {
                $dates->push($date->copy());
            }
            $date->addDay();
        }

        // Add any other dates that have logs for THIS ENTITY (including retained members)
        if ($entity) {
            $foreignKey = ($type === 'PCF' ? 'pcf_id' : 'church_id');
            $logDates = AttendanceLog::where(function ($q) use ($entity, $foreignKey) {
                $q->whereHas('firstTimer', fn($sq) => $sq->where($foreignKey, $entity->id))
                    ->orWhereHas('retainedMember', fn($sq) => $sq->where($foreignKey, $entity->id));
            })
                ->whereMonth('service_date', $month)
                ->whereYear('service_date', $year)
                ->pluck('service_date')
                ->map(fn($d) => Carbon::parse($d)->startOfDay())
                ->unique(fn($d) => $d->toDateString());

            foreach ($logDates as $ld) {
                if (!$dates->contains(fn($d) => $d->toDateString() === $ld->toDateString())) {
                    $dates->push($ld);
                }
            }
        }

        // Add any first visit dates for souls STILL in first timers
        foreach ($firstTimers as $ft) {
            $visitDate = Carbon::parse($ft->date_of_visit)->startOfDay();
            if ($visitDate->month == $month && $visitDate->year == $year && $visitDate->lte($today)) {
                if (!$dates->contains(fn($d) => $d->toDateString() === $visitDate->toDateString())) {
                    $dates->push($visitDate->copy());
                }
            }
        }

        // Add custom dates if provided and in same month and not future
        foreach ($customDates as $cdStr) {
            if (!$cdStr)
                continue;
            try {
                $cd = Carbon::parse($cdStr)->startOfDay();
                if ($cd->month == $month && $cd->year == $year && $cd->lte($today)) {
                    if (!$dates->contains(fn($d) => $d->toDateString() === $cd->toDateString())) {
                        $dates->push($cd);
                    }
                }
            } catch (\Exception $e) { /* skip invalid dates */
            }
        }

        return $dates->sortBy(fn($d) => $d->timestamp);
    }

    /**
     * Toggle attendance for a first timer on a specific date.
     */
    public function toggle(Request $request)
    {
        $validated = $request->validate([
            'first_timer_id' => 'required|exists:first_timers,id',
            'service_date' => 'required|date',
        ]);

        $firstTimer = FirstTimer::findOrFail($validated['first_timer_id']);

        // Check if already retained/locked and we are trying to ADD attendance
        $isExistingLog = AttendanceLog::where('first_timer_id', $firstTimer->id)
            ->whereDate('service_date', $validated['service_date'])
            ->exists();

        if ($firstTimer->service_count >= 3 && !$isExistingLog) {
            return response()->json(['error' => 'Member has already reached the attendance limit (3/3).'], 422);
        }

        // Determine the true earliest visit date (registration date + all attendance logs)
        $earliestLog = AttendanceLog::where('first_timer_id', $firstTimer->id)->min('service_date');
        $candidates = collect();
        if ($firstTimer->date_of_visit)
            $candidates->push(Carbon::parse($firstTimer->date_of_visit)->toDateString());
        if ($earliestLog)
            $candidates->push(Carbon::parse($earliestLog)->toDateString());
        $earliestVisitDate = $candidates->sort()->first();

        // Cannot toggle the true initial (earliest) visit
        if ($earliestVisitDate === $validated['service_date']) {
            return response()->json(['error' => 'Initial visit cannot be untoggled.'], 422);
        }

        // Prevent future dates
        if (Carbon::parse($validated['service_date'])->isFuture()) {
            return response()->json(['error' => 'Cannot mark attendance for future dates.'], 422);
        }

        $log = AttendanceLog::where('first_timer_id', $firstTimer->id)
            ->whereDate('service_date', $validated['service_date'])
            ->first();

        $migrated = false;
        $requiresConfirmation = false;
        DB::transaction(function () use ($firstTimer, $log, $validated, &$migrated, &$requiresConfirmation, $request) {
            // If this is a confirmation request, ONLY handle migration
            if ($request->boolean('confirmed')) {
                if ($firstTimer->service_count >= 3 && !$firstTimer->locked) {
                    $firstTimer->setAttribute('locked', true);
                    $firstTimer->saveQuietly();
                    \App\Jobs\MoveToRetainedMembers::dispatchSync($firstTimer);
                    $migrated = true;
                }
                return;
            }

            // Normal toggle logic
            if ($log) {
                $log->delete();
                $firstTimer->decrement('service_count');

                // Explicitly set locked to false if service count drops below 3
                if ($firstTimer->service_count < 3 && $firstTimer->locked) {
                    $firstTimer->setAttribute('locked', false);
                    $firstTimer->saveQuietly();
                }
            } else {
                AttendanceLog::create([
                    'first_timer_id' => $firstTimer->id,
                    'service_date' => $validated['service_date'],
                    'marked_by' => auth()->id(),
                ]);
                $firstTimer->increment('service_count');

                // Check for migration threshold
                if ($firstTimer->service_count >= 3 && !$firstTimer->locked) {
                    $requiresConfirmation = true;
                }
            }
        });

        return response()->json([
            'success' => true,
            'count' => $migrated ? 3 : ($firstTimer->fresh()?->service_count ?? 0),
            'status' => $log ? 'removed' : 'added',
            'migrated' => $migrated,
            'requires_confirmation' => $requiresConfirmation,
            'message' => $migrated ? 'Member has reached 3 visits and has been migrated to Retained Members.' : null
        ]);
    }
}
