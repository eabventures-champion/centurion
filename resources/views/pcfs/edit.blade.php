<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('pcfs.index') }}"
                class="p-2 text-slate-400 hover:text-slate-900 dark:hover:text-white transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
            </a>
            {{ __('Edit PCF') }}
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-3xl mx-auto px-4">
            <div class="glass-card p-8">
                <div class="mb-8">
                    <h3 class="text-xl font-bold text-slate-900 dark:text-white tracking-tight">Modify PCF Details</h3>
                    <p class="text-[10px] font-bold text-slate-500 uppercase tracking-widest mt-1">Update zonal unit
                        information</p>
                </div>

                <form action="{{ route('pcfs.update', $pcf) }}" method="POST" class="space-y-8">
                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-1 gap-6">
                        <div>
                            <x-input-label for="church_group_id" :value="__('Church Group (Zonal)')" />
                            <select name="church_group_id" id="church_group_id" required
                                class="block w-full bg-slate-50 dark:bg-slate-950/50 border-slate-200 dark:border-white/10 text-slate-900 dark:text-white focus:border-indigo-500 focus:ring-indigo-500 rounded-xl shadow-sm transition-all duration-300 py-3 px-4">
                                @foreach($groups as $group)
                                    <option value="{{ $group->id }}" {{ $pcf->church_group_id == $group->id ? 'selected' : '' }}>{{ $group->group_name }}</option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('church_group_id')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="name" :value="__('PCF Name')" />
                            <x-text-input type="text" name="name" id="name" value="{{ old('name', $pcf->name) }}"
                                required class="block w-full" />
                            <x-input-error :messages="$errors->get('name')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="leader_name" :value="__('Leader Name')" />
                            <x-text-input type="text" name="leader_name" id="leader_name"
                                value="{{ old('leader_name', $pcf->leader_name) }}" required class="block w-full" />
                            <x-input-error :messages="$errors->get('leader_name')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="leader_contact" :value="__('Leader Contact')" />
                            <x-text-input type="text" name="leader_contact" id="leader_contact"
                                value="{{ old('leader_contact', $pcf->leader_contact) }}" required
                                class="block w-full" />
                            <div id="contact-warning"
                                class="mt-2 text-[11px] font-bold text-rose-500 uppercase tracking-wider hidden italic">
                            </div>
                            <x-input-error :messages="$errors->get('leader_contact')" class="mt-2" />
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                            <div>
                                <x-input-label for="gender" :value="__('Gender')" />
                                <select name="gender" id="gender" required
                                    class="block w-full bg-slate-50 dark:bg-slate-950/50 border-slate-200 dark:border-white/10 text-slate-900 dark:text-white focus:border-indigo-500 focus:ring-indigo-500 rounded-xl shadow-sm transition-all duration-300 py-3 px-4">
                                    <option value="Male" {{ old('gender', $pcf->gender) == 'Male' ? 'selected' : '' }}>
                                        Male</option>
                                    <option value="Female" {{ old('gender', $pcf->gender) == 'Female' ? 'selected' : '' }}>Female</option>
                                </select>
                                <x-input-error :messages="$errors->get('gender')" class="mt-2" />
                            </div>
                            <div>
                                <x-input-label for="marital_status" :value="__('Marital Status')" />
                                <select name="marital_status" id="marital_status" required
                                    class="block w-full bg-slate-50 dark:bg-slate-950/50 border-slate-200 dark:border-white/10 text-slate-900 dark:text-white focus:border-indigo-500 focus:ring-indigo-500 rounded-xl shadow-sm transition-all duration-300 py-3 px-4">
                                    @foreach(['Single', 'Married', 'Divorced', 'Widowed'] as $status)
                                        <option value="{{ $status }}" {{ old('marital_status', $pcf->marital_status) == $status ? 'selected' : '' }}>{{ $status }}</option>
                                    @endforeach
                                </select>
                                <x-input-error :messages="$errors->get('marital_status')" class="mt-2" />
                            </div>
                        </div>

                        <div>
                            <x-input-label for="occupation" :value="__('Occupation')" />
                            <x-text-input type="text" name="occupation" id="occupation"
                                value="{{ old('occupation', $pcf->occupation) }}" required class="block w-full" />
                            <x-input-error :messages="$errors->get('occupation')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="official_id" :value="__('Official In Charge')" />
                            <select name="official_id" id="official_id" required
                                class="block w-full bg-slate-50 dark:bg-slate-950/50 border-slate-200 dark:border-white/10 text-slate-900 dark:text-white focus:border-indigo-500 focus:ring-indigo-500 rounded-xl shadow-sm transition-all duration-300 py-3 px-4">
                                @foreach($officials as $official)
                                    <option value="{{ $official->id }}" {{ old('official_id', $pcf->official_id) == $official->id ? 'selected' : '' }}>{{ $official->name }}
                                    </option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('official_id')" class="mt-2" />
                        </div>
                    </div>

                    <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-200 dark:border-white/5">
                        <x-secondary-button href="{{ route('pcfs.index') }}" tag="a">
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
        const pcfId = "{{ $pcf->id }}";

        const checkContact = debounce(async (contact) => {
            if (contact.length < 5) {
                warningDiv.classList.add('hidden');
                submitBtn.disabled = false;
                submitBtn.style.opacity = '1';
                return;
            }

            try {
                const response = await fetch(`/church-groups/check-contact?contact=${encodeURIComponent(contact)}&exclude_id=${pcfId}&exclude_type=pcf`);
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