<x-app-layout>
    <x-slot name="header">
        {{ __('Church Groups') }}
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto">
            <!-- Full-width Groups List -->
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

                <div class="mb-6 flex items-center justify-between">
                    <div>
                        <h3 class="text-base font-bold text-slate-900 dark:text-white tracking-tight">Existing Groups</h3>
                        <p class="text-[10px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-widest mt-1">Hierarchical
                            distribution</p>
                    </div>
                    <button type="button"
                        onclick="document.getElementById('createGroupModal').classList.remove('hidden')"
                        class="flex items-center gap-2 px-5 py-2.5 bg-indigo-600 hover:bg-indigo-500 text-white text-[10px] font-black uppercase tracking-[2px] rounded-xl transition-all active:scale-95 shadow-xl shadow-indigo-600/20">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Add New Group
                    </button>
                </div>

                <div class="space-y-4">
                    @forelse($categories as $category)
                        @if($category->churchGroups->count() > 0)
                            <div class="rounded-2xl border border-slate-200 dark:border-white/5 overflow-hidden">
                                <button type="button"
                                    onclick="this.nextElementSibling.classList.toggle('hidden'); this.querySelector('.chevron').classList.toggle('rotate-180')"
                                    class="w-full flex items-center justify-between p-5 bg-slate-100 dark:bg-white/5 hover:bg-slate-200 dark:hover:bg-white/10 transition-all cursor-pointer">
                                    <div class="flex items-center gap-3">
                                        <div
                                            class="w-10 h-10 rounded-lg bg-indigo-600/10 text-indigo-600 dark:text-indigo-400 flex items-center justify-center">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                                            </svg>
                                        </div>
                                        <div>
                                            <h4 class="text-sm font-bold text-slate-900 dark:text-white">{{ $category->name }}</h4>
                                            <span class="text-[10px] text-slate-500 dark:text-slate-400">{{ $category->churchGroups->count() }}
                                                {{ Str::plural('group', $category->churchGroups->count()) }}</span>
                                        </div>
                                    </div>
                                    <svg class="w-4 h-4 text-slate-400 transition-transform duration-200 chevron" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 9l-7 7-7-7" />
                                    </svg>
                                </button>
                                <div class="divide-y divide-slate-200 dark:divide-white/5">
                                    @foreach($category->churchGroups as $group)
                                        <div
                                            class="p-5 flex flex-col sm:flex-row sm:items-center justify-between gap-4 group hover:bg-slate-50 dark:hover:bg-white/5 transition-all">
                                            <div class="flex items-center gap-4">
                                                <div
                                                    class="w-10 h-10 rounded-lg bg-slate-100 dark:bg-white/5 flex items-center justify-center text-indigo-600 dark:text-indigo-400 font-bold text-sm">
                                                    {{ strtoupper(substr($group->group_name, 0, 1)) }}
                                                </div>
                                                <div>
                                                    <h5 class="text-sm font-bold text-slate-900 dark:text-white">{{ $group->group_name }}</h5>
                                                    <div class="flex flex-wrap items-center gap-x-3 gap-y-1 mt-0.5">
                                                        <span class="text-[10px] text-slate-400 flex items-center gap-1">
                                                            <svg class="w-3 h-3" fill="none" stroke="currentColor"
                                                                viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    stroke-width="2"
                                                                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                                            </svg>
                                                            {{ $group->pastor_name }}
                                                        </span>
                                                        @if($group->pastor_contact)
                                                            <span class="text-[10px] text-slate-500 flex items-center gap-1">
                                                                <svg class="w-3 h-3" fill="none" stroke="currentColor"
                                                                    viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                                        stroke-width="2"
                                                                        d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                                                </svg>
                                                                {{ $group->pastor_contact }}
                                                            </span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="flex items-center gap-1">
                                                <a href="{{ route('church-groups.edit', $group) }}"
                                                    class="p-2 text-slate-400 hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                            d="M11 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-5M16.5 3.5a2.121 2.121 0 013 3L7 19l-4 1 1-4L16.5 3.5z" />
                                                    </svg>
                                                </a>
                                                <form action="{{ route('church-groups.destroy', $group) }}" method="POST"
                                                    onsubmit="return confirm('Are you sure?')" class="inline-block">
                                                    @csrf @method('DELETE')
                                                    <button type="submit"
                                                        class="p-2 text-slate-400 hover:text-rose-600 dark:hover:text-rose-500 transition-colors">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                        </svg>
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    @empty
                        <div class="text-center py-10">
                            <p class="text-slate-500 italic">No groups found.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <!-- Create Group Modal -->
    <div id="createGroupModal" class="fixed inset-0 z-[100] overflow-y-auto hidden">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 transition-opacity bg-slate-950/80 backdrop-blur-sm"
                onclick="document.getElementById('createGroupModal').classList.add('hidden')"></div>

            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <div
                class="inline-block w-full max-w-lg overflow-hidden text-left align-middle transition-all transform glass-card relative bg-white dark:bg-slate-900 shadow-2xl rounded-3xl sm:my-8 border border-slate-200 dark:border-white/10 z-10">

                <!-- Modal Header -->
                <div class="px-6 py-4 border-b border-slate-200 dark:border-white/10 flex items-center justify-between bg-slate-50 dark:bg-slate-800">
                    <div>
                        <h3 class="text-lg font-bold text-slate-900 dark:text-white tracking-tight flex items-center gap-2">
                            <span
                                class="w-8 h-8 rounded-lg bg-indigo-600/10 text-indigo-600 dark:text-indigo-400 flex items-center justify-center">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 4v16m8-8H4" />
                                </svg>
                            </span>
                            Add New Group
                        </h3>
                        <p class="text-[10px] font-black text-slate-500 uppercase tracking-widest mt-1 ml-10">Create a
                            new church group</p>
                    </div>
                    <button onclick="document.getElementById('createGroupModal').classList.add('hidden')"
                        class="text-slate-500 hover:text-slate-900 dark:hover:text-white transition-colors p-2 rounded-xl hover:bg-slate-200 dark:hover:bg-slate-700">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <!-- Modal Body -->
                <div class="p-6">
                    <form action="{{ route('church-groups.store') }}" method="POST" class="space-y-6">
                        @csrf
                        <div>
                            <x-input-label for="modal_church_category_id" :value="__('Category')" />
                            <select name="church_category_id" id="modal_church_category_id" required
                                class="block w-full bg-slate-50 dark:bg-slate-950/50 border-slate-200 dark:border-white/10 text-slate-900 dark:text-white focus:border-indigo-500 focus:ring-indigo-500 rounded-xl shadow-sm transition-all duration-300 py-3 px-4">
                                <option value="">Select Category</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}"
                                        data-name="{{ $category->name }}"
                                        {{ old('church_category_id') == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('church_category_id')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="modal_group_name" :value="__('Group Name')" />
                            <x-text-input type="text" name="group_name" id="modal_group_name" placeholder="e.g. Group A"
                                required value="{{ old('group_name') }}" class="block w-full" />
                            <x-input-error :messages="$errors->get('group_name')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="modal_pastor_name" :value="__('Pastor Name')" />
                            <x-text-input type="text" name="pastor_name" id="modal_pastor_name" placeholder="Full Name"
                                required value="{{ old('pastor_name') }}" class="block w-full" />
                            <x-input-error :messages="$errors->get('pastor_name')" class="mt-2" />
                        </div>

                        <div id="modal_pastor_contact_container">
                            <x-input-label for="modal_pastor_contact" :value="__('Pastor Contact')" />
                            <x-text-input type="text" name="pastor_contact" id="modal_pastor_contact"
                                placeholder="Phone Number" value="{{ old('pastor_contact') }}" class="block w-full" />
                            <div id="modal-contact-warning"
                                class="mt-2 text-[11px] font-bold text-rose-500 uppercase tracking-wider hidden italic"></div>
                            <x-input-error :messages="$errors->get('pastor_contact')" class="mt-2" />
                        </div>

                        <div class="flex items-center gap-3 pt-2">
                            <x-secondary-button class="flex-1" onclick="document.getElementById('createGroupModal').classList.add('hidden')">
                                {{ __('Cancel') }}
                            </x-secondary-button>
                            <x-primary-button type="submit" id="modal-submit-btn" class="flex-1 justify-center">
                                {{ __('Create Group') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const categorySelect = document.getElementById('modal_church_category_id');
            const groupNameInput = document.getElementById('modal_group_name');
            const contactContainer = document.getElementById('modal_pastor_contact_container');
            const contactInput = document.getElementById('modal_pastor_contact');
            const contactWarning = document.getElementById('modal-contact-warning');
            const submitBtn = document.getElementById('modal-submit-btn');

            function debounce(func, delay) {
                let timeout;
                return function(...args) {
                    clearTimeout(timeout);
                    timeout = setTimeout(() => func.apply(this, args), delay);
                };
            }

            function togglePastorContact() {
                const selectedOption = categorySelect.options[categorySelect.selectedIndex];
                const categoryName = selectedOption ? selectedOption.getAttribute('data-name') : '';
                const groupName = groupNameInput.value.trim();

                if (categoryName === 'ZONAL CHURCH' || groupName.toUpperCase() === 'ZONAL CHURCH GROUP 1') {
                    contactContainer.style.display = 'none';
                    contactInput.removeAttribute('required');
                    contactWarning.classList.add('hidden');
                    contactWarning.textContent = '';
                } else {
                    contactContainer.style.display = 'block';
                    contactInput.setAttribute('required', 'required');
                    debouncedCheckContact();
                }
            }

            async function checkContact() {
                const contact = contactInput.value.trim();
                contactWarning.classList.add('hidden');
                contactWarning.textContent = '';

                if (contact.length < 5) {
                    submitBtn.disabled = false;
                    submitBtn.style.opacity = '1';
                    return;
                }

                try {
                    const response = await fetch(`/church-groups/check-contact?contact=${encodeURIComponent(contact)}&exclude_type=group`);
                    if (!response.ok) throw new Error('Network response was not ok');

                    const data = await response.json();
                    if (data.exists) {
                        contactWarning.textContent = `⚠️ This contact belongs to ${data.owner} in ${data.entity}`;
                        contactWarning.classList.remove('hidden');
                        submitBtn.disabled = true;
                        submitBtn.style.opacity = '0.5';
                    } else {
                        contactWarning.classList.add('hidden');
                        submitBtn.disabled = false;
                        submitBtn.style.opacity = '1';
                    }
                } catch (error) {
                    console.error('Error checking contact:', error);
                }
            }

            const debouncedCheckContact = debounce(checkContact, 500);

            categorySelect.addEventListener('change', togglePastorContact);
            groupNameInput.addEventListener('input', togglePastorContact);
            contactInput.addEventListener('input', debouncedCheckContact);

            togglePastorContact(); // Initial state

            // Auto-open modal if there are validation errors
            @if($errors->any())
                document.getElementById('createGroupModal').classList.remove('hidden');
            @endif
        });
    </script>
</x-app-layout>