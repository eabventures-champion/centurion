<x-app-layout>
    <div class="min-h-screen py-8 px-4 sm:px-6 lg:px-8">
        <div class="max-w-7xl mx-auto">
            <!-- Header -->
            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-8">
                <div class="flex items-center gap-4">
                    <a href="{{ url()->previous() }}"
                        class="p-2.5 rounded-xl bg-white/5 hover:bg-white/10 border border-white/10 transition-all group">
                        <svg class="w-4 h-4 text-slate-400 group-hover:text-white transition-colors" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                        </svg>
                    </a>
                    <div>
                        <h1 class="text-2xl font-black tracking-tight text-white flex items-center gap-3">
                            <svg class="w-6 h-6 text-rose-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                            TRASH
                        </h1>
                        <p class="text-xs font-bold text-slate-500 mt-1">{{ $totalTrashed }}
                            item{{ $totalTrashed !== 1 ? 's' : '' }} in trash</p>
                    </div>
                </div>
            </div>

            <!-- Flash Messages -->
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
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                    {{ session('error') }}
                </div>
            @endif

            <!-- Tabs -->
            <div class="flex flex-wrap gap-2 mb-6">
                @foreach($trashed as $key => $data)
                            <a href="{{ route('trash.index', ['tab' => $key]) }}" class="px-4 py-2 rounded-xl text-xs font-black uppercase tracking-widest transition-all border
                                                {{ $activeTab === $key
                    ? 'bg-indigo-500/15 text-indigo-400 border-indigo-500/30'
                    : 'bg-white/5 text-slate-400 border-white/5 hover:bg-white/10 hover:text-white' }}">
                                {{ $data['label'] }}
                                @if($data['count'] > 0)
                                    <span
                                        class="ml-1.5 px-1.5 py-0.5 rounded-md text-[9px] font-black {{ $activeTab === $key ? 'bg-indigo-500/20 text-indigo-300' : 'bg-rose-500/15 text-rose-400' }}">{{ $data['count'] }}</span>
                                @endif
                            </a>
                @endforeach
            </div>

            <!-- Active Tab Content -->
            <div
                class="overflow-hidden rounded-2xl border border-slate-200 dark:border-white/5 bg-slate-50 dark:bg-slate-900/50">
                <div class="overflow-x-auto overflow-y-hidden w-full max-w-full">
                    <table class="w-full text-left border-collapse min-w-[700px] sm:min-w-[800px]">
                        <thead>
                            <tr>
                                <th
                                    class="px-6 py-4 border-b border-slate-200 dark:border-white/5 bg-slate-100 dark:bg-white/5 text-[10px] font-black text-slate-500 dark:text-slate-400 uppercase tracking-[2px]">
                                    Item</th>
                                <th
                                    class="px-6 py-4 border-b border-slate-200 dark:border-white/5 bg-slate-100 dark:bg-white/5 text-[10px] font-black text-slate-500 dark:text-slate-400 uppercase tracking-[2px]">
                                    Details</th>
                                <th
                                    class="px-6 py-4 border-b border-slate-200 dark:border-white/5 bg-slate-100 dark:bg-white/5 text-[10px] font-black text-slate-500 dark:text-slate-400 uppercase tracking-[2px]">
                                    Deleted At</th>
                                <th
                                    class="px-6 py-4 border-b border-slate-200 dark:border-white/5 bg-slate-100 dark:bg-white/5 text-[10px] font-black text-slate-500 dark:text-slate-400 uppercase tracking-[2px] text-right">
                                    Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-white/5">
                            @forelse($trashed[$activeTab]['items'] as $item)
                                <tr class="hover:bg-slate-100/50 dark:hover:bg-white/[0.02] transition-colors">
                                    <td class="px-6 py-4">
                                        <div class="text-sm font-bold text-slate-900 dark:text-white">
                                            @if($activeTab === 'categories')
                                                {{ $item->name }}
                                            @elseif($activeTab === 'groups')
                                                {{ $item->group_name }}
                                            @elseif($activeTab === 'churches')
                                                {{ $item->name }}
                                            @elseif($activeTab === 'pcfs')
                                                {{ $item->name }}
                                            @elseif($activeTab === 'users')
                                                {{ $item->title ? $item->title . ' ' : '' }}{{ $item->name }}
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-xs text-slate-500">
                                            @if($activeTab === 'categories')
                                                Zonal Pastor: {{ $item->zonal_pastor_name ?? '—' }}
                                            @elseif($activeTab === 'groups')
                                                Pastor: {{ $item->pastor_name ?? '—' }}
                                            @elseif($activeTab === 'churches')
                                                Leader: {{ $item->leader_name ?? '—' }}
                                            @elseif($activeTab === 'pcfs')
                                                Leader: {{ $item->leader_name ?? '—' }}
                                            @elseif($activeTab === 'users')
                                                {{ $item->email }} · {{ $item->contact ?? 'N/A' }}
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-xs text-slate-500">
                                            {{ $item->deleted_at->format('M j, Y') }}
                                        </div>
                                        <div class="text-[10px] text-slate-500/70 mt-0.5">
                                            {{ $item->deleted_at->format('g:i A') }} ·
                                            {{ $item->deleted_at->diffForHumans() }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <div class="flex items-center justify-end gap-2">
                                            <!-- Restore -->
                                            <form action="{{ route('trash.restore', [$activeTab, $item->id]) }}"
                                                method="POST"
                                                onsubmit="return confirm('Restore this item? It will be returned to its original location.');"
                                                class="inline">
                                                @csrf
                                                <button type="submit"
                                                    class="p-2 hover:bg-emerald-500/10 rounded-lg text-emerald-500 hover:text-emerald-600 transition-all"
                                                    title="Restore">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2" d="M3 10h10a5 5 0 015 5v2M3 10l6 6m-6-6l6-6" />
                                                    </svg>
                                                </button>
                                            </form>
                                            <!-- Permanently Delete -->
                                            <form action="{{ route('trash.force-delete', [$activeTab, $item->id]) }}"
                                                method="POST"
                                                onsubmit="return confirm('PERMANENTLY DELETE this item? This action cannot be undone.');"
                                                class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                    class="p-2 hover:bg-rose-500/10 rounded-lg text-rose-500 hover:text-rose-600 transition-all"
                                                    title="Permanently Delete">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                    </svg>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-12 text-center">
                                        <div class="flex flex-col items-center gap-3">
                                            <div class="p-3 bg-slate-500/10 rounded-2xl">
                                                <svg class="w-6 h-6 text-slate-500" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M5 13l4 4L19 7" />
                                                </svg>
                                            </div>
                                            <p class="text-sm text-slate-500 font-bold">No trashed
                                                {{ strtolower($trashed[$activeTab]['label']) }}
                                            </p>
                                            <p class="text-[10px] text-slate-500/70 uppercase tracking-widest">All items are
                                                active</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>