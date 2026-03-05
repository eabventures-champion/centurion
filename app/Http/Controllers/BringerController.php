<?php

namespace App\Http\Controllers;

use App\Models\Bringer;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Exports\BringerExport;


class BringerController extends Controller
{
    public function index()
    {
        $data = $this->getBringerData();
        return view('bringers.index', $data);
    }

    protected function getBringerData()
    {
        $user = auth()->user();

        $query = Bringer::with([
            'firstTimers.church.churchGroup.churchCategory',
            'firstTimers.pcf.churchGroup.churchCategory',
            'retainedMembers.church.churchGroup.churchCategory',
            'retainedMembers.pcf.churchGroup.churchCategory'
        ])->withCount(['firstTimers', 'retainedMembers']);

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

        $bringers = $query->latest()->get();

        $ftChurches = $bringers->pluck('firstTimers')->flatten()->pluck('church')->filter();
        $rmChurches = $bringers->pluck('retainedMembers')->flatten()->pluck('church')->filter();
        $availableChurches = $ftChurches->merge($rmChurches)->unique('id')->sortBy('name');

        $ftPcfs = $bringers->pluck('firstTimers')->flatten()->pluck('pcf')->filter();
        $rmPcfs = $bringers->pluck('retainedMembers')->flatten()->pluck('pcf')->filter();
        $availablePcfs = $ftPcfs->merge($rmPcfs)->unique('id')->sortBy('name');

        $groupedBringers = [];

        foreach ($bringers as $bringer) {
            // Get first soul to determine hierarchy (prioritize active, fallback to retained)
            $ft = $bringer->firstTimers->first() ?? $bringer->retainedMembers->first();

            // Total souls calculation for view property injection
            $bringer->total_souls_count = $bringer->first_timers_count + $bringer->retained_members_count;

            $categoryName = 'Unassigned';
            $groupName = 'Unassigned';
            $entityName = 'Unassigned';

            if ($bringer->pcf && $bringer->pcf->churchGroup) {
                $categoryName = strtoupper($bringer->pcf->churchGroup->churchCategory->name ?? 'Unassigned');
                $groupName = strtoupper($bringer->pcf->churchGroup->group_name ?? 'Unassigned');
                $entityName = strtoupper($bringer->pcf->name);
            } elseif ($bringer->church && $bringer->church->churchGroup) {
                $categoryName = strtoupper($bringer->church->churchGroup->churchCategory->name ?? 'Unassigned');
                $groupName = strtoupper($bringer->church->churchGroup->group_name ?? 'Unassigned');
                $entityName = strtoupper($bringer->church->name);
            }

            if (!isset($groupedBringers[$categoryName])) {
                $groupedBringers[$categoryName] = [];
            }
            if (!isset($groupedBringers[$categoryName][$groupName])) {
                $groupedBringers[$categoryName][$groupName] = [];
            }
            if (!isset($groupedBringers[$categoryName][$groupName][$entityName])) {
                $groupedBringers[$categoryName][$groupName][$entityName] = [];
            }

            $groupedBringers[$categoryName][$groupName][$entityName][] = $bringer;
        }

        // Sort categories to ensure ZONAL CHURCH is first
        uksort($groupedBringers, function ($a, $b) {
            if ($a === 'ZONAL CHURCH')
                return -1;
            if ($b === 'ZONAL CHURCH')
                return 1;
            return strcmp($a, $b);
        });

        return [
            'groupedBringers' => $groupedBringers,
            'availableChurches' => $availableChurches,
            'availablePcfs' => $availablePcfs
        ];
    }

    public function downloadExcel()
    {
        $data = $this->getBringerData();
        return Excel::download(new BringerExport($data['groupedBringers']), 'bringers_' . now()->format('Y-m-d') . '.xlsx');
    }

    public function downloadPdf()
    {
        $data = $this->getBringerData();
        $pdf = Pdf::loadView('bringers.pdf', [
            'groupedBringers' => $data['groupedBringers'],
            'title' => 'Bringers Directory'
        ])->setPaper('a4', 'landscape');

        return $pdf->download('bringers_' . now()->format('Y-m-d') . '.pdf');
    }


    /**
     * Check if a bringer exists by contact (AJAX).
     */
    public function check(Request $request)
    {
        $contact = $request->contact;
        $bringer = Bringer::with([
            'firstTimers.church',
            'firstTimers.pcf',
            'retainedMembers.church',
            'retainedMembers.pcf'
        ])->where('contact', $contact)->first();

        if ($bringer) {
            $assignedTo = 'Unassigned';
            if ($bringer->pcf) {
                $assignedTo = "PCF: {$bringer->pcf->name}";
            } elseif ($bringer->church) {
                $assignedTo = "Church: {$bringer->church->name}";
            } else {
                // Fallback to old behavior if columns are still null
                $soul = $bringer->firstTimers->first() ?? $bringer->retainedMembers->first();
                if ($soul) {
                    $assignedTo = $soul->pcf->name ?? $soul->church->name ?? 'Unassigned';
                }
            }

            return response()->json([
                'exists' => true,
                'name' => $bringer->name,
                'id' => $bringer->id,
                'fellowship' => $assignedTo,
                'details' => $bringer,
                'pcf_id' => $bringer->pcf_id,
                'church_id' => $bringer->church_id
            ]);
        }
        return response()->json(['exists' => false]);
    }

    /**
     * Store a new bringer.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'contact' => 'required|string|max:20|unique:bringers,contact',
            'senior_cell_name' => 'nullable|string|max:255',
            'cell_name' => 'nullable|string|max:255',
        ]);

        $bringer = Bringer::create($validated);
        return response()->json($bringer, 201);
    }
}
