<x-app-layout>
    <x-slot name="header">
        Homepage Settings
    </x-slot>

    <div class="max-w-4xl mx-auto py-10 sm:px-6 lg:px-8">
        <div class="glass-card p-8 group">
            <div class="mb-8">
                <h3 class="text-xl font-black text-slate-900 dark:text-white tracking-tight">Souls Winning Theme</h3>
                <p class="text-sm text-slate-500 mt-1 uppercase font-bold tracking-widest text-[10px]">Customize your
                    landing page appearance</p>
            </div>

            <form action="{{ route('homepage-settings.update') }}" method="POST" enctype="multipart/form-data"
                class="space-y-6">
                @csrf

                @if(session('success'))
                    <div
                        class="flash-alert mb-8 p-4 bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 rounded-xl flex items-center gap-3">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span class="text-sm font-bold">{{ session('success') }}</span>
                    </div>
                @endif

                @if($errors->any())
                    <div class="p-4 bg-rose-500/10 border border-rose-500/20 rounded-xl animate-in fade-in slide-in-from-top-2">
                        <h4 class="text-sm font-black text-rose-400 uppercase tracking-widest mb-2">Please fix the following errors:</h4>
                        <ul class="list-disc list-inside space-y-1">
                            @foreach($errors->all() as $error)
                                <li class="text-xs text-rose-300 font-bold uppercase tracking-wider">{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="grid grid-cols-1 gap-6">
                    <!-- Hero Heading -->
                    <div>
                        <x-input-label for="hero_heading" :value="__('Hero Heading')" />
                        <x-text-input type="text" name="hero_heading" id="hero_heading"
                            value="{{ old('hero_heading', $settings->hero_heading) }}"
                            class="block w-full" />
                        <x-input-error :messages="$errors->get('hero_heading')" class="mt-2" />
                    </div>

                    <!-- Hero Subtext -->
                    <div>
                        <x-input-label for="hero_subtext" :value="__('Hero Subtext (Tagline)')" />
                        <x-text-input type="text" name="hero_subtext" id="hero_subtext"
                            value="{{ old('hero_subtext', $settings->hero_subtext) }}"
                            class="block w-full" />
                        <x-input-error :messages="$errors->get('hero_subtext')" class="mt-2" />
                    </div>

                    <!-- Hero Description -->
                    <div>
                        <x-input-label for="hero_description" :value="__('Hero Description')" />
                        <x-textarea name="hero_description" id="hero_description" rows="4"
                            class="block w-full">{{ old('hero_description', $settings->hero_description) }}</x-textarea>
                        <x-input-error :messages="$errors->get('hero_description')" class="mt-2" />
                    </div>

                    <!-- Background Image -->
                    <div class="space-y-4">
                        <x-input-label for="background_image" :value="__('Hero Background Image')" />

                        @if($settings->background_image)
                            <div class="relative w-full h-48 rounded-2xl overflow-hidden border border-white/10 group/img">
                                <img src="{{ asset('storage/' . $settings->background_image) }}"
                                    class="w-full h-full object-cover">
                                <div
                                    class="absolute inset-0 bg-slate-950/60 flex items-center justify-center opacity-0 group-hover/img:opacity-100 transition-opacity">
                                    <span class="text-[10px] font-black text-white uppercase tracking-[2px]">Current
                                        Background</span>
                                </div>
                            </div>
                        @endif

                        <div class="relative">
                            <input type="file" name="background_image" id="background_image" accept="image/*"
                                class="block w-full text-sm text-slate-500 file:mr-4 file:py-3 file:px-6 file:rounded-xl file:border-0 file:text-[10px] file:font-black file:uppercase file:tracking-[2px] file:bg-indigo-600 file:text-white hover:file:bg-indigo-500 transition-all cursor-pointer">
                        </div>
                        <p class="text-[10px] text-slate-500 italic font-bold">Recommended: High resolution 1920x1080 or
                            larger. Max size 5MB.</p>
                        <x-input-error :messages="$errors->get('background_image')" class="mt-2" />
                    </div>

                    <!-- Objectives Section -->
                    <div class="pt-8 border-t border-slate-200 dark:border-white/5 space-y-8">
                        <div>
                            <h4 class="text-sm font-black text-indigo-400 uppercase tracking-[2px] mb-6">Our Aim &
                                Objectives Section</h4>
                            <div class="grid grid-cols-1 gap-6">
                                <div>
                                    <x-input-label for="objectives_title" :value="__('Section Title')" />
                                    <x-text-input type="text" name="objectives_title" id="objectives_title"
                                        value="{{ old('objectives_title', $settings->objectives_title) }}"
                                        class="block w-full" />
                                </div>
                                <div>
                                    <x-input-label for="objectives_subtitle" :value="__('Section Subtitle')" />
                                    <x-textarea name="objectives_subtitle" id="objectives_subtitle" rows="2"
                                        class="block w-full">{{ old('objectives_subtitle', $settings->objectives_subtitle) }}</x-textarea>
                                </div>
                            </div>
                        </div>

                        <!-- Individual Objectives -->
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                            <!-- Objective 1 -->
                            <div class="space-y-4 p-4 rounded-2xl bg-white dark:bg-white/5 border border-slate-200 dark:border-white/5">
                                <span class="text-[10px] font-black text-indigo-400 uppercase tracking-[2px]">Objective
                                    1</span>
                                <div>
                                    <x-input-label for="obj_1_title" :value="__('Title')" />
                                    <x-text-input type="text" name="obj_1_title" id="obj_1_title"
                                        value="{{ old('obj_1_title', $settings->obj_1_title) }}"
                                        class="block w-full" />
                                </div>
                                <div>
                                    <x-input-label for="obj_1_description" :value="__('Description')" />
                                    <x-textarea name="obj_1_description" id="obj_1_description" rows="3"
                                        class="block w-full">{{ old('obj_1_description', $settings->obj_1_description) }}</x-textarea>
                                </div>
                            </div>

                            <!-- Objective 2 -->
                            <div class="space-y-4 p-4 rounded-2xl bg-white dark:bg-white/5 border border-slate-200 dark:border-white/5">
                                <span class="text-[10px] font-black text-emerald-400 uppercase tracking-[2px]">Objective
                                    2</span>
                                <div>
                                    <x-input-label for="obj_2_title" :value="__('Title')" />
                                    <x-text-input type="text" name="obj_2_title" id="obj_2_title"
                                        value="{{ old('obj_2_title', $settings->obj_2_title) }}"
                                        class="block w-full" />
                                </div>
                                <div>
                                    <x-input-label for="obj_2_description" :value="__('Description')" />
                                    <x-textarea name="obj_2_description" id="obj_2_description" rows="3"
                                        class="block w-full">{{ old('obj_2_description', $settings->obj_2_description) }}</x-textarea>
                                </div>
                            </div>

                            <!-- Objective 3 -->
                            <div class="space-y-4 p-4 rounded-2xl bg-white dark:bg-white/5 border border-slate-200 dark:border-white/5">
                                <span class="text-[10px] font-black text-rose-400 uppercase tracking-[2px]">Objective
                                    3</span>
                                <div>
                                    <x-input-label for="obj_3_title" :value="__('Title')" />
                                    <x-text-input type="text" name="obj_3_title" id="obj_3_title"
                                        value="{{ old('obj_3_title', $settings->obj_3_title) }}"
                                        class="block w-full" />
                                </div>
                                <div>
                                    <x-input-label for="obj_3_description" :value="__('Description')" />
                                    <x-textarea name="obj_3_description" id="obj_3_description" rows="3"
                                        class="block w-full">{{ old('obj_3_description', $settings->obj_3_description) }}</x-textarea>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Registration & Welcome Settings -->
                    <div class="pt-8 border-t border-slate-200 dark:border-white/5 space-y-8">
                        <div>
                            <h4 class="text-sm font-black text-indigo-400 uppercase tracking-[2px] mb-6">Pastor Registration Settings</h4>
                            <div class="grid grid-cols-1 gap-6">
                                <div class="flex items-center gap-4 p-4 rounded-2xl bg-white dark:bg-white/5 border border-slate-200 dark:border-white/5">
                                    <div class="flex items-center h-5">
                                        <input type="checkbox" name="show_welcome_modal" id="show_welcome_modal" 
                                            {{ old('show_welcome_modal', $settings->show_welcome_modal) ? 'checked' : '' }}
                                            class="h-5 w-5 bg-white dark:bg-slate-900 border-slate-300 dark:border-white/10 rounded text-indigo-600 focus:ring-indigo-500 focus:ring-offset-slate-950">
                                    </div>
                                    <div class="ml-3 text-sm">
                                        <label for="show_welcome_modal" class="font-black text-[10px] text-slate-600 dark:text-slate-300 uppercase tracking-widest">Show Welcome Modal</label>
                                        <p class="text-[10px] text-slate-500 font-bold uppercase tracking-widest mt-1">Check this to show a welcome modal to pastors after registration</p>
                                    </div>
                                </div>

                                <div>
                                    <x-input-label for="welcome_modal_heading" :value="__('Welcome Heading')" />
                                    <x-text-input type="text" name="welcome_modal_heading" id="welcome_modal_heading"
                                        value="{{ old('welcome_modal_heading', $settings->welcome_modal_heading) }}"
                                        placeholder="Welcome Home, {name}!"
                                        class="block w-full" />
                                    <p class="text-[10px] text-slate-500 italic font-bold mt-2">Use {name} to personalize with the pastor's name and {title} for their title.</p>
                                </div>

                                <div>
                                    <x-input-label for="welcome_modal_message" :value="__('Welcome Message')" />
                                    <x-textarea name="welcome_modal_message" id="welcome_modal_message" rows="4"
                                        placeholder="Specifically welcome our new pastors here..."
                                        class="block w-full">{{ old('welcome_modal_message', $settings->welcome_modal_message) }}</x-textarea>
                                    <p class="text-[10px] text-slate-500 italic font-bold mt-2">Use {name} to personalize the message with the pastor's name.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="pt-8 border-t border-slate-200 dark:border-white/5 flex justify-end">
                    <x-primary-button type="submit" class="px-12 py-4 text-[11px] tracking-[4px]">
                        {{ __('SAVE SETTINGS') }}
                    </x-primary-button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>