<?php

namespace App\Http\Controllers;

use App\Models\LocalAssembly;
use App\Models\ChurchGroup;
use Illuminate\Http\Request;

class LocalAssemblyController extends Controller
{
    public function index()
    {
        $assemblies = LocalAssembly::with('churchGroup')->orderBy('name')->get();
        $groups = ChurchGroup::whereNotIn('group_name', ['LAA', 'AVENOR'])->orderBy('group_name')->get();
        return view('local-assemblies.index', compact('assemblies', 'groups'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'church_group_id' => 'required|exists:church_groups,id',
            'name' => 'required|string|max:255|unique:local_assemblies,name'
        ]);

        LocalAssembly::create($validated);

        return redirect()->back()->with('success', 'Church created successfully!');
    }

    public function update(Request $request, LocalAssembly $localAssembly)
    {
        $validated = $request->validate([
            'church_group_id' => 'required|exists:church_groups,id',
            'name' => 'required|string|max:255|unique:local_assemblies,name,' . $localAssembly->id
        ]);

        $localAssembly->update($validated);

        return redirect()->back()->with('success', 'Church updated successfully!');
    }

    public function destroy(LocalAssembly $localAssembly)
    {
        $localAssembly->delete();
        return redirect()->back()->with('success', 'Church deleted successfully!');
    }
}
