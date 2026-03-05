<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('church-groups.index') }}" class="p-2 text-slate-400 hover:text-slate-900 dark:hover:text-white transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
            </a>
            {{ __('Edit Church Group') }}
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-3xl mx-auto px-4">
            <div class="glass-card p-8">
                <div class="mb-8">
                    <h3 class="text-xl font-bold text-slate-900 dark:text-white tracking-tight">Modify Group</h3>
                    <p class="text-[10px] font-bold text-slate-500 uppercase tracking-widest mt-1">Update hierarchical
                        distribution details</p>
                </div>

                <form action="{{ route('church-groups.update', $churchGroup) }}" method="POST" class="space-y-8">
                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-1 gap-6">
                        <div>
                            <x-input-label for="church_category_id" :value="__('Category')" />
                            <select name="church_category_id" id="church_category_id" required
                                class="block w-full bg-slate-50 dark:bg-slate-950/50 border-slate-200 dark:border-white/10 text-slate-900 dark:text-white focus:border-indigo-500 focus:ring-indigo-500 rounded-xl shadow-sm transition-all duration-300 py-3 px-4">
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" 
                                            data-name="{{ $category->name }}"
                                            {{ old('church_category_id', $churchGroup->church_category_id) == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('church_category_id')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="group_name" :value="__('Group Name')" />
                            <x-text-input type="text" name="group_name" id="group_name"
                                value="{{ old('group_name', $churchGroup->group_name) }}" required
                                class="block w-full" />
                            <x-input-error :messages="$errors->get('group_name')" class="mt-2" />
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <x-input-label for="pastor_name" :value="__('Pastor Name')" />
                                <x-text-input type="text" name="pastor_name" id="pastor_name"
                                    value="{{ old('pastor_name', $churchGroup->pastor_name) }}" required
                                    class="block w-full" />
                                <x-input-error :messages="$errors->get('pastor_name')" class="mt-2" />
                            </div>

                            <div id="pastor_contact_container">
                                <x-input-label for="pastor_contact" :value="__('Pastor Contact')" />
                                <x-text-input type="text" name="pastor_contact" id="pastor_contact"
                                    value="{{ old('pastor_contact', $churchGroup->pastor_contact) }}"
                                    class="block w-full" />
                                <div id="pastor_contact_warning" class="mt-2 text-[11px] font-bold text-rose-500 uppercase tracking-wider hidden leading-tight"></div>
                                <x-input-error :messages="$errors->get('pastor_contact')" class="mt-2" />
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-200 dark:border-white/5">
                        <x-secondary-button href="{{ route('church-groups.index') }}" tag="a">
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

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const categorySelect = document.getElementById('church_category_id');
            const groupNameInput = document.getElementById('group_name');
            const contactContainer = document.getElementById('pastor_contact_container');
            const contactInput = document.getElementById('pastor_contact');
            const pastorContactWarning = document.getElementById('pastor_contact_warning');

            function debounce(func, delay) {
                let timeout;
                return function(...args) {
                    const context = this;
                    clearTimeout(timeout);
                    timeout = setTimeout(() => func.apply(context, args), delay);
                };
            }

            function togglePastorContact() {
                const selectedOption = categorySelect.options[categorySelect.selectedIndex];
                const categoryName = selectedOption ? selectedOption.getAttribute('data-name') : '';
                const groupName = groupNameInput.value.trim();

                if (categoryName === 'ZONAL CHURCH' || groupName.toUpperCase() === 'ZONAL CHURCH GROUP 1') {
                    contactContainer.style.display = 'none';
                    contactInput.removeAttribute('required');
                    pastorContactWarning.style.display = 'none';
                    pastorContactWarning.textContent = '';
                } else {
                    contactContainer.style.display = 'block';
                    contactInput.setAttribute('required', 'required');
                    debouncedCheckPastorContact();
                }
            }

            async function checkPastorContact() {
                const contact = contactInput.value.trim();
                pastorContactWarning.style.display = 'none';
                pastorContactWarning.textContent = '';

                if (contactContainer.style.display === 'block' && contact.length > 5) {
                    try {
                        const response = await fetch(`/church-groups/check-contact?contact=${encodeURIComponent(contact)}&exclude_id={{ $churchGroup->id }}&exclude_type=group`);
                        if (!response.ok) throw new Error('Network response was not ok');
                        
                        const data = await response.json();
                        if (data.exists) {
                            pastorContactWarning.textContent = `⚠️ This contact belongs to ${data.owner} in ${data.entity}.`;
                            pastorContactWarning.style.display = 'block';
                        }
                    } catch (error) {
                        console.error('Error checking contact:', error);
                    }
                }
            }

            const debouncedCheckPastorContact = debounce(checkPastorContact, 500);

            categorySelect.addEventListener('change', togglePastorContact);
            groupNameInput.addEventListener('input', togglePastorContact);
            contactInput.addEventListener('input', debouncedCheckPastorContact);

            togglePastorContact(); // Initial state
        });
    </script>
</x-app-layout>
