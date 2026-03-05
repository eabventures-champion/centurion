<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class OfficialController extends Controller
{
    public function index()
    {
        $officials = User::role('Official')->with('pcfs')->get();
        $pcfs = \App\Models\Pcf::orderBy('name')->get();
        return view('officials.index', compact('officials', 'pcfs'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email',
            'password' => 'required|string|min:6',
            'is_default' => 'nullable|boolean',
            'pcf_ids' => 'nullable|array',
            'pcf_ids.*' => 'exists:pcfs,id',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        $user->assignRole('Official');

        if (!empty($validated['pcf_ids'])) {
            \App\Models\Pcf::whereIn('id', $validated['pcf_ids'])->update(['official_id' => $user->id]);
        }

        // If marked as default, unset all others first
        if ($request->has('is_default') && $request->is_default) {
            User::where('is_default', true)->update(['is_default' => false]);
            $user->update(['is_default' => true]);
        }

        return redirect()->back()->with('success', 'Official created successfully!');
    }

    public function update(Request $request, User $official)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $official->id,
            'password' => 'nullable|string|min:6',
            'is_default' => 'nullable|boolean',
            'pcf_ids' => 'nullable|array',
            'pcf_ids.*' => 'exists:pcfs,id',
        ]);

        $official->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
        ]);

        if (!empty($validated['password'])) {
            $official->update(['password' => Hash::make($validated['password'])]);
        }

        // Sync PCFs: Clear existing then set new
        \App\Models\Pcf::where('official_id', $official->id)->update(['official_id' => null]);
        if (!empty($validated['pcf_ids'])) {
            \App\Models\Pcf::whereIn('id', $validated['pcf_ids'])->update(['official_id' => $official->id]);
        }

        if ($request->has('is_default') && $request->is_default) {
            User::where('is_default', true)->where('id', '!=', $official->id)->update(['is_default' => false]);
            $official->update(['is_default' => true]);
        } elseif ($request->has('is_default') && !$request->is_default) {
            $official->update(['is_default' => false]);
        }

        return redirect()->back()->with('success', 'Official updated successfully!');
    }

    public function setDefault(User $official)
    {
        // Unset ALL users as default first (reliable bulk update)
        User::where('is_default', true)->update(['is_default' => false]);
        $official->update(['is_default' => true]);

        return redirect()->back()->with('success', $official->name . ' set as default official!');
    }

    public function destroy(User $official)
    {
        $official->delete();
        return redirect()->back()->with('success', 'Official removed successfully!');
    }
}
