<?php

namespace App\Http\Controllers;

use App\Models\FirstTimer;
use App\Models\RetainedMember;
use App\Models\Pcf;
use App\Models\Church;
use App\Models\ChurchGroup;
use App\Models\ChurchCategory;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Exports\PerformanceExport;

class PerformanceController extends Controller
{
    public function index(Request $request)
    {
        $data = $this->getPerformanceData($request);

        // Sort chartData by retention rate for the leaderboard effect
        usort($data['chartData'], fn($a, $b) => $b['retention_rate'] <=> $a['retention_rate']);

        $labels = $data['labels'];
        $chartData = $data['chartData'];
        $hierarchy = $data['hierarchy'];
        $entityType = $data['entityType'];
        $type = $data['type'];

        return view('performance.index', compact('chartData', 'labels', 'hierarchy', 'entityType', 'type'));
    }

    public function downloadExcel(Request $request)
    {
        $data = $this->getPerformanceData($request);
        $filename = 'performance_report_' . strtolower(str_replace(' ', '_', $data['entityType'])) . '_' . now()->format('Y_m_d') . '.xlsx';

        return Excel::download(new PerformanceExport($data), $filename);
    }

    public function downloadPdf(Request $request)
    {
        $data = $this->getPerformanceData($request);
        $pdf = Pdf::loadView('performance.pdf', $data);
        $filename = 'performance_report_' . strtolower(str_replace(' ', '_', $data['entityType'])) . '_' . now()->format('Y_m_d') . '.pdf';

        return $pdf->download($filename);
    }

    private function getPerformanceData(Request $request)
    {
        $user = auth()->user();
        $type = $request->get('type', 'pcf'); // Defaults to pcf
        $currentMonth = now()->format('Y-m');

        // Get data for the last 6 months for the chart
        $months = [];
        for ($i = 5; $i >= 0; $i--) {
            $months[] = now()->subMonths($i)->format('Y-m');
        }

        $chartData = [];
        $hierarchy = [];
        $entityType = ($type === 'pcf') ? 'PCF' : 'Church';
        $foreignKey = ($type === 'church') ? 'church_id' : 'pcf_id';

        if ($user->hasRole('Official')) {
            $pcfs = $user->pcfs()->with('churchGroup')->get();
            $grouped = $pcfs->groupBy('church_group_id');

            $allGroups = [];
            $totalFt = 0;
            $totalRm = 0;

            foreach ($grouped as $groupId => $items) {
                $group = ChurchGroup::find($groupId);
                $groupData = [
                    'name' => $group ? $group->group_name : 'Unknown Group',
                    'entities' => [],
                    'total_ft' => 0,
                    'total_rm' => 0,
                ];

                foreach ($items as $pcf) {
                    $stats = $this->getEntityStats($pcf, $foreignKey, $currentMonth);
                    $groupData['entities'][] = $stats;
                    $groupData['total_ft'] += $stats['total_ft'];
                    $groupData['total_rm'] += $stats['total_rm'];

                    $chartData[] = [
                        'name' => $pcf->name,
                        'ft_data' => $this->getMonthlyData(FirstTimer::class, $foreignKey, $pcf->id, $months),
                        'rm_data' => $this->getMonthlyData(RetainedMember::class, $foreignKey, $pcf->id, $months),
                        'retention_rate' => $stats['retention_rate']
                    ];
                }

                $totalSouls = $groupData['total_ft'] + $groupData['total_rm'];
                $groupData['avg_retention'] = $totalSouls > 0 ? round(($groupData['total_rm'] / $totalSouls) * 100, 1) : 0;

                $allGroups[] = $groupData;
                $totalFt += $groupData['total_ft'];
                $totalRm += $groupData['total_rm'];
            }

            $totalSouls = $totalFt + $totalRm;
            $hierarchy[] = [
                'name' => 'My Performance',
                'groups' => $allGroups,
                'avg_retention' => $totalSouls > 0 ? round(($totalRm / $totalSouls) * 100, 1) : 0
            ];
            $entityType = 'My PCF';
        } elseif ($user->hasRole('Admin')) {
            // Admins see churches they lead
            $churches = Church::where('leader_contact', $user->contact)->with('churchGroup')->get();
            $grouped = $churches->groupBy('church_group_id');

            $allGroups = [];
            $totalFt = 0;
            $totalRm = 0;

            foreach ($grouped as $groupId => $items) {
                $group = ChurchGroup::find($groupId);
                $groupData = [
                    'name' => $group ? $group->group_name : 'Unknown Group',
                    'entities' => [],
                    'total_ft' => 0,
                    'total_rm' => 0,
                ];

                foreach ($items as $church) {
                    $stats = $this->getEntityStats($church, 'church_id', $currentMonth);
                    $groupData['entities'][] = $stats;
                    $groupData['total_ft'] += $stats['total_ft'];
                    $groupData['total_rm'] += $stats['total_rm'];

                    $chartData[] = [
                        'name' => $church->name,
                        'ft_data' => $this->getMonthlyData(FirstTimer::class, 'church_id', $church->id, $months),
                        'rm_data' => $this->getMonthlyData(RetainedMember::class, 'church_id', $church->id, $months),
                        'retention_rate' => $stats['retention_rate']
                    ];
                }

                $totalSouls = $groupData['total_ft'] + $groupData['total_rm'];
                $groupData['avg_retention'] = $totalSouls > 0 ? round(($groupData['total_rm'] / $totalSouls) * 100, 1) : 0;

                $allGroups[] = $groupData;
                $totalFt += $groupData['total_ft'];
                $totalRm += $groupData['total_rm'];
            }

            $totalSouls = $totalFt + $totalRm;
            $hierarchy[] = [
                'name' => 'My Performance',
                'groups' => $allGroups,
                'avg_retention' => $totalSouls > 0 ? round(($totalRm / $totalSouls) * 100, 1) : 0
            ];
            $entityType = 'My Church';
        } elseif ($user->hasRole('Super Admin')) {
            $categories = ChurchCategory::with([
                'churchGroups' => function ($q) use ($type) {
                    $q->with($type === 'church' ? 'churches' : 'pcfs');
                }
            ])->get()->sort(function ($a, $b) {
                if ($a->name === 'ZONAL CHURCH')
                    return -1;
                if ($b->name === 'ZONAL CHURCH')
                    return 1;
                return strcmp($a->name, $b->name);
            });

            foreach ($categories as $category) {
                $catData = [
                    'name' => $category->name,
                    'groups' => [],
                    'total_ft' => 0,
                    'total_rm' => 0,
                ];

                foreach ($category->churchGroups as $group) {
                    $groupData = [
                        'name' => $group->group_name,
                        'entities' => [],
                        'total_ft' => 0,
                        'total_rm' => 0,
                    ];

                    $entities = ($type === 'church') ? $group->churches : $group->pcfs;

                    foreach ($entities as $entity) {
                        $stats = $this->getEntityStats($entity, $foreignKey, $currentMonth);
                        $groupData['entities'][] = $stats;
                        $groupData['total_ft'] += $stats['total_ft'];
                        $groupData['total_rm'] += $stats['total_rm'];

                        $chartData[] = [
                            'name' => $entity->name,
                            'ft_data' => $this->getMonthlyData(FirstTimer::class, $foreignKey, $entity->id, $months),
                            'rm_data' => $this->getMonthlyData(RetainedMember::class, $foreignKey, $entity->id, $months),
                            'retention_rate' => $stats['retention_rate']
                        ];
                    }

                    if (!empty($groupData['entities'])) {
                        $totalSouls = $groupData['total_ft'] + $groupData['total_rm'];
                        $groupData['avg_retention'] = $totalSouls > 0 ? round(($groupData['total_rm'] / $totalSouls) * 100, 1) : 0;

                        $catData['groups'][] = $groupData;
                        $catData['total_ft'] += $groupData['total_ft'];
                        $catData['total_rm'] += $groupData['total_rm'];
                    }
                }

                if (!empty($catData['groups'])) {
                    $totalSouls = $catData['total_ft'] + $catData['total_rm'];
                    $catData['avg_retention'] = $totalSouls > 0 ? round(($catData['total_rm'] / $totalSouls) * 100, 1) : 0;
                    $hierarchy[] = $catData;
                }
            }
        }

        $labels = array_map(fn($m) => Carbon::createFromFormat('Y-m', $m)->format('M Y'), $months);

        return compact('chartData', 'labels', 'hierarchy', 'entityType', 'type');
    }

