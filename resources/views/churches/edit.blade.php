<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('churches.index') }}"
                class="p-2 text-slate-400 hover:text-slate-900 dark:hover:text-white transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
            </a>
            {{ __('Edit Church') }}
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-3xl mx-auto px-4">
            <div class="glass-card p-8">
                <div class="mb-8">
                    <h3 class="text-xl font-bold text-slate-900 dark:text-white tracking-tight">Modify Church Details
                    </h3>
                    <p class="text-[10px] font-bold text-slate-500 uppercase tracking-widest mt-1">Update local assembly
                        information</p>
                </div>

                <form action="{{ route('churches.update', $church) }}" method="POST" class="space-y-8">
                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-1 gap-6">
                        <div>
                            <x-input-label for="church_group_id" :value="__('Church Group')" />
                            <select name="church_group_id" id="church_group_id" required
                                class="block w-full bg-slate-50 dark:bg-slate-950/50 border-slate-200 dark:border-white/10 text-slate-900 dark:text-white focus:border-indigo-500 focus:ring-indigo-500 rounded-xl shadow-sm transition-all duration-300 py-3 px-4">
                                @foreach($groups as $group)
                                    <option value="{{ $group->id }}" {{ $church->church_group_id == $group->id ? 'selected' : '' }}>{{ $group->group_name }}</option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('church_group_id')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="name" :value="__('Church Name')" />
                            <x-text-input type="text" name="name" id="name" value="{{ old('name', $church->name) }}"
                                required class="block w-full" />
                            <x-input-error :messages="$errors->get('name')" class="mt-2" />
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div>
                                <x-input-label for="title" :value="__('Title')" />
                                <select name="title" id="title" required
                                    class="block w-full bg-slate-50 dark:bg-slate-950/50 border-slate-200 dark:border-white/10 text-slate-900 dark:text-white focus:border-indigo-500 focus:ring-indigo-500 rounded-xl shadow-sm transition-all duration-300 py-3 px-4">
                                    @foreach(['Bro', 'Sis', 'Pastor', 'Dcn', 'Dcns', 'Mr', 'Mrs'] as $title)
                                        <option value="{{ $title }}" {{ old('title', $church->title) == $title ? 'selected' : '' }}>{{ $title }}</option>
                                    @endforeach
                                </select>
                                <x-input-error :messages="$errors->get('title')" class="mt-2" />
                            </div>
                            <div class="md:col-span-2">
                                <x-input-label for="leader_name" :value="__('Leader Name')" />
                                <x-text-input type="text" name="leader_name" id="leader_name"
                                    value="{{ old('leader_name', $church->leader_name) }}" required
                                    class="block w-full" />
                                <x-input-error :messages="$errors->get('leader_name')" class="mt-2" />
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <x-input-label for="leader_contact" :value="__('Leader Contact')" />
                                <x-text-input type="text" name="leader_contact" id="leader_contact"
                                    value="{{ old('leader_contact', $church->leader_contact) }}" required
                                    class="block w-full" />
                                <div id="contact-warning"
                                    class="mt-2 text-[11px] font-bold text-rose-500 uppercase tracking-wider hidden italic">
                                </div>
                                <x-input-error :messages="$errors->get('leader_contact')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="location" :value="__('Location')" />
                                <x-text-input type="text" name="location" id="location"
                                    value="{{ old('location', $church->location) }}" class="block w-full" />
                                <x-input-error :messages="$errors->get('location')" class="mt-2" />
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-200 dark:border-white/5">
                        <x-secondary-button href="{{ route('churches.index') }}" tag="a">
                            {{ __('Cancel') }}
                        </x-secondary-button>
                        <x-primary-button type="submit" id="submit-btn" class="px-8">
                            {{ __('Save Changes') }}
                        </x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script>
        function debounce(func, wait) {
            let timeout;
            return function (...args) {
                clearTimeout(timeout);
                timeout = setTimeout(() => func.apply(this, args), wait);
            };
        }

        const contactInput = document.getElementById('leader_contact');
        const warningDiv = document.getElementById('contact-warning');
        const submitBtn = document.getElementById('submit-btn');
        const churchId = "{{ $church->id }}";

        const checkContact = debounce(async (contact) => {
            if (contact.length < 5) {
                warningDiv.classList.add('hidden');
                submitBtn.disabled = false;
                submitBtn.style.opacity = '1';
                return;
            }

            try {
                const response = await fetch(`/church-groups/check-contact?contact=${encodeURIComponent(contact)}&exclude_id=${churchId}&exclude_type=church`);
                const data = await response.json();

                if (data.exists) {
                    warningDiv.textContent = `⚠️ This contact belongs to ${data.owner} in ${data.entity}`;
                    warningDiv.classList.remove('hidden');
                    submitBtn.disabled = true;
                    submitBtn.style.opacity = '0.5';
                } else {
                    warningDiv.classList.add('hidden');
                    submitBtn.disabled = false;
                    submitBtn.style.opacity = '1';
                }
            } catch (error) {
                console.error('Error checking contact:', error);
            }
        }, 500);

        contactInput.addEventListener('input', (e) => checkContact(e.target.value));
    </script>
</x-app-layout>