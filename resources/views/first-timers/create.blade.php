<x-app-layout>
    <x-slot name="header">
        <!-- <h2 class="font-semibold text-xl text-gray-800 leading-tight"> -->
        {{ __('Register New First Timer') }}
        <!-- </h2> -->
    </x-slot>

    <div class="py-6">
        <div class="max-w-4xl mx-auto">
            <div class="glass-card p-6 md:p-10">
                <form action="{{ route('first-timers.store') }}" method="POST" class="space-y-8">
                    @csrf

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <!-- Group & Assignment Selection -->
                        @if(auth()->user()->hasRole('Super Admin'))
                            <div
                                class="md:col-span-2 grid grid-cols-1 md:grid-cols-2 gap-6 p-6 bg-slate-50 dark:bg-slate-900/50 rounded-2xl border border-slate-200 dark:border-white/5">
                                <div>
                                    <label for="church_group_id"
                                        class="block text-[11px] font-black text-slate-500 uppercase tracking-[2px] mb-2">Church
                                        Group</label>
                                    <select id="church_group_id"
                                        class="block w-full bg-white dark:bg-slate-950/50 border-slate-200 dark:border-slate-700/50 text-slate-900 dark:text-white focus:border-indigo-500 focus:ring-indigo-500 rounded-xl shadow-sm transition-all"
                                        onchange="handleGroupChange(this)" required>
                                        <option value="">Select Group</option>
                                        @foreach($churchGroups as $group)
                                            <option value="{{ $group->id }}"
                                                data-category="{{ strtoupper($group->churchCategory->name) }}">
                                                {{ $group->group_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div>
                                    <label for="assignment_id" id="assignment_label"
                                        class="block text-[11px] font-black text-slate-500 uppercase tracking-[2px] mb-2">Assign
                                        to PCF</label>
                                    <select id="assignment_id"
                                        class="block w-full bg-white dark:bg-slate-950/50 border-slate-200 dark:border-slate-700/50 text-slate-900 dark:text-white focus:border-indigo-500 focus:ring-indigo-500 rounded-xl shadow-sm transition-all"
                                        required disabled>
                                        <option value="">Select PCF</option>
                                    </select>
                                    <div id="assignment_error">
                                        <x-input-error :messages="$errors->get('pcf_id')" class="mt-2" />
                                        <x-input-error :messages="$errors->get('church_id')" class="mt-2" />
                                    </div>
                                </div>
                            </div>
                        @else
                            {{-- Admin: auto-assign to their church --}}
                            <input type="hidden" name="church_id" value="{{ auth()->user()->church()?->id }}">
                        @endif

                        <!-- Full Name -->
                        <div>
                            <x-input-label for="full_name" :value="__('Full Name')" />
                            <x-text-input id="full_name" name="full_name" type="text" class="block w-full"
                                value="{{ old('full_name') }}" required />
                            <x-input-error :messages="$errors->get('full_name')" class="mt-2" />
                        </div>

                        <!-- Email -->
                        <div>
                            <x-input-label for="email" :value="__('Email Address')" />
                            <x-text-input id="email" name="email" type="email" class="block w-full"
                                value="{{ old('email') }}" />
                            <x-input-error :messages="$errors->get('email')" class="mt-2" />
                        </div>

                        <!-- Primary Contact -->
                        <div>
                            <x-input-label for="primary_contact" :value="__('Primary Contact')" />
                            <x-text-input id="primary_contact" name="primary_contact" type="text" class="block w-full"
                                value="{{ old('primary_contact') }}" required />
                            <div id="contact-check-status" class="mt-2 h-4 text-[10px] font-bold"></div>
                            <x-input-error :messages="$errors->get('primary_contact')" class="mt-2" />
                        </div>

                        <!-- Alternate Contact -->
                        <div>
                            <x-input-label for="alternate_contact" :value="__('Alternate Contact')" />
                            <x-text-input id="alternate_contact" name="alternate_contact" type="text"
                                class="block w-full" value="{{ old('alternate_contact') }}" />
                            <x-input-error :messages="$errors->get('alternate_contact')" class="mt-2" />
                        </div>

                        <!-- Gender -->
                        <div>
                            <label for="gender"
                                class="block text-[11px] font-black text-slate-500 uppercase tracking-[2px] mb-2">Gender</label>
                            <select name="gender" id="gender"
                                class="block w-full bg-white dark:bg-slate-900/50 border-slate-200 dark:border-slate-700/50 text-slate-900 dark:text-white focus:border-indigo-500 focus:ring-indigo-500 rounded-xl shadow-sm transition-all">
                                <option value="Male">Male</option>
                                <option value="Female">Female</option>
                            </select>
                            <x-input-error :messages="$errors->get('gender')" class="mt-2" />
                        </div>

                        <!-- Date of Visit -->
                        <div>
                            <x-input-label for="date_of_visit" :value="__('Date of Visit')" />
                            <x-text-input id="date_of_visit" name="date_of_visit" type="date" class="block w-full"
                                value="{{ old('date_of_visit', date('Y-m-d')) }}" required />
                            <x-input-error :messages="$errors->get('date_of_visit')" class="mt-2" />
                        </div>

                        <!-- Status -->
                        <div>
                            <label for="marital_status"
                                class="block text-[11px] font-black text-slate-500 uppercase tracking-[2px] mb-2">Marital
                                Status</label>
                            <select name="marital_status" id="marital_status"
                                class="block w-full bg-white dark:bg-slate-900/50 border-slate-200 dark:border-slate-700/50 text-slate-900 dark:text-white focus:border-indigo-500 focus:ring-indigo-500 rounded-xl shadow-sm transition-all shadow-sm transition-all cursor-pointer">
                                <option value="Single" {{ old('marital_status') == 'Single' ? 'selected' : '' }}>Single
                                </option>
                                <option value="Married" {{ old('marital_status') == 'Married' ? 'selected' : '' }}>Married
                                </option>
                                <option value="Widowed" {{ old('marital_status') == 'Widowed' ? 'selected' : '' }}>Widowed
                                </option>
                                <option value="Divorced" {{ old('marital_status') == 'Divorced' ? 'selected' : '' }}>
                                    Divorced</option>
                            </select>
                            <x-input-error :messages="$errors->get('marital_status')" class="mt-2" />
                        </div>

                        <!-- Birthday -->
                        <div class="md:col-span-1">
                            <label
                                class="block text-[11px] font-black text-slate-500 uppercase tracking-[2px] mb-2">Birth
                                Day (Day/Month)</label>
                            <div class="grid grid-cols-2 gap-2">
                                <select name="birth_day_day"
                                    class="block w-full bg-white dark:bg-slate-900/50 border-slate-200 dark:border-slate-700/50 text-slate-900 dark:text-white focus:border-indigo-500 focus:ring-indigo-500 rounded-xl shadow-sm transition-all">
                                    <option value="">Day</option>
                                    @for($i = 1; $i <= 31; $i++)
                                        <option value="{{ sprintf('%02d', $i) }}">{{ $i }}</option>
                                    @endfor
                                </select>
                                <select name="birth_day_month"
                                    class="block w-full bg-white dark:bg-slate-900/50 border-slate-200 dark:border-slate-700/50 text-slate-900 dark:text-white focus:border-indigo-500 focus:ring-indigo-500 rounded-xl shadow-sm transition-all">
                                    <option value="">Month</option>
                                    @foreach(['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'] as $index => $month)
                                        <option value="{{ sprintf('%02d', $index + 1) }}">{{ $month }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <!-- Occupation -->
                        <div>
                            <x-input-label for="occupation" :value="__('Occupation')" />
                            <x-text-input id="occupation" name="occupation" type="text" class="block w-full"
                                value="{{ old('occupation') }}" placeholder="e.g. Engineer" />
                            <x-input-error :messages="$errors->get('occupation')" class="mt-2" />
                        </div>

                        <!-- Address -->
                        <div class="md:col-span-2">
                            <label for="residential_address"
                                class="block text-[11px] font-black text-slate-500 uppercase tracking-[2px] mb-2">Residential
                                Address</label>
                            <textarea id="residential_address" name="residential_address"
                                class="block w-full bg-white dark:bg-slate-900/50 border-slate-200 dark:border-slate-700/50 text-slate-900 dark:text-white focus:border-indigo-500 focus:ring-indigo-500 rounded-xl shadow-sm transition-all"
                                rows="2" required>{{ old('residential_address') }}</textarea>
                            <x-input-error :messages="$errors->get('residential_address')" class="mt-2" />
                        </div>

                        <!-- Spiritual Status & Prayer Requests -->
                        <div
                            class="md:col-span-2 grid grid-cols-1 md:grid-cols-2 gap-6 p-6 bg-slate-50 dark:bg-slate-900/30 rounded-2xl border border-slate-200 dark:border-white/5 mt-4">
                            <div class="space-y-4">
                                <h4 class="text-xs font-black text-indigo-400 uppercase tracking-[2px] mb-4">Spiritual
                                    Status</h4>
                                <div class="flex items-center gap-6">
                                    <label class="flex items-center gap-2 cursor-pointer group">
                                        <input type="checkbox" name="born_again" value="1" {{ old('born_again') ? 'checked' : '' }}
                                            class="rounded-md border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900 text-indigo-600 focus:ring-indigo-500 transition-all">
                                        <span
                                            class="text-xs font-bold text-slate-500 dark:text-slate-400 group-hover:text-indigo-500">Born
                                            Again</span>
                                    </label>
                                    <label class="flex items-center gap-2 cursor-pointer group">
                                        <input type="checkbox" name="water_baptism" value="1" {{ old('water_baptism') ? 'checked' : '' }}
                                            class="rounded-md border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900 text-indigo-600 focus:ring-indigo-500 transition-all">
                                        <span
                                            class="text-xs font-bold text-slate-500 dark:text-slate-400 group-hover:text-indigo-500">Water
                                            Baptism</span>
                                    </label>
                                </div>
                            </div>
                            <div>
                                <h4 class="text-xs font-black text-indigo-400 uppercase tracking-[2px] mb-4">Prayer
                                    Requests</h4>
                                <textarea name="prayer_requests"
                                    class="block w-full bg-white dark:bg-slate-950/50 border-slate-200 dark:border-slate-700/50 text-slate-900 dark:text-white focus:border-indigo-500 focus:ring-indigo-500 rounded-xl shadow-sm transition-all text-xs"
                                    rows="3"
                                    placeholder="Enter any specific prayer requests...">{{ old('prayer_requests') }}</textarea>
                                <x-input-error :messages="$errors->get('prayer_requests')" class="mt-2" />
                            </div>
                        </div>

                        <!-- Bringer Validation Logic -->
                        <div class="md:col-span-2 border-t border-slate-200 dark:border-slate-800 pt-8 mt-4">
                            <div class="flex items-center gap-3 mb-6">
                                <h4 class="text-xs font-black text-indigo-400 uppercase tracking-[2px]">Bringer
                                    Information</h4>
                                <div class="h-px flex-1 bg-slate-100 dark:bg-slate-800"></div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label
                                        class="block text-[11px] font-black text-slate-500 uppercase tracking-[2px] mb-2">Find
                                        Existing Bringer</label>
                                    <div class="flex flex-col sm:flex-row gap-2">
                                        <input type="text" id="bringer_contact_check"
                                            class="block w-full bg-white dark:bg-slate-950/50 border-slate-200 dark:border-slate-700/50 text-slate-900 dark:text-white focus:border-indigo-500 focus:ring-indigo-500 rounded-xl sm:rounded-r-none shadow-sm transition-all"
                                            placeholder="Phone number...">
                                        <button type="button" onclick="checkBringer()"
                                            class="px-6 py-3 sm:py-2 bg-indigo-600 text-white font-bold text-xs uppercase tracking-widest rounded-xl sm:rounded-l-none hover:bg-indigo-500 transition-all shadow-lg shadow-indigo-600/20">Check</button>
                                    </div>
                                    <p id="bringer-status" class="mt-2 text-[10px] font-bold h-4"></p>
                                </div>
                                <div id="new-bringer-fields" style="display:none">
                                    <label for="new_bringer_name"
                                        class="block text-[11px] font-black text-slate-500 uppercase tracking-[2px] mb-2">New
                                        Bringer Name</label>
                                    <input id="new_bringer_name" name="new_bringer[name]" type="text"
                                        class="block w-full bg-white dark:bg-slate-950/50 border-slate-200 dark:border-slate-700/50 text-slate-900 dark:text-white focus:border-indigo-500 focus:ring-indigo-500 rounded-xl shadow-sm transition-all"
                                        placeholder="Enter full name" oninput="toggleSelfBrought()" />
                                    <input type="hidden" name="new_bringer[contact]" id="new_bringer_contact">

                                    <div class="grid grid-cols-2 gap-4 mt-4">
                                        <div>
                                            <label for="new_bringer_senior_cell"
                                                class="block text-[11px] font-black text-slate-500 uppercase tracking-[2px] mb-2">Senior
                                                Cell Name</label>
                                            <input id="new_bringer_senior_cell" name="new_bringer[senior_cell_name]"
                                                type="text"
                                                class="block w-full bg-white dark:bg-slate-950/50 border-slate-200 dark:border-slate-700/50 text-slate-900 dark:text-white focus:border-indigo-500 focus:ring-indigo-500 rounded-xl shadow-sm transition-all"
                                                placeholder="e.g. Grace Senior Cell" />
                                        </div>
                                        <div>
                                            <label for="new_bringer_cell"
                                                class="block text-[11px] font-black text-slate-500 uppercase tracking-[2px] mb-2">Cell
                                                Name</label>
                                            <input id="new_bringer_cell" name="new_bringer[cell_name]" type="text"
                                                class="block w-full bg-white dark:bg-slate-950/50 border-slate-200 dark:border-slate-700/50 text-slate-900 dark:text-white focus:border-indigo-500 focus:ring-indigo-500 rounded-xl shadow-sm transition-all"
                                                placeholder="e.g. Life Cell" />
                                        </div>
                                    </div>
                                </div>
                                <input type="hidden" name="bringer_id" id="bringer_id">
                            </div>
                            <div
                                class="mt-6 flex items-center gap-2 px-4 py-3 bg-indigo-500/5 border border-indigo-500/10 rounded-xl">
                                <input type="checkbox" name="is_self_brought" id="is_self_brought" value="1"
                                    class="rounded-md border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900 text-indigo-600 focus:ring-indigo-500 transition-all">
                                <label for="is_self_brought"
                                    class="text-xs font-bold text-slate-500 dark:text-slate-400 cursor-pointer">Self
                                    Brought (First Timer is
                                    the
                                    bringer)</label>
                            </div>
                        </div>
                    </div> <!-- Added closing div for strictly closing the grid -->

                    <div
                        class="flex items-center justify-end gap-4 w-full pt-8 mt-4 border-t border-slate-200 dark:border-slate-800">
                        <a href="{{ route('first-timers.index') }}"
                            class="inline-flex items-center px-6 py-3 bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl font-bold text-xs text-slate-600 dark:text-slate-300 uppercase tracking-widest hover:bg-slate-200 dark:hover:bg-slate-700 hover:text-slate-900 dark:hover:text-white transition-all active:scale-95">
                            {{ __('Cancel') }}
                        </a>
                        <x-primary-button id="submit-button"
                            class="shadow-xl shadow-indigo-600/20 disabled:opacity-50 disabled:cursor-not-allowed">
                            {{ __('Register') }}
                        </x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            async function checkBringer() {
                const contact = document.getElementById('bringer_contact_check').value;
                const status = document.getElementById('bringer-status');
                const newFields = document.getElementById('new-bringer-fields');
                const bringerIdInput = document.getElementById('bringer_id');

                if (!contact) return;

                status.innerText = 'Checking...';
                status.className = 'mt-1 text-xs text-blue-600';

                try {
                    const response = await fetch(`{{ route('bringers.check') }}?contact=${contact}`);
                    const data = await response.json();

                    if (data.exists) {
                        status.innerHTML = `Bringer contact already exists: ${data.name} <span class="ml-2 px-2 py-0.5 bg-amber-100/10 text-amber-500 border border-amber-500/20 rounded-md text-[9px] font-black uppercase tracking-wider">${data.fellowship}</span>`;
                        status.className = 'mt-1 text-xs text-green-600 flex items-center';
                        bringerIdInput.value = data.id;
                        newFields.style.display = 'none';
                        Array.from(newFields.querySelectorAll('input')).forEach(el => el.disabled = true);
                    } else {
                        status.innerText = 'Bringer not found. Please enter name.';
                        status.className = 'mt-1 text-xs text-amber-600';
                        bringerIdInput.value = '';
                        newFields.style.display = 'block';
                        Array.from(newFields.querySelectorAll('input')).forEach(el => el.disabled = false);
                        document.getElementById('new_bringer_contact').value = contact;
                    }
                    toggleSelfBrought();
                } catch (e) {
                    status.innerText = 'Error checking bringer.';
                    status.className = 'mt-1 text-xs text-red-600';
                }
            }

            document.addEventListener('DOMContentLoaded', () => {
                const selfBroughtInput = document.getElementById('is_self_brought');
                if (selfBroughtInput) {
                    selfBroughtInput.addEventListener('change', function (e) {
                        const checkSection = document.getElementById('bringer_contact_check').closest('div');
                        const newFields = document.getElementById('new-bringer-fields');
                        const checkInput = document.getElementById('bringer_contact_check');
                        const checkButton = checkInput.nextElementSibling;

                        if (e.target.checked) {
                            checkSection.style.opacity = '0.5';
                            checkInput.disabled = true;
                            checkButton.disabled = true;
                            newFields.style.display = 'none';
                            Array.from(newFields.querySelectorAll('input')).forEach(el => el.disabled = true);
                            // Clear bringer info if self-brought is checked
                            document.getElementById('bringer_id').value = '';
                            document.getElementById('new_bringer_name').value = '';
                            document.getElementById('new_bringer_contact').value = '';
                            document.getElementById('new_bringer_senior_cell').value = '';
                            document.getElementById('new_bringer_cell').value = '';
                        } else {
                            checkSection.style.opacity = '1';
                            checkInput.disabled = false;
                            checkButton.disabled = false;
                        }
                    });
                }
            });

            function toggleSelfBrought() {
                const selfBroughtCheckbox = document.getElementById('is_self_brought');
                const bringerId = document.getElementById('bringer_id').value;
                const newBringerName = document.getElementById('new_bringer_name').value;
                const searchInput = document.getElementById('bringer_contact_check').value;

                if (bringerId || newBringerName || (searchInput && document.getElementById('bringer-status').innerText.includes('Matched'))) {
                    selfBroughtCheckbox.disabled = true;
                    selfBroughtCheckbox.closest('div').style.opacity = '0.5';
                } else {
                    selfBroughtCheckbox.disabled = false;
                    selfBroughtCheckbox.closest('div').style.opacity = '1';
                }
            }

            async function handleGroupChange(select) {
                const groupId = select.value;
                if (!groupId) return;

                const option = select.querySelector(`option[value="${groupId}"]`);
                const category = option ? (option.getAttribute('data-category') || '') : '';

                const assignmentLabel = document.getElementById('assignment_label');
                const assignmentSelect = document.getElementById('assignment_id');

                // Determine if it's Zonal or Other
                const isZonal = category.includes('ZONAL');

                // Update Labels and Name attribute
                if (isZonal) {
                    assignmentLabel.innerText = 'Assign to PCF';
                    assignmentSelect.name = 'pcf_id';
                    assignmentSelect.innerHTML = '<option value="">Loading PCFs...</option>';
                } else {
                    assignmentLabel.innerText = 'Assign to Church';
                    assignmentSelect.name = 'church_id';
                    assignmentSelect.innerHTML = '<option value="">Loading Churches...</option>';
                }

                assignmentSelect.disabled = true;

                if (!groupId) {
                    assignmentSelect.innerHTML = isZonal ? '<option value="">Select PCF</option>' : '<option value="">Select Church</option>';
                    return;
                }

                try {
                    const pcfUrlTemplate = "{{ route('church-groups.pcfs', ':id') }}";
                    const churchUrlTemplate = "{{ route('church-groups.churches', ':id') }}";
                    const endpoint = (isZonal ? pcfUrlTemplate : churchUrlTemplate).replace(':id', groupId);

                    const response = await fetch(endpoint);
                    const items = await response.json();

                    assignmentSelect.innerHTML = isZonal ? '<option value="">Select PCF</option>' : '<option value="">Select Church</option>';

                    items.forEach(item => {
                        const opt = document.createElement('option');
                        opt.value = item.id;
                        opt.textContent = item.name;
                        assignmentSelect.appendChild(opt);
                    });

                    assignmentSelect.disabled = false;
                } catch (e) {
                    console.error('Error fetching assignment items:', e);
                    assignmentSelect.innerHTML = '<option value="">Error loading items</option>';
                }
            }

            function debounce(func, wait) {
                let timeout;
                return function (...args) {
                    clearTimeout(timeout);
                    timeout = setTimeout(() => func.apply(this, args), wait);
                };
            }

            document.addEventListener('DOMContentLoaded', () => {
                console.info('First Timer Contact Check Initialized');
                const contactInput = document.getElementById('primary_contact');
                if (contactInput) {
                    console.info('Found primary_contact input, attaching listener');
                    contactInput.addEventListener('input', debounce(function () {
                        console.info('Input detected, value:', this.value);
                        checkDuplicateContact(this.value);
                    }, 400));
                } else {
                    console.error('primary_contact input NOT found');
                }
            });

            function checkDuplicateContact(contactValue) {
                const status = document.getElementById('contact-check-status');
                const submitBtn = document.getElementById('submit-button');

                if (!status) return;

                const contact = contactValue.trim();
                const numericOnly = contact.replace(/[^0-9]/g, '');

                if (!contact) {
                    status.innerText = '';
                    status.className = 'mt-2 h-4 text-[10px] font-bold';
                    if (submitBtn) submitBtn.disabled = false;
                    return;
                }

                if (numericOnly.length < 5) {
                    status.innerText = 'Contact too short...';
                    status.className = 'mt-2 h-4 text-[10px] font-bold text-amber-500 uppercase tracking-wider';
                    if (submitBtn) submitBtn.disabled = true;
                    return;
                }

                status.innerText = 'Validating...';
                status.className = 'mt-2 h-4 text-[10px] font-bold text-blue-400 uppercase tracking-wider animate-pulse';
                if (submitBtn) submitBtn.disabled = true;

                fetch(`/church-groups/check-contact?contact=${encodeURIComponent(contact)}&exclude_type=visitor`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.exists) {
                            status.innerText = `⚠️ DUPLICATE: Used by ${data.owner} (${data.entity})`;
                            status.className = 'mt-2 h-4 text-[10px] font-bold text-rose-500 uppercase tracking-wider';
                            if (submitBtn) submitBtn.disabled = true;
                        } else {
                            status.innerText = '✓ Unique Contact';
                            status.className = 'mt-2 h-4 text-[10px] font-bold text-emerald-500 uppercase tracking-wider';
                            if (submitBtn) submitBtn.disabled = false;
                        }
                    })
                    .catch(err => {
                        console.error('Contact check error:', err);
                        status.innerText = 'Connectivity error - please try again';
                        if (submitBtn) submitBtn.disabled = false;
                    })
                    .finally(() => {
                        status.classList.remove('animate-pulse');
                    });
            }

        </script>
    @endpush
</x-app-layout>
```