<x-app-layout :show-back="true">
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('church-categories.index') }}"
                class="p-2 text-slate-400 hover:text-slate-900 dark:hover:text-white transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
            </a>
            {{ __('Edit Church Category') }}
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-3xl mx-auto px-4">
            <div class="glass-card p-8">
                <div class="mb-8">
                    <h3 class="text-xl font-bold text-slate-900 dark:text-white tracking-tight">Modify Category</h3>
                    <p class="text-[10px] font-bold text-slate-500 uppercase tracking-widest mt-1">Update foundation
                        level classification details</p>
                </div>

                <form action="{{ route('church-categories.update', $churchCategory) }}" method="POST" class="space-y-8">
                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-1 gap-6">
                        <div>
                            <x-input-label for="name" :value="__('Category Name')" />
                            <x-text-input type="text" name="name" id="name"
                                value="{{ old('name', $churchCategory->name) }}" required class="block w-full" />
                            <x-input-error :messages="$errors->get('name')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="zonal_pastor_name" :value="__('Zonal Pastor Name')" />
                            <x-text-input type="text" name="zonal_pastor_name" id="zonal_pastor_name"
                                value="{{ old('zonal_pastor_name', $churchCategory->zonal_pastor_name) }}"
                                class="block w-full" />
                            <x-input-error :messages="$errors->get('zonal_pastor_name')" class="mt-2" />
                        </div>
                    </div>

                    <div class="flex items-center justify-end gap-3 pt-4 border-t border-white/5">
                        <x-secondary-button href="{{ route('church-categories.index') }}" tag="a">
                            {{ __('Cancel') }}
                        </x-secondary-button>
                        <x-primary-button type="submit">
                            {{ __('Save Changes') }}
                        </x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>