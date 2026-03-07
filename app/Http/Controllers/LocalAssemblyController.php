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

    public function checkName(Request $request)
    {
        $name = $request->query('name');
        if (!$name) {
            return response()->json(['exists' => false]);
        }

        $exists = LocalAssembly::where('name', $name)->whereNull('deleted_at')->exists();
        return response()->json(['exists' => $exists]);
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'church_group_id' => 'required|exists:church_groups,id',
                'name' => [
                    'required',
                    'string',
                    'max:255',
                    \Illuminate\Validation\Rule::unique('local_assemblies')->whereNull('deleted_at')
                ]
            ]);

            LocalAssembly::create($validated);

            return redirect()->back()->with('success', 'Church created successfully!');
        } catch (\Illuminate\Validation\ValidationException $e) {
            throw $e;
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error creating church: ' . $e->getMessage())->withInput();
        }
    }

    public function update(Request $request, LocalAssembly $localAssembly)
    {
        try {
            $validated = $request->validate([
                'church_group_id' => 'required|exists:church_groups,id',
                'name' => [
                    'required',
                    'string',
                    'max:255',
                    \Illuminate\Validation\Rule::unique('local_assemblies')->ignore($localAssembly->id)->whereNull('deleted_at')
                ]
            ]);

            $localAssembly->update($validated);

            return redirect()->back()->with('success', 'Church updated successfully!');
        } catch (\Illuminate\Validation\ValidationException $e) {
            throw $e;
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error updating church: ' . $e->getMessage())->withInput();
        }
    }

    public function destroy(LocalAssembly $localAssembly)
    {
        $localAssembly->delete();
        return redirect()->back()->with('success', 'Church deleted successfully!');
    }
}
