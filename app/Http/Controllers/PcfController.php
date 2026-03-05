<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePcfRequest;
use App\Models\Pcf;
use App\Models\ChurchGroup;
use App\Models\ChurchCategory;
use App\Models\User;
use App\Services\ChurchService;
use Illuminate\Http\Request;

class PcfController extends Controller
{
    protected $churchService;

    public function __construct(ChurchService $churchService)
    {
        $this->churchService = $churchService;
    }

    public function index()
    {
        $pcfs = Pcf::with('churchGroup.churchCategory')->get();
        $groupedPcfs = $pcfs->groupBy('church_group_id');
        // Only allow groups from 'Zonal church' category for PCF creation
        $groups = ChurchGroup::whereHas('churchCategory', function ($q) {
            $q->where('name', 'ZONAL CHURCH');
        })->get();

        $officials = User::role('Official')->get();
        $defaultOfficialId = User::role('Official')->where('is_default', true)->value('id');

        return view('pcfs.index', compact('pcfs', 'groupedPcfs', 'groups', 'officials', 'defaultOfficialId'));
    }

    public function store(StorePcfRequest $request)
    {
        try {
            $this->churchService->createPcf($request->validated());
            return redirect()->back()->with('success', 'PCF created successfully!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function edit(Pcf $pcf)
    {
        $groups = ChurchGroup::whereHas('churchCategory', function ($query) {
            $query->where('name', 'ZONAL CHURCH');
        })->get();
        $officials = User::role('Official')->get();
        $defaultOfficialId = User::role('Official')->where('is_default', true)->value('id');
        return view('pcfs.edit', compact('pcf', 'groups', 'officials', 'defaultOfficialId'));
    }

    public function update(Request $request, Pcf $pcf)
    {
        $validated = $request->validate([
            'church_group_id' => 'required|exists:church_groups,id',
            'name' => 'required|string|max:255',
            'leader_name' => 'required|string|max:255',
            'leader_contact' => 'required|string|unique:pcfs,leader_contact,' . $pcf->id,
            'official_id' => 'required|exists:users,id',
            'gender' => 'required|in:Male,Female',
            'marital_status' => 'required|in:Single,Married,Divorced,Widowed',
            'occupation' => 'required|string|max:255',
        ]);

        $pcf->update($validated);

        return redirect()->route('pcfs.index')->with('success', 'PCF updated successfully!');
    }

    public function show(Pcf $pcf)
    {
        return response()->json($pcf->load(['churchGroup.churchCategory', 'official']));
    }

    public function destroy(Pcf $pcf)
    {
        $pcf->delete();
        return redirect()->back()->with('success', 'PCF deleted successfully!');
    }
}
