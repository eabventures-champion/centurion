<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreChurchRequest;
use App\Models\Church;
use App\Models\LocalAssembly;
use App\Models\ChurchCategory;
use App\Models\ChurchGroup;
use App\Services\ChurchService;
use Illuminate\Http\Request;

class ChurchController extends Controller
{
    protected $churchService;

    public function __construct(ChurchService $churchService)
    {
        $this->churchService = $churchService;
    }

    public function index()
    {
        $churches = Church::with('churchGroup.churchCategory')->get();
        $groupedChurches = $churches->groupBy('church_group_id');
        // Only allow groups from 'Other churches' category for church creation
        $groups = \App\Models\ChurchGroup::whereHas('churchCategory', function ($q) {
            $q->where('name', 'OTHER CHURCHES');
        })->whereNotIn('group_name', ['LAA', 'AVENOR'])->get();

        $assemblies = \App\Models\LocalAssembly::orderBy('name')->get();

        return view('churches.index', compact('churches', 'groupedChurches', 'groups', 'assemblies'));
    }

    public function store(StoreChurchRequest $request)
    {
        try {
            $this->churchService->createChurch($request->validated());
            return redirect()->back()->with('success', 'Church created successfully!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function edit(Church $church)
    {
        $groups = ChurchGroup::whereHas('churchCategory', function ($query) {
            $query->where('name', 'OTHER CHURCHES');
        })->whereNotIn('group_name', ['LAA', 'AVENOR'])->get();
        return view('churches.edit', compact('church', 'groups'));
    }

    public function show(Church $church)
    {
        $church->load('churchGroup.churchCategory', 'pastor');
        return response()->json([
            'church' => $church,
            'pastor' => $church->pastor
        ]);
    }

    public function update(Request $request, Church $church)
    {
        $validated = $request->validate([
            'church_group_id' => 'required|exists:church_groups,id',
            'name' => 'required|string|max:255|exists:local_assemblies,name',
            'title' => 'required|in:Bro,Sis,Pastor,Dcn,Dcns,Mr,Mrs',
            'leader_name' => 'required|string|max:255',
            'leader_contact' => 'required|string|unique:churches,leader_contact,' . $church->id,
            'location' => 'nullable|string|max:255',
        ]);

        $church->update($validated);

        return redirect()->route('churches.index')->with('success', 'Church updated successfully!');
    }

    public function destroy(Church $church)
    {
        $church->delete();
        return redirect()->back()->with('success', 'Church deleted successfully!');
    }
}
