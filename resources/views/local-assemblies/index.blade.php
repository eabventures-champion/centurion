<x-app-layout>
    <x-slot name="header">
        {{ __('Church names') }}
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto">
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
                        <h3 class="text-base font-bold text-slate-900 dark:text-white tracking-tight">Church names
                        </h3>
                        <p
                            class="text-[10px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-widest mt-1">
                            Manage church names for selection</p>
                    </div>
                    <button type="button"
                        onclick="document.getElementById('createAssemblyModal').classList.remove('hidden')"
                        class="flex items-center gap-2 px-5 py-2.5 bg-indigo-600 hover:bg-indigo-500 text-white text-[10px] font-black uppercase tracking-[2px] rounded-xl transition-all active:scale-95 shadow-xl shadow-indigo-600/20">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Add Church
                    </button>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @forelse($assemblies as $assembly)
                        <div
                            class="p-4 rounded-2xl bg-slate-50 dark:bg-white/5 border border-slate-200 dark:border-white/5 flex items-center justify-between group hover:border-indigo-500/30 transition-all">
                            <div class="flex items-center gap-3">
                                <div
                                    class="w-8 h-8 rounded-lg bg-indigo-600/10 text-indigo-600 dark:text-indigo-400 flex items-center justify-center font-bold text-xs">
                                    {{ substr($assembly->name, 0, 1) }}
                                </div>
                                <div class="flex flex-col">
                                    <span
                                        class="text-sm font-bold text-slate-700 dark:text-slate-200">{{ $assembly->name }}</span>
                                    <span class="text-[10px] text-slate-500 font-medium">Group:
                                        {{ $assembly->churchGroup->group_name ?? 'N/A' }}</span>
                                </div>
                            </div>
                            <div class="flex items-center gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                                <button type="button"
                                    onclick="openEditModal({{ $assembly->id }}, '{{ addslashes($assembly->name) }}', {{ $assembly->church_group_id ?? 'null' }})"
                                    class="p-1.5 text-slate-400 hover:text-indigo-500 transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M11 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-5M16.5 3.5a2.121 2.121 0 013 3L7 19l-4 1 1-4L16.5 3.5z" />
                                    </svg>
                                </button>
                                <form action="{{ route('local-assemblies.destroy', $assembly) }}" method="POST"
                                    onsubmit="return confirm('Are you sure?')">
                                    @csrf @method('DELETE')
                                    <button type="submit"
                                        class="p-1.5 text-slate-400 hover:text-rose-500 transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        </div>
                    @empty
                        <div class="col-span-full text-center py-10">
                            <p class="text-slate-500 italic text-sm">No church names added yet.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <!-- Create Modal -->
    <div id="createAssemblyModal" class="fixed inset-0 z-[100] overflow-y-auto hidden">
        <div class="flex items-center justify-center min-h-screen px-4">
            <div class="fixed inset-0 bg-slate-950/80 backdrop-blur-sm"
                onclick="this.parentElement.parentElement.classList.add('hidden')"></div>
            <div
                class="relative w-full max-w-md p-6 glass-card bg-white dark:bg-slate-900 rounded-3xl shadow-2xl border border-white/10">
                <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-4">Add Church name</h3>
                <form action="{{ route('local-assemblies.store') }}" method="POST">
                    @csrf
                    <div class="mb-4">
                        <x-input-label for="church_group_id" :value="__('Church Group')" />
                        <select name="church_group_id" id="church_group_id" required
                            class="block w-full mt-1 bg-slate-50 dark:bg-slate-950/50 border-slate-200 dark:border-white/10 text-slate-900 dark:text-white rounded-xl py-2.5 px-4">
                            <option value="">Select Group</option>
                            @foreach($groups as $group)
                                <option value="{{ $group->id }}">{{ $group->group_name }}</option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('church_group_id')" class="mt-2" />
                    </div>
                    <div class="mb-4">
                        <x-input-label for="name" :value="__('Church Name')" />
                        <x-text-input type="text" name="name" id="name" required class="block w-full mt-1"
                            placeholder="e.g. CE Atomic" />
                        <div id="name-warning" class="mt-2 text-[11px] font-bold text-rose-500 uppercase tracking-wider hidden italic">
                        </div>
                        <x-input-error :messages="$errors->get('name')" class="mt-2" />
                    </div>
                    <div class="flex gap-3">
                        <x-secondary-button type="button" class="flex-1 justify-center"
                            onclick="document.getElementById('createAssemblyModal').classList.add('hidden')">Cancel</x-secondary-button>
                        <x-primary-button id="submit-btn" class="flex-1 justify-center">Create</x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Modal -->
    <div id="editAssemblyModal" class="fixed inset-0 z-[100] overflow-y-auto hidden">
        <div class="flex items-center justify-center min-h-screen px-4">
            <div class="fixed inset-0 bg-slate-950/80 backdrop-blur-sm"
                onclick="this.parentElement.parentElement.classList.add('hidden')"></div>
            <div
                class="relative w-full max-w-md p-6 glass-card bg-white dark:bg-slate-900 rounded-3xl shadow-2xl border border-white/10">
                <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-4">Edit Church name</h3>
                <form id="editForm" method="POST">
                    @csrf @method('PUT')
                    <div class="mb-4">
                        <x-input-label for="edit_church_group_id" :value="__('Church Group')" />
                        <select name="church_group_id" id="edit_church_group_id" required
                            class="block w-full mt-1 bg-slate-50 dark:bg-slate-950/50 border-slate-200 dark:border-white/10 text-slate-900 dark:text-white rounded-xl py-2.5 px-4">
                            <option value="">Select Group</option>
                            @foreach($groups as $group)
                                <option value="{{ $group->id }}">{{ $group->group_name }}</option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('church_group_id')" class="mt-2" />
                    </div>
                    <div class="mb-4">
                        <x-input-label for="edit_name" :value="__('Church Name')" />
                        <x-text-input type="text" name="name" id="edit_name" required class="block w-full mt-1" />
                        <div id="edit-name-warning" class="mt-2 text-[11px] font-bold text-rose-500 uppercase tracking-wider hidden italic">
                        </div>
                        <x-input-error :messages="$errors->get('name')" class="mt-2" />
                    </div>
                    <div class="flex gap-3">
                        <x-secondary-button type="button" class="flex-1 justify-center"
                            onclick="document.getElementById('editAssemblyModal').classList.add('hidden')">Cancel</x-secondary-button>
                        <x-primary-button id="edit-submit-btn" class="flex-1 justify-center">Update</x-primary-button>
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

        async function checkDuplicateName(name, warningDiv, submitBtn, originalName = null) {
            if (!name || name === originalName) {
                warningDiv.classList.add('hidden');
                submitBtn.disabled = false;
                submitBtn.style.opacity = '1';
                return;
            }

            try {
                const response = await fetch(`/local-assemblies/check-name?name=${encodeURIComponent(name)}`);
                const data = await response.json();

                if (data.exists) {
                    warningDiv.textContent = `⚠️ The name "${name}" already exists!`;
                    warningDiv.classList.remove('hidden');
                    submitBtn.disabled = true;
                    submitBtn.style.opacity = '0.5';
                } else {
                    warningDiv.classList.add('hidden');
                    submitBtn.disabled = false;
                    submitBtn.style.opacity = '1';
                }
            } catch (error) {
                console.error('Error checking name:', error);
            }
        }

        const nameInput = document.getElementById('name');
        const nameWarning = document.getElementById('name-warning');
        const submitBtn = document.getElementById('submit-btn');

        nameInput.addEventListener('input', debounce((e) => {
            checkDuplicateName(e.target.value, nameWarning, submitBtn);
        }, 500));

        const editNameInput = document.getElementById('edit_name');
        const editNameWarning = document.getElementById('edit-name-warning');
        const editSubmitBtn = document.getElementById('edit-submit-btn');
        let currentOriginalName = '';

        editNameInput.addEventListener('input', debounce((e) => {
            checkDuplicateName(e.target.value, editNameWarning, editSubmitBtn, currentOriginalName);
        }, 500));

        function openEditModal(id, name, groupId) {
            const modal = document.getElementById('editAssemblyModal');
            const form = document.getElementById('editForm');
            const input = document.getElementById('edit_name');
            const groupSelect = document.getElementById('edit_church_group_id');

            currentOriginalName = name;
            form.action = `/local-assemblies/${id}`;
            input.value = name;
            groupSelect.value = groupId;
            
            // Clear previous warnings
            editNameWarning.classList.add('hidden');
            editSubmitBtn.disabled = false;
            editSubmitBtn.style.opacity = '1';
            
            modal.classList.remove('hidden');
        }
    </script>
</x-app-layout>