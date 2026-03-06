<x-app-layout>
    <x-slot name="header">
        {{ __('Church Categories') }}
    </x-slot>

    <div class="py-6" x-data="{ addModalOpen: false }">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Header Section with Add Button -->
            <div class="mb-10 flex flex-col sm:flex-row sm:items-center justify-between gap-6">
                <div>
                    <div class="flex items-center gap-3">
                        <h3 class="text-xl font-bold text-slate-800 dark:text-white tracking-tight">Existing Categories
                        </h3>
                        <span
                            class="px-2 py-0.5 rounded-lg bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 border border-indigo-500/20 text-[10px] font-black uppercase tracking-widest">{{ $categories->count() }}</span>
                    </div>
                    <p class="text-[10px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-widest mt-1">
                        Foundation level classification
                    </p>
                </div>

                <button @click="addModalOpen = true"
                    class="flex items-center gap-2 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-bold rounded-xl transition-all shadow-lg shadow-indigo-500/30">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Add New Category
                </button>
            </div>

            <!-- Categories List (Full Width) -->
            <div class="glass-card p-4 sm:p-8">
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

                @if($errors->any())
                    <div
                        class="flash-alert mb-6 p-4 bg-rose-500/10 border border-rose-500/20 text-rose-600 dark:text-rose-400 rounded-xl flex items-start gap-3">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <ul class="text-sm font-bold list-disc list-inside">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @forelse($categories as $category)
                        <div
                            class="p-6 rounded-2xl bg-slate-50 dark:bg-white/5 border border-slate-200 dark:border-white/5 flex flex-col justify-between group hover:border-indigo-500/30 transition-all h-full">
                            <div>
                                <div class="flex items-center gap-4 mb-4">
                                    <div
                                        class="w-12 h-12 rounded-xl bg-slate-100 dark:bg-slate-800 text-indigo-600 dark:text-indigo-400 flex items-center justify-center font-black text-lg group-hover:scale-110 transition-transform">
                                        {{ strtoupper(substr($category->name, 0, 1)) }}
                                    </div>
                                    <h4
                                        class="text-base font-bold text-slate-900 dark:text-white group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors">
                                        {{ $category->name }}
                                    </h4>
                                </div>
                                <div class="space-y-2 mb-6">
                                    <p
                                        class="text-xs text-slate-500 dark:text-slate-400 flex items-center gap-2 font-medium">
                                        <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 002-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10">
                                            </path>
                                        </svg>
                                        {{ $category->churchGroups->count() }} Groups Assigned
                                    </p>
                                    @if($category->zonal_pastor_name)
                                        <p
                                            class="text-xs text-slate-500 dark:text-slate-400 flex items-center gap-2 font-medium">
                                            <svg class="w-4 h-4 text-indigo-500" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                            </svg>
                                            {{ $category->zonal_pastor_name }}
                                        </p>
                                    @endif
                                </div>
                            </div>

                            <div
                                class="flex items-center justify-end gap-2 pt-4 border-t border-slate-200 dark:border-white/5 mt-auto">
                                <a href="{{ route('church-categories.edit', $category) }}"
                                    class="p-2 bg-slate-100 hover:bg-indigo-500/10 text-slate-500 hover:text-indigo-600 dark:bg-white/5 dark:hover:bg-indigo-500/20 dark:text-slate-400 dark:hover:text-indigo-400 rounded-lg transition-colors"
                                    aria-label="Edit Category">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M11 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-5M16.5 3.5a2.121 2.121 0 013 3L7 19l-4 1 1-4L16.5 3.5z" />
                                    </svg>
                                </a>
                                <form action="{{ route('church-categories.destroy', $category) }}" method="POST"
                                    onsubmit="return confirm('Are you sure you want to delete this category?')"
                                    class="inline-block">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                        class="p-2 bg-slate-100 hover:bg-rose-500/10 text-slate-500 hover:text-rose-600 dark:bg-white/5 dark:hover:bg-rose-500/20 dark:text-slate-400 dark:hover:text-rose-400 rounded-lg transition-colors"
                                        aria-label="Delete Category">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        </div>
                    @empty
                        <div
                            class="col-span-1 md:col-span-2 lg:col-span-3 text-center py-16 px-4 rounded-2xl border border-dashed border-slate-300 dark:border-slate-700">
                            <div
                                class="w-16 h-16 rounded-full bg-slate-100 dark:bg-slate-800 flex items-center justify-center mx-auto mb-4">
                                <svg class="w-8 h-8 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                        d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 002-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                                </svg>
                            </div>
                            <h3 class="text-sm font-bold text-slate-900 dark:text-white mb-1">No Categories Found</h3>
                            <p class="text-xs text-slate-500">Get started by adding a new church category.</p>
                            <button @click="addModalOpen = true"
                                class="mt-6 px-4 py-2 bg-slate-900 dark:bg-white text-white dark:text-slate-900 text-xs font-bold rounded-lg hover:bg-slate-800 dark:hover:bg-slate-100 transition-colors inline-block">
                                Add Category
                            </button>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Add Category Modal -->
        <div x-show="addModalOpen" class="fixed inset-0 z-[100] overflow-y-auto" style="display: none;">
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
                <!-- Overlay -->
                <div x-show="addModalOpen" x-transition:enter="ease-out duration-300"
                    x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                    x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100"
                    x-transition:leave-end="opacity-0" class="fixed inset-0 transition-opacity" aria-hidden="true"
                    @click="addModalOpen = false">
                    <div class="absolute inset-0 bg-slate-900/80 backdrop-blur-sm"></div>
                </div>

                <!-- Modal Panel -->
                <div x-show="addModalOpen" x-transition:enter="ease-out duration-300"
                    x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                    x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                    x-transition:leave="ease-in duration-200"
                    x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                    x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                    class="relative inline-block align-bottom bg-white dark:bg-slate-900 rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full border border-slate-200 dark:border-white/10"
                    @click.stop>

                    <div
                        class="px-6 py-5 border-b border-slate-200 dark:border-white/10 flex items-center justify-between">
                        <h3 class="text-lg font-bold text-slate-900 dark:text-white flex items-center gap-3">
                            <span
                                class="w-8 h-8 rounded-lg bg-indigo-500/10 text-indigo-500 flex items-center justify-center">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 4v16m8-8H4" />
                                </svg>
                            </span>
                            Add New Category
                        </h3>
                        <button @click="addModalOpen = false"
                            class="text-slate-400 hover:text-rose-500 transition-colors p-2 rounded-lg hover:bg-rose-500/10">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <form action="{{ route('church-categories.store') }}" method="POST">
                        @csrf
                        <div class="px-6 py-6 space-y-6">
                            <div>
                                <x-input-label for="name" :value="__('Category Name')" />
                                <x-text-input type="text" name="name" id="name" placeholder="e.g. Zonal church" required
                                    value="{{ old('name') }}" class="block w-full" autofocus />
                                <x-input-error :messages="$errors->get('name')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="zonal_pastor_name" :value="__('Zonal Pastor Name')" />
                                <x-text-input type="text" name="zonal_pastor_name" id="zonal_pastor_name"
                                    placeholder="Full name" value="{{ old('zonal_pastor_name') }}"
                                    class="block w-full" />
                                <x-input-error :messages="$errors->get('zonal_pastor_name')" class="mt-2" />
                            </div>
                        </div>

                        <div
                            class="px-6 py-4 bg-slate-50 dark:bg-white/5 border-t border-slate-200 dark:border-white/10 flex flex-col sm:flex-row items-center justify-center gap-3">
                            <x-primary-button type="submit" class="w-full sm:w-auto px-8 justify-center">
                                {{ __('Create Category') }}
                            </x-primary-button>
                            <button type="button" @click="addModalOpen = false"
                                class="w-full sm:w-auto px-4 py-2 text-sm font-bold text-slate-600 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-white/10 rounded-xl transition-colors">
                                Cancel
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    </div>
</x-app-layout>