    private function getEntityStats($entity, $foreignKey, $currentMonth)
    {
        $newFT = FirstTimer::where($foreignKey, $entity->id)
            ->where('created_at', 'like', $currentMonth . '%')
            ->count();
        $totalRM = RetainedMember::where($foreignKey, $entity->id)->count();

        // Count unique bringers from both FirstTimer and RetainedMember
        $ftBringers = FirstTimer::where($foreignKey, $entity->id)
            ->whereNotNull('bringer_id')
            ->pluck('bringer_id');
        $rmBringers = RetainedMember::where($foreignKey, $entity->id)
            ->whereNotNull('bringer_id')
            ->pluck('bringer_id');

        $totalBringers = $ftBringers->concat($rmBringers)->unique()->count();

        // As per user request: Total FT = NEW FT + MEMBERS
        $totalFT = $newFT + $totalRM;

        // Determine Officer Name with Title
        $officerName = 'N/A';
        if ($entity instanceof \App\Models\Pcf) {
            $official = $entity->official;
            if ($official) {
                $title = $official->title ? $official->title . ' ' : '';
                $officerName = $title . $official->name;
            } else {
                $officerName = $entity->leader_name ?? 'N/A';
            }
        } else {
            // It's a Church
            $pastor = $entity->pastor; // Using the pastor() relationship
            if ($pastor) {
                $title = $pastor->title ? $pastor->title . ' ' : '';
                $officerName = $title . $pastor->name;
            } else {
                $title = $entity->title ? $entity->title . ' ' : '';
                $officerName = $title . ($entity->leader_name ?? 'N/A');
            }
        }

        return [
            'name' => $entity->name,
            'officer' => $officerName,
            'total_ft' => $totalFT,
            'new_ft' => $newFT,
            'bringers' => $totalBringers,
            'total_rm' => $totalRM,
            'retention_rate' => $totalFT > 0 ? round(($totalRM / $totalFT) * 100, 1) : 0
        ];
    }

    private function getMonthlyData($model, $foreignKey, $id, $months)
    {
        $data = [];
        $dateField = ($model === RetainedMember::class) ? 'retained_date' : 'created_at';
        foreach ($months as $month) {
            $data[] = $model::where($foreignKey, $id)
                ->where($dateField, 'like', $month . '%')
                ->count();
        }
        return $data;
    }
}
