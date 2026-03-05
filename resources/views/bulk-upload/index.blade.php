<x-app-layout>
    <x-slot name="header">
        {{ __('Bulk Upload First Timers') }}
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto">
            <div class="glass-card p-6 md:p-10">

                @if(session('success'))
                    <div
                        class="flash-alert mb-6 p-4 bg-emerald-500/10 border border-emerald-500/20 text-emerald-600 dark:text-emerald-400 rounded-xl flex items-center gap-3">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span class="text-sm font-bold">{{ session('success') }}</span>
                    </div>
                @endif

                @if(session('error'))
                    <div
                        class="flash-alert mb-6 p-4 bg-rose-500/10 border border-rose-500/20 text-rose-600 dark:text-rose-400 rounded-xl flex items-center gap-3">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span class="text-sm font-bold">{{ session('error') }}</span>
                    </div>
                @endif

                @if(session('failures'))
                    <div
                        class="mb-8 p-6 bg-amber-500/10 border border-amber-500/20 text-amber-600 dark:text-amber-400 rounded-xl">
                        <h4 class="text-xs font-black uppercase tracking-[2px] mb-4">Import completed with errors:</h4>
                        <ul class="space-y-2">
                            @foreach(session('failures') as $failure)
                                <li class="text-sm font-medium flex items-center gap-2">
                                    <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                                    Row {{ $failure->row() }}: {{ implode(', ', $failure->errors()) }}
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-10">
                    <!-- Step 1: Download Template -->
                    <div
                        class="p-8 rounded-2xl bg-slate-50 dark:bg-white/5 border border-slate-200 dark:border-white/5 group hover:border-indigo-500/30 transition-all">
                        <h3
                            class="text-lg font-bold text-slate-900 dark:text-white mb-2 group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors">
                            1.
                            Download Template</h3>
                        <p class="text-sm text-slate-500 dark:text-slate-400 mb-8 leading-relaxed font-medium">
                            Download the Excel template to ensure your data is formatted correctly before uploading.
                        </p>
                        <a href="{{ route('bulk-upload.export') }}"
                            class="inline-flex items-center px-6 py-3 bg-indigo-600 border border-transparent rounded-xl font-black text-xs text-white uppercase tracking-widest hover:bg-indigo-500 active:scale-95 transition-all shadow-xl shadow-indigo-600/20">
                            Download Excel Template
                        </a>
                    </div>

                    <!-- Step 2: Upload Files -->
                    <div
                        class="p-8 rounded-2xl bg-slate-50 dark:bg-white/5 border border-slate-200 dark:border-white/5 group hover:border-emerald-500/30 transition-all">
                        <h3
                            class="text-lg font-bold text-slate-900 dark:text-white mb-2 group-hover:text-emerald-600 dark:group-hover:text-emerald-400 transition-colors">
                            2.
                            Upload Data</h3>
                        <form action="{{ route('bulk-upload.import') }}" method="POST" enctype="multipart/form-data"
                            class="space-y-6">
                            @csrf

                            @unless(auth()->user()->hasRole('Admin'))
                                <div>
                                    <label for="pcf_id"
                                        class="block text-[11px] font-black text-slate-500 uppercase tracking-[2px] mb-2">Assign
                                        to PCF</label>
                                    <select name="pcf_id" id="pcf_id"
                                        class="block w-full bg-slate-100 dark:bg-slate-900 border-slate-200 dark:border-white/5 text-slate-900 dark:text-white focus:border-emerald-500 focus:ring-emerald-500 rounded-xl shadow-sm transition-all">
                                        <option value="">Select PCF</option>
                                        @foreach($pcfs as $pcf)
                                            <option value="{{ $pcf->id }}">{{ $pcf->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            @endunless

                            <div>
                                <label for="file"
                                    class="block text-[11px] font-black text-slate-500 uppercase tracking-[2px] mb-2">Select
                                    Excel/CSV File</label>
                                <input type="file" name="file" id="file"
                                    class="block w-full text-xs text-slate-400 file:mr-4 file:py-3 file:px-6 file:rounded-xl file:border-0 file:text-[11px] file:font-black file:uppercase file:tracking-widest file:bg-emerald-600/20 file:text-emerald-400 hover:file:bg-emerald-600/40 transition-all cursor-pointer"
                                    required>
                            </div>

                            <div class="pt-2">
                                <button type="submit"
                                    class="w-full flex justify-center py-3 bg-emerald-600 border border-transparent rounded-xl font-black text-xs text-white uppercase tracking-widest hover:bg-emerald-500 active:scale-95 transition-all shadow-xl shadow-emerald-600/20">
                                    Start Import Process
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="mt-12 pt-8 border-t border-slate-200 dark:border-slate-800">
                    <h4 class="text-[11px] font-black text-slate-500 uppercase tracking-[2px] mb-4">Instructions &
                        Rules:</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-x-10 gap-y-3">
                        <div class="flex items-center gap-3 text-sm text-slate-500 dark:text-slate-400 font-medium">
                            <span class="w-1.5 h-1.5 rounded-full bg-slate-300 dark:bg-slate-700"></span>
                            <span>Ensure 'Gender' is either 'Male' or 'Female'.</span>
                        </div>
                        <div class="flex items-center gap-3 text-sm text-slate-500 dark:text-slate-400 font-medium">
                            <span class="w-1.5 h-1.5 rounded-full bg-slate-300 dark:bg-slate-700"></span>
                            <span>'Marital Status' must be: Single, Married, etc.</span>
                        </div>
                        <div class="flex items-center gap-3 text-sm text-slate-500 dark:text-slate-400 font-medium">
                            <span class="w-1.5 h-1.5 rounded-full bg-slate-300 dark:bg-slate-700"></span>
                            <span>'Born Again' and 'Water Baptism' (1 or 0).</span>
                        </div>
                        <div class="flex items-center gap-3 text-sm text-slate-500 dark:text-slate-400 font-medium">
                            <span class="w-1.5 h-1.5 rounded-full bg-slate-300 dark:bg-slate-700"></span>
                            <span>'Bringer Name' ensures correct attribution.</span>
                        </div>
                        <div class="flex items-center gap-3 text-sm text-slate-500 dark:text-slate-400 font-medium">
                            <span class="w-1.5 h-1.5 rounded-full bg-slate-300 dark:bg-slate-700"></span>
                            <span>'Bringer Contact' links automatically.</span>
                        </div>
                        <div
                            class="flex items-center gap-3 text-sm text-slate-500 dark:text-slate-400 font-medium font-bold">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                            <span>'Bringer Senior Cell' & 'Cell Name' are mandatory.</span>
                        </div>
                        <div
                            class="flex items-center gap-3 text-sm text-slate-500 dark:text-slate-400 font-medium font-bold">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                            <span>Dates (Visit/Birth) Format: YYYY-MM-DD (e.g., 2024-03-04).</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>