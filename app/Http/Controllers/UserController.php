<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;

class UserController extends Controller
{
    public function index()
    {
        $users = User::with('roles')
            ->orderByDesc('pending_deletion') // true (1) comes before false (0)
            ->orderBy('is_approved', 'asc') // false (0) pending approvals come next
            ->orderBy('name')
            ->get();
        $pendingApprovalsCount = User::where('is_approved', false)->count();
        $pendingDeletionsCount = User::where('pending_deletion', true)->count();
        return view('users.index', compact('users', 'pendingApprovalsCount', 'pendingDeletionsCount'));
    }

    public function edit(User $user)
    {
        $roles = \Spatie\Permission\Models\Role::all();
        return view('users.edit', compact('user', 'roles'));
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'title' => 'nullable|string|max:20',
            'roles' => 'required|array',
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        $userData = $request->only('name', 'email', 'title');

        if ($request->filled('password')) {
            $userData['password'] = \Illuminate\Support\Facades\Hash::make($request->password);
            $userData['plain_password'] = $request->password;
        }

        $user->update($userData);
        $user->syncRoles($request->roles);

        return redirect()->route('users.index')->with('success', 'User updated successfully');
    }

    public function destroy(User $user)
    {
        if ($user->hasRole('Super Admin')) {
            return redirect()->route('users.index')->with('error', 'Super Admin cannot be deleted');
        }

        $user->delete();
        return redirect()->route('users.index')->with('success', 'User deleted successfully');
    }

    public function approve(User $user)
    {
        $user->update(['is_approved' => true]);
        return redirect()->back()->with('success', 'User approved successfully.');
    }

    public function cancelDeletion(User $user)
    {
        $user->update(['pending_deletion' => false]);
        return redirect()->back()->with('success', 'User account deletion request cancelled. Access restored.');
    }
}
