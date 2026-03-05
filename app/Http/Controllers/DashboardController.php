<?php

namespace App\Http\Controllers;

use App\Models\FirstTimer;
use App\Models\RetainedMember;
use App\Models\Church;
use App\Models\ChurchGroup;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class DashboardController extends Controller
{
    /**
     * Handle the dashboard display based on roles.
     */
    public function __invoke(Request $request)
    {
        Log::info('Dashboard accessed.', [
            'user_id' => auth()->id(),
            'show_pastor_welcome' => session('show_pastor_welcome')
        ]);
        $user = auth()->user();

        // Initialize base stats structure to avoid undefined keys in view
        $stats = [
            'total_churches' => 0,
            'total_first_timers' => 0,
            'new_first_timers' => 0,
            'total_retained' => 0,
            'total_categories' => 0,
            'total_groups' => 0,
            'total_pcfs' => 0,
            'total_officials' => 0,
            'gender_dist' => [
                'male' => 0,
                'female' => 0,
                'male_percent' => 0,
                'female_percent' => 0,
            ],
            'birthday_reminders' => collect(),
            'recent_registrations' => collect(),
            'my_church_first_timers' => 0,
            'my_church_retained' => 0,
            'recent_visitors' => collect(),
        ];

        if ($user->hasRole('Super Admin')) {
            $firstTimers = FirstTimer::all();
            $retained = RetainedMember::all();

            $stats['total_first_timers'] = $firstTimers->count() + $retained->count();
            $stats['new_first_timers'] = FirstTimer::whereMonth('created_at', now()->month)->whereYear('created_at', now()->year)->count();
            $stats['total_retained'] = $retained->count();

            // Calculate Bringer Performance Tiers
            $bringers = \App\Models\Bringer::with([
                'firstTimers.church',
                'firstTimers.pcf',
                'retainedMembers.church',
                'retainedMembers.pcf'
            ])->withCount(['firstTimers', 'retainedMembers'])->get();
            $tiers = ['offerers' => [], 'tithers' => [], 'jubilee' => [], 'centurion' => [], 'millennial' => []];
            foreach ($bringers as $b) {
                $souls = $b->first_timers_count + $b->retained_members_count;
                $b->retention_percentage = $souls > 0 ? round(($b->retained_members_count / $souls) * 100, 1) : 0;

                // Determine Fellowship
                $soul = $b->firstTimers->first() ?? $b->retainedMembers->first();
                $b->fellowship_name = $soul ? ($soul->pcf->name ?? $soul->church->name ?? 'Unassigned') : 'Unassigned';

                if ($souls >= 1 && $souls <= 9)
                    $tiers['offerers'][] = $b;
                elseif ($souls >= 10 && $souls <= 49)
                    $tiers['tithers'][] = $b;
                elseif ($souls >= 50 && $souls <= 99)
                    $tiers['jubilee'][] = $b;
                elseif ($souls >= 100 && $souls <= 999)
                    $tiers['centurion'][] = $b;
                elseif ($souls >= 1000)
                    $tiers['millennial'][] = $b;
            }
            // Sort each tier by total souls descending
            $sortDesc = fn($a, $b) => ($b->first_timers_count + $b->retained_members_count) <=> ($a->first_timers_count + $a->retained_members_count);
            foreach ($tiers as &$tierList) {
                usort($tierList, $sortDesc);
            }
            unset($tierList);
            $stats['tiers'] = [
                'offerers' => ['count' => count($tiers['offerers']), 'bringers' => $tiers['offerers']],
                'tithers' => ['count' => count($tiers['tithers']), 'bringers' => $tiers['tithers']],
                'jubilee' => ['count' => count($tiers['jubilee']), 'bringers' => $tiers['jubilee']],
                'centurion' => ['count' => count($tiers['centurion']), 'bringers' => $tiers['centurion']],
                'millennial' => ['count' => count($tiers['millennial']), 'bringers' => $tiers['millennial']],
            ];

            // Gender Distribution Split
            $zonalCategory = \App\Models\ChurchCategory::where('name', 'ZONAL CHURCH')->first();
            $zonalGroupIds = $zonalCategory ? $zonalCategory->churchGroups->pluck('id') : collect();
            $zonalPcfIds = \App\Models\Pcf::whereIn('church_group_id', $zonalGroupIds)->pluck('id');
            $zonalChurchIds = \App\Models\Church::whereIn('church_group_id', $zonalGroupIds)->pluck('id');

            $otherCategory = \App\Models\ChurchCategory::where('name', 'OTHER CHURCHES')->first();
            $otherGroupIds = $otherCategory ? $otherCategory->churchGroups->pluck('id') : collect();
            $otherPcfIds = \App\Models\Pcf::whereIn('church_group_id', $otherGroupIds)->pluck('id');
            $otherChurchIds = \App\Models\Church::whereIn('church_group_id', $otherGroupIds)->pluck('id');

            $getGenderInfo = function ($pcfIds, $churchIds) {
                $ft = FirstTimer::whereIn('pcf_id', $pcfIds)->orWhereIn('church_id', $churchIds)->get();
                $rm = RetainedMember::whereIn('pcf_id', $pcfIds)->orWhereIn('church_id', $churchIds)->get();
                $m = $ft->where('gender', 'Male')->count() + $rm->where('gender', 'Male')->count();
                $f = $ft->where('gender', 'Female')->count() + $rm->where('gender', 'Female')->count();
                $t = $m + $f ?: 1;
                return [
                    'male' => $m,
                    'female' => $f,
                    'total' => $m + $f,
                    'male_percent' => round(($m / $t) * 100, 1),
                    'female_percent' => round(($f / $t) * 100, 1),
                ];
            };

            $stats['gender_dist_main'] = $getGenderInfo($zonalPcfIds, $zonalChurchIds);
            $stats['gender_dist_other'] = $getGenderInfo($otherPcfIds, $otherChurchIds);
            $stats['is_super_admin'] = true;

            // Birthday reminders: this month + next 15 days
            $birthdayDates = collect();
            $today = now();
            $endDate = now()->addDays(15);
            $cursor = $today->copy()->startOfMonth();
            while ($cursor->lte($endDate)) {
                $birthdayDates->push($cursor->format('d-m'));
                $cursor->addDay();
            }

            $stats['birthday_reminders'] = FirstTimer::with(['church', 'pcf'])
                ->whereIn('date_of_birth', $birthdayDates->toArray())
                ->get()
                ->merge(RetainedMember::with(['church', 'pcf'])->whereIn('date_of_birth', $birthdayDates->toArray())->get())
                ->groupBy(function ($item) {
                    return $item->church->name ?? $item->pcf->name ?? 'Unassigned';
                });

            $stats['recent_registrations'] = FirstTimer::with('church')->latest()->take(5)->get();

        } elseif ($user->hasRole('Admin')) {
            $church = $user->church();

            if ($church) {
                // Get PCF IDs under the same church group as the admin's church
                $pcfIds = \App\Models\Pcf::where('church_group_id', $church->church_group_id)->pluck('id');

                $firstTimers = FirstTimer::where(function ($q) use ($church, $pcfIds) {
                    $q->where('church_id', $church->id)
                        ->orWhereIn('pcf_id', $pcfIds);
                })->get();

                $retained = RetainedMember::where(function ($q) use ($church, $pcfIds) {
                    $q->where('church_id', $church->id)
                        ->orWhereIn('pcf_id', $pcfIds);
                })->get();

                $maleCount = $firstTimers->where('gender', 'Male')->count() + $retained->where('gender', 'Male')->count();
                $femaleCount = $firstTimers->where('gender', 'Female')->count() + $retained->where('gender', 'Female')->count();
                $totalMembers = $maleCount + $femaleCount ?: 1;

                $stats['total_first_timers'] = $firstTimers->count() + $retained->count();
                $stats['new_first_timers'] = FirstTimer::where(function ($q) use ($church, $pcfIds) {
                    $q->where('church_id', $church->id)
                        ->orWhereIn('pcf_id', $pcfIds);
                })->whereMonth('created_at', now()->month)->whereYear('created_at', now()->year)->count();
                $stats['total_retained'] = $retained->count();

                // Calculate Bringer Performance Tiers for Admin (Scoped)
                $bringers = \App\Models\Bringer::with([
                    'firstTimers.church',
                    'firstTimers.pcf',
                    'retainedMembers.church',
                    'retainedMembers.pcf'
                ])->withCount([
                            'firstTimers' => function ($q) use ($church, $pcfIds) {
                                $q->where('church_id', $church->id)->orWhereIn('pcf_id', $pcfIds);
                            },
                            'retainedMembers' => function ($q) use ($church, $pcfIds) {
                                $q->where('church_id', $church->id)->orWhereIn('pcf_id', $pcfIds);
                            }
                        ])->get();
                $tiers = ['offerers' => [], 'tithers' => [], 'jubilee' => [], 'centurion' => [], 'millennial' => []];
                foreach ($bringers as $b) {
                    $souls = $b->first_timers_count + $b->retained_members_count;
                    $b->retention_percentage = $souls > 0 ? round(($b->retained_members_count / $souls) * 100, 1) : 0;

                    // Determine Fellowship
                    $soul = $b->firstTimers->first() ?? $b->retainedMembers->first();
                    $b->fellowship_name = $soul ? ($soul->pcf->name ?? $soul->church->name ?? 'Unassigned') : 'Unassigned';

                    if ($souls >= 1 && $souls <= 9)
                        $tiers['offerers'][] = $b;
                    elseif ($souls >= 10 && $souls <= 49)
                        $tiers['tithers'][] = $b;
                    elseif ($souls >= 50 && $souls <= 99)
                        $tiers['jubilee'][] = $b;
                    elseif ($souls >= 100 && $souls <= 999)
                        $tiers['centurion'][] = $b;
                    elseif ($souls >= 1000)
                        $tiers['millennial'][] = $b;
                }
                // Sort each tier by total souls descending
                $sortDesc = fn($a, $b) => ($b->first_timers_count + $b->retained_members_count) <=> ($a->first_timers_count + $a->retained_members_count);
                foreach ($tiers as &$tierList) {
                    usort($tierList, $sortDesc);
                }
                unset($tierList);
                $stats['tiers'] = [
                    'offerers' => ['count' => count($tiers['offerers']), 'bringers' => $tiers['offerers']],
                    'tithers' => ['count' => count($tiers['tithers']), 'bringers' => $tiers['tithers']],
                    'jubilee' => ['count' => count($tiers['jubilee']), 'bringers' => $tiers['jubilee']],
                    'centurion' => ['count' => count($tiers['centurion']), 'bringers' => $tiers['centurion']],
                    'millennial' => ['count' => count($tiers['millennial']), 'bringers' => $tiers['millennial']],
                ];

                $stats['gender_dist'] = [
                    'male' => $maleCount,
                    'female' => $femaleCount,
                    'male_percent' => round(($maleCount / $totalMembers) * 100, 1),
                    'female_percent' => round(($femaleCount / $totalMembers) * 100, 1),
                ];

                $stats['church_name'] = $church->name;

                // Birthday reminders: this month + next 15 days
                $birthdayDates = collect();
                $today = now();
                $endDate = now()->addDays(15);
                $cursor = $today->copy()->startOfMonth();
                while ($cursor->lte($endDate)) {
                    $birthdayDates->push($cursor->format('d-m'));
                    $cursor->addDay();
                }

                $stats['birthday_reminders'] = FirstTimer::with(['church', 'pcf'])->where(function ($q) use ($church, $pcfIds) {
                    $q->where('church_id', $church->id)
                        ->orWhereIn('pcf_id', $pcfIds);
                })->whereIn('date_of_birth', $birthdayDates->toArray())
                    ->get()
                    ->merge(RetainedMember::with('church')->where('church_id', $church->id)->whereIn('date_of_birth', $birthdayDates->toArray())->get())
                    ->groupBy(function ($item) {
                        return $item->church->name ?? $item->pcf->name ?? 'Unassigned';
                    });

                $stats['recent_registrations'] = FirstTimer::with(['church', 'pcf'])->where(function ($q) use ($church, $pcfIds) {
                    $q->where('church_id', $church->id)
                        ->orWhereIn('pcf_id', $pcfIds);
                })->latest()->take(5)->get();
            }
        } elseif ($user->hasRole('Official')) {
            // Get PCF IDs assigned to this official
            $pcfIds = $user->pcfs()->pluck('id');
            $stats['total_pcfs'] = $pcfIds->count();

            $firstTimers = FirstTimer::whereIn('pcf_id', $pcfIds)->get();
            $retained = RetainedMember::whereIn('pcf_id', $pcfIds)->get();

            $stats['total_first_timers'] = $firstTimers->count() + $retained->count();
            $stats['new_first_timers'] = FirstTimer::whereIn('pcf_id', $pcfIds)
                ->whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->count();
            $stats['total_retained'] = $retained->count();

            // Calculate Bringer Performance Tiers (scoped to official's PCFs)
            $bringers = \App\Models\Bringer::with([
                'firstTimers.church',
                'firstTimers.pcf',
                'retainedMembers.church',
                'retainedMembers.pcf'
            ])->withCount([
                        'firstTimers' => function ($q) use ($pcfIds) {
                            $q->whereIn('pcf_id', $pcfIds);
                        },
                        'retainedMembers' => function ($q) use ($pcfIds) {
                            $q->whereIn('pcf_id', $pcfIds);
                        }
                    ])->get();

            $tiers = ['offerers' => [], 'tithers' => [], 'jubilee' => [], 'centurion' => [], 'millennial' => []];
            foreach ($bringers as $b) {
                $souls = $b->first_timers_count + $b->retained_members_count;
                if ($souls === 0)
                    continue;

                $b->retention_percentage = $souls > 0 ? round(($b->retained_members_count / $souls) * 100, 1) : 0;

                // Determine Fellowship
                $soul = $b->firstTimers->first() ?? $b->retainedMembers->first();
                $b->fellowship_name = $soul ? ($soul->pcf->name ?? $soul->church->name ?? 'Unassigned') : 'Unassigned';

                if ($souls >= 1 && $souls <= 9)
                    $tiers['offerers'][] = $b;
                elseif ($souls >= 10 && $souls <= 49)
                    $tiers['tithers'][] = $b;
                elseif ($souls >= 50 && $souls <= 99)
                    $tiers['jubilee'][] = $b;
                elseif ($souls >= 100 && $souls <= 999)
                    $tiers['centurion'][] = $b;
                elseif ($souls >= 1000)
                    $tiers['millennial'][] = $b;
            }

            $sortDesc = fn($a, $b) => ($b->first_timers_count + $b->retained_members_count) <=> ($a->first_timers_count + $a->retained_members_count);
            foreach ($tiers as &$tierList) {
                usort($tierList, $sortDesc);
            }
            unset($tierList);

            $stats['tiers'] = [
                'offerers' => ['count' => count($tiers['offerers']), 'bringers' => $tiers['offerers']],
                'tithers' => ['count' => count($tiers['tithers']), 'bringers' => $tiers['tithers']],
                'jubilee' => ['count' => count($tiers['jubilee']), 'bringers' => $tiers['jubilee']],
                'centurion' => ['count' => count($tiers['centurion']), 'bringers' => $tiers['centurion']],
                'millennial' => ['count' => count($tiers['millennial']), 'bringers' => $tiers['millennial']],
            ];

            // Gender Distribution
            $maleCount = $firstTimers->where('gender', 'Male')->count() + $retained->where('gender', 'Male')->count();
            $femaleCount = $firstTimers->where('gender', 'Female')->count() + $retained->where('gender', 'Female')->count();
            $totalMembers = $maleCount + $femaleCount ?: 1;

            $stats['gender_dist'] = [
                'male' => $maleCount,
                'female' => $femaleCount,
                'male_percent' => round(($maleCount / $totalMembers) * 100, 1),
                'female_percent' => round(($femaleCount / $totalMembers) * 100, 1),
            ];

            // Birthday reminders: this month + next 15 days
            $birthdayDates = collect();
            $today = now();
            $endDate = now()->addDays(15);
            $cursor = $today->copy()->startOfMonth();
            while ($cursor->lte($endDate)) {
                $birthdayDates->push($cursor->format('d-m'));
                $cursor->addDay();
            }

            $stats['birthday_reminders'] = FirstTimer::with(['church', 'pcf'])
                ->whereIn('pcf_id', $pcfIds)
                ->whereIn('date_of_birth', $birthdayDates->toArray())
                ->get()
                ->merge(
                    RetainedMember::with(['church', 'pcf'])
                        ->whereIn('pcf_id', $pcfIds)
                        ->whereIn('date_of_birth', $birthdayDates->toArray())
                        ->get()
                )
                ->groupBy(function ($item) {
                    return $item->pcf->name ?? $item->church->name ?? 'Unassigned';
                });

            $stats['recent_registrations'] = FirstTimer::with(['church', 'pcf'])
                ->whereIn('pcf_id', $pcfIds)
                ->latest()
                ->take(5)
                ->get();
        }

        $homepageSettings = \App\Models\HomepageSetting::first();

        return view('dashboard', compact('stats', 'homepageSettings'));
    }
}
