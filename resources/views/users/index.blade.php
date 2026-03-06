<x-app-layout>
    <x-slot name="header">
        {{ __('User Management') }}
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Header Section -->
            <div class="mb-10 flex flex-col sm:flex-row sm:items-center justify-between gap-6">
                <div>
                    <div class="flex items-center gap-3">
                        <h3 class="text-xl font-bold text-slate-800 dark:text-white tracking-tight">System Users</h3>
                        <span
                            class="px-2 py-0.5 rounded-lg bg-amber-500/10 text-amber-600 dark:text-amber-500 border border-amber-500/20 text-[10px] font-black uppercase tracking-widest shadow-sm shadow-amber-500/10">
                            {{ $users->count() }}
                        </span>
                    </div>
                    <p class="text-[10px] font-bold text-slate-500 uppercase tracking-widest mt-1">
                        Overview of all registered users and their roles
                    </p>
                </div>
            </div>

            <div class="glass-card p-4 sm:p-8">

                @if(session('success'))
                    <div
                        class="flash-alert mb-6 p-4 bg-emerald-500/10 border border-emerald-500/20 rounded-xl flex items-center gap-3 text-emerald-600 dark:text-emerald-400 text-sm font-bold">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        {{ session('success') }}
                    </div>
                @endif

                @if(session('error'))
                    <div
                        class="flash-alert mb-6 p-4 bg-rose-500/10 border border-rose-500/20 rounded-xl flex items-center gap-3 text-rose-600 dark:text-rose-400 text-sm font-bold">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                        {{ session('error') }}
                    </div>
                @endif

                @if($pendingApprovalsCount > 0)
                    <div
                        class="mb-6 p-4 bg-amber-500/10 border border-amber-500/20 rounded-2xl flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3">
                        <div class="flex items-center gap-3">
                            <div class="p-2 bg-amber-500/15 rounded-xl">
                                <svg class="w-5 h-5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                </svg>
                            </div>
                            <div>
                                <p class="text-sm font-black text-amber-600 dark:text-amber-400">
                                    {{ $pendingApprovalsCount }} Pending
                                    Approval{{ $pendingApprovalsCount !== 1 ? 's' : '' }}
                                </p>
                                <p
                                    class="text-[10px] font-bold text-amber-600/70 dark:text-amber-500/70 uppercase tracking-widest">
                                    Review and approve new admin accounts</p>
                            </div>
                        </div>
                    </div>
                @endif

                @if($pendingDeletionsCount > 0)
                    <div
                        class="mb-6 p-4 bg-rose-500/10 border border-rose-500/20 rounded-2xl flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3">
                        <div class="flex items-center gap-3">
                            <div class="p-2 bg-rose-500/15 rounded-xl">
                                <svg class="w-5 h-5 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                            </div>
                            <div>
                                <p class="text-sm font-black text-rose-600 dark:text-rose-400">
                                    {{ $pendingDeletionsCount }} Pending
                                    Deletion{{ $pendingDeletionsCount !== 1 ? 's' : '' }}
                                </p>
                                <p
                                    class="text-[10px] font-bold text-rose-600/70 dark:text-rose-500/70 uppercase tracking-widest">
                                    Review and confirm account deletion requests</p>
                            </div>
                        </div>
                    </div>
                @endif

                <div
                    class="overflow-hidden rounded-2xl border border-slate-200 dark:border-white/5 bg-slate-50 dark:bg-slate-900/50">
                    <div class="overflow-x-auto overflow-y-hidden w-full max-w-full">
                        <table class="w-full text-left border-collapse min-w-[900px]">
                            <thead>
                                <tr>
                                    <th
                                        class="px-6 py-4 border-b border-slate-200 dark:border-white/5 bg-slate-100 dark:bg-white/5 text-[10px] font-black text-slate-500 dark:text-slate-400 uppercase tracking-[2px]">
                                        User</th>
                                    <th
                                        class="px-6 py-4 border-b border-slate-200 dark:border-white/5 bg-slate-100 dark:bg-white/5 text-[10px] font-black text-slate-500 dark:text-slate-400 uppercase tracking-[2px]">
                                        Contact & Gender</th>
                                    <th
                                        class="px-6 py-4 border-b border-slate-200 dark:border-white/5 bg-slate-100 dark:bg-white/5 text-[10px] font-black text-slate-500 dark:text-slate-400 uppercase tracking-[2px]">
                                        Status</th>
                                    <th
                                        class="px-6 py-4 border-b border-slate-200 dark:border-white/5 bg-slate-100 dark:bg-white/5 text-[10px] font-black text-slate-500 dark:text-slate-400 uppercase tracking-[2px]">
                                        Role</th>
                                    <th
                                        class="px-6 py-4 border-b border-slate-200 dark:border-white/5 bg-slate-100 dark:bg-white/5 text-[10px] font-black text-slate-500 dark:text-slate-400 uppercase tracking-[2px] text-right">
                                        Registered Date</th>
                                    <th
                                        class="px-6 py-4 border-b border-slate-200 dark:border-white/5 bg-slate-100 dark:bg-white/5 text-[10px] font-black text-slate-500 dark:text-slate-400 uppercase tracking-[2px] text-right">
                                        Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-200 dark:divide-white/5">
                                @forelse($users as $user)
                                    <tr class="hover:bg-slate-100 dark:hover:bg-white/5 transition-colors group">
                                        <td class="px-6 py-4">
                                            <div class="flex items-center gap-3">
                                                <div
                                                    class="w-10 h-10 rounded-full bg-indigo-600/20 text-indigo-400 flex items-center justify-center font-bold text-sm overflow-hidden border border-indigo-500/20">
                                                    @if($user->profile_picture)
                                                        <img src="{{ Storage::url($user->profile_picture) }}"
                                                            alt="{{ $user->name }}" class="w-full h-full object-cover">
                                                    @else
                                                        {{ strtoupper(substr($user->name, 0, 1)) }}
                                                    @endif
                                                </div>
                                                <div>
                                                    <div
                                                        class="text-sm font-bold text-slate-900 dark:text-white group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors">
                                                        {{ $user->title ? $user->title . ' ' : '' }}{{ $user->name }}
                                                    </div>
                                                    <div class="text-[10px] text-slate-500 flex items-center gap-1 mt-0.5">
                                                        <svg class="w-3 h-3" fill="none" stroke="currentColor"
                                                            viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                                        </svg>
                                                        {{ $user->email }}
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="text-xs text-slate-700 dark:text-slate-400 font-medium">
                                                {{ $user->contact ?? 'N/A' }}
                                            </div>
                                            <div class="text-[10px] text-slate-500 mt-0.5">
                                                {{ $user->gender ?? '—' }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4">
                                            @if($user->pending_deletion)
                                                <div class="text-xs font-bold text-rose-600 dark:text-rose-400">Pending Deletion
                                                </div>
                                            @elseif(!$user->is_approved)
                                                <div class="text-xs font-bold text-amber-600 dark:text-amber-400">Pending
                                                    Approval</div>
                                            @else
                                                <div class="text-xs font-bold text-emerald-600 dark:text-emerald-400">Active
                                                </div>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="flex flex-wrap gap-1">
                                                @forelse($user->getRoleNames() as $role)
                                                                                    <span
                                                                                        class="inline-flex items-center px-2 py-0.5 rounded-full text-[9px] font-black uppercase tracking-widest 
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                {{ $role === 'Super Admin' ? 'bg-rose-500/10 text-rose-600 dark:text-rose-400 border border-rose-500/20' :
                                                    ($role === 'Official' ? 'bg-amber-500/10 text-amber-600 dark:text-amber-400 border border-amber-500/20' :
                                                        ($role === 'Admin' ? 'bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 border border-indigo-500/20' :
                                                            'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-500/20')) }}">
                                                                                        {{ $role }}
                                                                                    </span>
                                                @empty
                                                    <span class="text-xs text-slate-500 italic">No roles</span>
                                                @endforelse
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 text-right">
                                            <div class="text-sm font-medium text-slate-900 dark:text-white">
                                                {{ $user->created_at->format('M j, Y') }}
                                            </div>
                                            <div class="text-[10px] text-slate-500 mt-0.5">
                                                {{ $user->created_at->format('g:i A') }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 text-right">
                                            <div class="flex items-center justify-end gap-2 text-slate-400">
                                                @if(!$user->is_approved)
                                                    <form action="{{ route('users.approve', $user) }}" method="POST"
                                                        onsubmit="return confirm('Are you sure you want to approve this user? They will be granted access to the system.');"
                                                        class="inline">
                                                        @csrf
                                                        <button type="submit"
                                                            class="p-2 hover:bg-emerald-500/10 rounded-lg text-emerald-500 hover:text-emerald-600 transition-all transition-colors"
                                                            title="Approve User">
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                                viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    stroke-width="2.5" d="M5 13l4 4L19 7" />
                                                            </svg>
                                                        </button>
                                                    </form>
                                                @endif
                                                <a href="{{ route('users.edit', $user) }}"
                                                    class="p-2 hover:bg-slate-200 dark:hover:bg-white/10 rounded-lg text-slate-500 dark:text-slate-400 hover:text-indigo-600 dark:hover:text-indigo-400 transition-all transition-colors"
                                                    title="Edit User">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                    </svg>
                                                </a>
                                                @if(!$user->hasRole('Super Admin'))
                                                    @if($user->pending_deletion)
                                                        {{-- Cancel Deletion --}}
                                                        <form action="{{ route('users.cancel-deletion', $user) }}" method="POST"
                                                            onsubmit="return confirm('Cancel the deletion request and restore this user\'s access?');"
                                                            class="inline">
                                                            @csrf
                                                            <button type="submit"
                                                                class="p-2 hover:bg-emerald-500/10 rounded-lg text-emerald-500 hover:text-emerald-600 transition-all"
                                                                title="Cancel Deletion & Restore Access">
                                                                <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                                    viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                                        stroke-width="2"
                                                                        d="M3 10h10a5 5 0 015 5v2M3 10l6 6m-6-6l6-6" />
                                                                </svg>
                                                            </button>
                                                        </form>
                                                        {{-- Approve Deletion (Hard Delete) --}}
                                                        <form action="{{ route('users.destroy', $user) }}" method="POST"
                                                            onsubmit="return confirm('PERMANENTLY DELETE this user? This action cannot be undone.');"
                                                            class="inline">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit"
                                                                class="p-2 hover:bg-rose-500/10 rounded-lg text-rose-500 hover:text-rose-600 transition-all"
                                                                title="Approve Deletion (Permanent)">
                                                                <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                                    viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                                        stroke-width="2"
                                                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                                </svg>
                                                            </button>
                                                        </form>
                                                    @else
                                                        <form action="{{ route('users.destroy', $user) }}" method="POST"
                                                            onsubmit="return confirm('Are you sure you want to delete this user? This action cannot be undone.');"
                                                            class="inline">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit"
                                                                class="p-2 hover:bg-rose-500/10 rounded-lg hover:text-rose-500 transition-all transition-colors"
                                                                title="Delete User">
                                                                <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                                    viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                                        stroke-width="2"
                                                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                                </svg>
                                                            </button>
                                                        </form>
                                                    @endif
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="px-6 py-8 text-center text-slate-500 italic">
                                            No users found.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>