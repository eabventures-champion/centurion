<x-app-layout>
    <x-slot name="header">
        {{ __('Church Credentials') }}
    </x-slot>

    <div class="py-6" x-data="{ 
        search: '',
        matches(churchName, groupName, pastorName, contact, email) {
            const s = this.search.toLowerCase();
            return churchName.toLowerCase().includes(s) || 
                   groupName.toLowerCase().includes(s) || 
                   pastorName.toLowerCase().includes(s) || 
                   contact.toLowerCase().includes(s) || 
                   email.toLowerCase().includes(s);
        }
    }">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="glass-card p-8">
                <div class="mb-8 flex flex-col md:flex-row md:items-center justify-between gap-6">
                    <div>
                        <h3 class="text-lg font-bold text-white tracking-tight text-emerald-400">System Credentials</h3>
                        <p class="text-[10px] font-bold text-slate-500 uppercase tracking-widest mt-1">
                            Sensitive login details for all registered churches and pastors
                        </p>
                    </div>

                    {{-- Search Bar --}}
                    <div class="relative w-full md:w-80 group">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <svg class="w-4 h-4 text-slate-500 group-focus-within:text-emerald-400 transition-colors"
                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </div>
                        <input type="text" x-model="search" placeholder="SEARCH CHURCH OR PASTOR..."
                            class="w-full bg-slate-950/50 border-white/5 text-white placeholder:text-slate-600 focus:border-emerald-500/30 focus:ring-emerald-500/10 rounded-xl py-3 pl-11 pr-4 transition-all text-[10px] font-black tracking-widest uppercase">
                    </div>
                </div>

                <div class="overflow-hidden rounded-2xl border border-white/5 bg-slate-900/50" x-cloak>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr>
                                    <th
                                        class="px-6 py-4 border-b border-white/5 bg-white/5 text-[10px] font-black text-slate-400 uppercase tracking-[2px]">
                                        Church</th>
                                    <th
                                        class="px-6 py-4 border-b border-white/5 bg-white/5 text-[10px] font-black text-slate-400 uppercase tracking-[2px]">
                                        Pastor & Contact</th>
                                    <th
                                        class="px-6 py-4 border-b border-white/5 bg-white/5 text-[10px] font-black text-slate-400 uppercase tracking-[2px]">
                                        Login Email (Username)</th>
                                    <th
                                        class="px-6 py-4 border-b border-white/5 bg-white/5 text-[10px] font-black text-rose-400 uppercase tracking-[2px]">
                                        Password</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-white/5">
                                @forelse($churches as $church)
                                    <tr class="hover:bg-white/5 transition-colors group"
                                        x-show="matches('{{ addslashes($church->name) }}', '{{ addslashes($church->churchGroup->group_name) }}', '{{ addslashes($church->leader_name) }}', '{{ addslashes($church->leader_contact) }}', '{{ addslashes($church->pastor ? $church->pastor->email : '') }}')">
                                        <td class="px-6 py-4">
                                            <div class="flex items-center gap-3">
                                                <div
                                                    class="w-10 h-10 rounded-lg bg-indigo-600/20 text-indigo-400 flex items-center justify-center font-bold">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                                    </svg>
                                                </div>
                                                <div>
                                                    <div
                                                        class="text-sm font-bold text-white group-hover:text-indigo-400 transition-colors">
                                                        {{ $church->name }}
                                                    </div>
                                                    <div class="text-[10px] text-slate-500 uppercase tracking-wider mt-0.5">
                                                        {{ $church->churchGroup->group_name }}
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="text-xs text-slate-400 font-medium whitespace-nowrap">
                                                {{ $church->title }} {{ $church->leader_name }}
                                            </div>
                                            <div class="text-[10px] text-slate-500 mt-0.5 font-bold">
                                                {{ $church->leader_contact }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="text-xs text-slate-400 font-medium">
                                                {{ $church->pastor ? $church->pastor->email : 'N/A' }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <span
                                                class="inline-block px-3 py-1.5 rounded-lg bg-rose-500/10 border border-rose-500/20">
                                                <span class="text-xs font-black text-rose-400 tracking-wider">
                                                    {{ $church->pastor && $church->pastor->plain_password ? $church->pastor->plain_password : '********' }}
                                                </span>
                                            </span>
                                            @if($church->pastor && !$church->pastor->plain_password)
                                                <p class="text-[9px] text-slate-600 italic mt-1 font-bold uppercase">Legacy
                                                    Hashed Password</p>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4"
                                            class="px-6 py-12 text-center text-slate-500 italic uppercase font-black tracking-widest text-[10px]">
                                            No church credentials found.
                                        </td>
                                    </tr>
                                @endforelse
                                {{-- Alpine No Results Row --}}
                                <tr x-show="document.querySelectorAll('tbody tr[x-show]:not([style*=\'display: none\'])').length === 0"
                                    x-cloak>
                                    <td colspan="4"
                                        class="px-6 py-12 text-center text-slate-500 italic uppercase font-black tracking-widest text-[10px]">
                                        No matching credentials found for "<span x-text="search"
                                            class="text-emerald-400"></span>"
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
创新