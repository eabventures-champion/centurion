<x-app-layout>
    <x-slot name="header">
        {{ __('Church Categories') }}
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Add Form -->
                <div class="lg:col-span-1">
                    <div class="glass-card p-5 sticky top-24">
                        <h3 class="text-base font-bold text-slate-900 dark:text-white mb-5 flex items-center gap-2">
                            <span
                                class="w-7 h-7 rounded-lg bg-indigo-600/10 text-indigo-600 dark:text-indigo-400 flex items-center justify-center">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 4v16m8-8H4" />
                                </svg>
                            </span>
                            Add New Category
                        </h3>

                        <form action="{{ route('church-categories.store') }}" method="POST" class="space-y-6">
                            @csrf
                            <div>
                                <x-input-label for="name" :value="__('Category Name')" />
                                <x-text-input type="text" name="name" id="name" placeholder="e.g. Zonal church" required
                                    value="{{ old('name') }}" class="block w-full" />
                                <x-input-error :messages="$errors->get('name')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="zonal_pastor_name" :value="__('Zonal Pastor Name')" />
                                <x-text-input type="text" name="zonal_pastor_name" id="zonal_pastor_name"
                                    placeholder="Full name" value="{{ old('zonal_pastor_name') }}"
                                    class="block w-full" />
                                <x-input-error :messages="$errors->get('zonal_pastor_name')" class="mt-2" />
                            </div>

                            <x-primary-button type="submit" class="w-full">
                                {{ __('Create Category') }}
                            </x-primary-button>
                        </form>
                    </div>
                </div>

                <!-- Categories List -->
                <div class="lg:col-span-2">
                    <div class="glass-card p-5">
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

                        <div class="mb-6">
                            <h3 class="text-base font-bold text-slate-900 dark:text-white tracking-tight">Existing
                                Categories</h3>
                            <p
                                class="text-[9px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-widest mt-1">
                                Foundation
                                level classification</p>
                        </div>

                        <div class="space-y-4">
                            @forelse($categories as $category)
                                <div
                                    class="p-6 rounded-2xl bg-slate-50 dark:bg-white/5 border border-slate-200 dark:border-white/5 flex items-center justify-between group hover:border-indigo-500/30 transition-all">
                                    <div class="flex items-center gap-5">
                                        <div
                                            class="w-14 h-14 rounded-xl bg-slate-100 dark:bg-white/5 flex items-center justify-center font-bold text-sm text-indigo-600 dark:text-indigo-400 group-hover:scale-110 transition-transform">
                                            {{ strtoupper(substr($category->name, 0, 1)) }}
                                        </div>
                                        <div>
                                            <h4
                                                class="text-sm font-bold text-slate-900 dark:text-white group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors">
                                                {{ $category->name }}
                                            </h4>
                                            <div class="flex flex-wrap gap-x-4 gap-y-1 mt-1">
                                                <p class="text-[10px] text-slate-500 font-bold uppercase tracking-wider">
                                                    {{ $category->churchGroups->count() }} Groups
                                                </p>
                                                @if($category->zonal_pastor_name)
                                                    <p class="text-[10px] text-slate-400 font-medium flex items-center gap-1">
                                                        <svg class="w-3 h-3" fill="none" stroke="currentColor"
                                                            viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                                        </svg>
                                                        {{ $category->zonal_pastor_name }}
                                                    </p>
                                                @endif
                                            </div>
                                        </div>
                                    </div>

                                    <div class="flex items-center gap-2">
                                        <a href="{{ route('church-categories.edit', $category) }}"
                                            class="p-2 text-slate-400 hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors"
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
                                                class="p-2 text-slate-400 hover:text-rose-600 dark:hover:text-rose-500 transition-colors"
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
                                <div class="text-center py-10">
                                    <p class="text-slate-500 italic">No categories found.</p>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>