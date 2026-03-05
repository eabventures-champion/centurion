<x-guest-layout>
    <div class="mb-10 text-center relative">
        <div
            class="inline-block px-4 py-1.5 rounded-full bg-indigo-500/10 border border-indigo-500/20 text-indigo-400 text-[10px] font-black tracking-[3px] uppercase mb-4">
            Centurion Campaign
        </div>
        <h2 class="text-4xl font-black text-white tracking-tighter leading-none">JOIN THE MOVEMENT</h2>
        <p class="text-sm text-slate-400 mt-4 max-w-xs mx-auto font-medium">
            Register as a Pastor to manage your church, souls, and follow-up activities.
        </p>
    </div>

    <form method="POST" action="{{ route('pastor.register') }}" enctype="multipart/form-data" class="space-y-8">
        @csrf

        {{-- CHURCH INFORMATION SECTION --}}
        <div
            class="p-6 md:p-10 rounded-3xl bg-white/5 border border-white/10 shadow-2xl backdrop-blur-xl relative overflow-hidden group">
            <div class="absolute top-0 right-0 p-6 md:p-10 opacity-10 group-hover:opacity-20 transition-opacity">
                <svg class="w-24 h-24" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                        d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                </svg>
            </div>

            <h3 class="text-[11px] font-black text-indigo-400 uppercase tracking-[3px] flex items-center gap-3 mb-8">
                <span class="w-8 h-px bg-indigo-500/30"></span>
                Church Details
            </h3>

            <div class="space-y-6">
                <div>
                    <label for="church_group_id"
                        class="block text-[11px] font-black text-slate-500 uppercase tracking-[2px] mb-3 ml-1">Church
                        Group</label>
                    <select name="church_group_id" id="church_group_id" required
                        class="block w-full bg-slate-950/50 border-white/5 text-white focus:border-indigo-500 focus:ring-indigo-500 rounded-2xl py-4 transition-all sm:text-sm shadow-inner group-hover:border-white/10">
                        <option value="">Select Group</option>
                        @foreach($groups as $group)
                            <option value="{{ $group->id }}" {{ old('church_group_id') == $group->id ? 'selected' : '' }}>
                                {{ $group->group_name }} ({{ $group->churchCategory->name }})
                            </option>
                        @endforeach
                    </select>
                    <x-input-error :messages="$errors->get('church_group_id')" class="mt-2" />
                </div>

                <div>
                    <label for="church_name"
                        class="block text-[11px] font-black text-slate-500 uppercase tracking-[2px] mb-3 ml-1">Name of
                        Church</label>
                    <input type="text" name="church_name" id="church_name" placeholder="e.g. CE Atomic" required
                        value="{{ old('church_name') }}"
                        class="block w-full bg-slate-950/50 border-white/5 text-white focus:border-indigo-500 focus:ring-indigo-500 rounded-2xl py-4 transition-all sm:text-sm shadow-inner group-hover:border-white/10">
                    <x-input-error :messages="$errors->get('church_name')" class="mt-2" />
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="venue"
                            class="block text-[11px] font-black text-slate-500 uppercase tracking-[2px] mb-3 ml-1">Church
                            Venue</label>
                        <input type="text" name="venue" id="venue" placeholder="e.g. Marriott Hotel"
                            value="{{ old('venue') }}"
                            class="block w-full bg-slate-950/50 border-white/5 text-white focus:border-indigo-500 focus:ring-indigo-500 rounded-2xl py-4 transition-all sm:text-sm shadow-inner group-hover:border-white/10">
                        <x-input-error :messages="$errors->get('venue')" class="mt-2" />
                    </div>
                    <div>
                        <label for="location"
                            class="block text-[11px] font-black text-slate-500 uppercase tracking-[2px] mb-3 ml-1">Location
                            (City/Area)</label>
                        <input type="text" name="location" id="location" placeholder="e.g. East Legon"
                            value="{{ old('location') }}"
                            class="block w-full bg-slate-950/50 border-white/5 text-white focus:border-indigo-500 focus:ring-indigo-500 rounded-2xl py-4 transition-all sm:text-sm shadow-inner group-hover:border-white/10">
                        <x-input-error :messages="$errors->get('location')" class="mt-2" />
                    </div>
                </div>
            </div>
        </div>

        {{-- PASTOR INFORMATION SECTION --}}
        <div
            class="p-6 md:p-10 rounded-3xl bg-white/5 border border-white/10 shadow-2xl backdrop-blur-xl relative overflow-hidden group">
            <div class="absolute top-0 right-0 p-6 md:p-10 opacity-10 group-hover:opacity-20 transition-opacity">
                <svg class="w-24 h-24" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                </svg>
            </div>

            <h3 class="text-[11px] font-black text-emerald-400 uppercase tracking-[3px] flex items-center gap-3 mb-8">
                <span class="w-8 h-px bg-emerald-500/30"></span>
                Personal Profile
            </h3>

            <div class="space-y-6">
                <!-- Profile Picture -->
                <div class="flex items-center gap-6" x-data="{ 
                    imageUrl: null,
                    fileChosen(event) {
                        this.fileToDataUrl(event, src => this.imageUrl = src)
                    },
                    fileToDataUrl(event, callback) {
                        if (! event.target.files.length) return

                        let file = event.target.files[0],
                            reader = new FileReader()

                        reader.readAsDataURL(file)
                        reader.onload = e => callback(e.target.result)
                    }
                }">
                    <div
                        class="w-20 h-20 rounded-2xl bg-slate-950 flex items-center justify-center border border-white/5 group/pic relative overflow-hidden">
                        <template x-if="!imageUrl">
                            <svg class="w-8 h-8 text-slate-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path d="M12 4v16m8-8H4" stroke-width="2" stroke-linecap="round" />
                            </svg>
                        </template>
                        <template x-if="imageUrl">
                            <img :src="imageUrl" class="absolute inset-0 w-full h-full object-cover">
                        </template>
                        <template x-if="imageUrl">
                            <div
                                class="absolute inset-0 bg-slate-950/40 flex items-center justify-center opacity-0 group-hover/pic:opacity-100 transition-opacity">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path d="M12 4v16m8-8H4" stroke-width="2" stroke-linecap="round" />
                                </svg>
                            </div>
                        </template>
                        <input type="file" name="profile_picture" id="profile_picture" accept="image/*"
                            @change="fileChosen" class="absolute inset-0 opacity-0 cursor-pointer z-10">
                    </div>
                    <div class="flex-1">
                        <label class="block text-[10px] font-black text-slate-500 uppercase tracking-[2px] mb-1">Profile
                            Photo</label>
                        <p class="text-[10px] text-slate-400">Click to upload or drag and drop</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                    <div class="md:col-span-1">
                        <label for="title"
                            class="block text-[11px] font-black text-slate-500 uppercase tracking-[2px] mb-3 ml-1">Title</label>
                        <select name="title" id="title" required
                            class="block w-full bg-slate-950/50 border-white/5 text-white focus:border-indigo-500 focus:ring-indigo-500 rounded-2xl py-4 transition-all sm:text-sm">
                            @foreach(['Bro', 'Sis', 'Pastor', 'Dcn', 'Dcns', 'Mr', 'Mrs'] as $t)
                                <option value="{{ $t }}" {{ old('title') == $t ? 'selected' : '' }}>{{ $t }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="md:col-span-3">
                        <label for="name"
                            class="block text-[11px] font-black text-slate-500 uppercase tracking-[2px] mb-3 ml-1">Full
                            Name</label>
                        <input type="text" name="name" id="name" placeholder="E.g. John Doe" required
                            value="{{ old('name') }}"
                            class="block w-full bg-slate-950/50 border-white/5 text-white focus:border-indigo-500 focus:ring-indigo-500 rounded-2xl py-4 transition-all sm:text-sm">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="contact"
                            class="block text-[11px] font-black text-slate-500 uppercase tracking-[2px] mb-3 ml-1">Contact
                            Number</label>
                        <input type="text" name="contact" id="contact" placeholder="024 000 0000" required
                            value="{{ old('contact') }}"
                            class="block w-full bg-slate-950/50 border-white/5 text-white focus:border-indigo-500 focus:ring-indigo-500 rounded-2xl py-4 transition-all sm:text-sm">
                        <input type="hidden" name="ignore_group_duplicate" id="ignore_group_duplicate" value="0">
                        <div id="contact-warning"
                            class="mt-2 p-3 rounded-xl bg-rose-500/10 border border-rose-500/20 hidden">
                            <div class="flex flex-col gap-3">
                                <span id="warning-message" class="text-[10px] font-bold text-rose-400 italic"></span>
                                <button type="button" id="continue-as-pastor"
                                    class="hidden w-fit px-4 py-2 bg-emerald-600 hover:bg-emerald-500 text-white text-[9px] font-black uppercase tracking-[2px] rounded-lg transition-all active:scale-95 shadow-lg shadow-emerald-600/20">
                                    CONTINUE AS A PASTOR
                                </button>
                            </div>
                        </div>
                    </div>
                    <div>
                        <label for="email"
                            class="block text-[11px] font-black text-slate-500 uppercase tracking-[2px] mb-3 ml-1">Email
                            Address</label>
                        <input type="email" name="email" id="email" required placeholder="name@church.com"
                            value="{{ old('email') }}"
                            class="block w-full bg-slate-950/50 border-white/5 text-white focus:border-indigo-500 focus:ring-indigo-500 rounded-2xl py-4 transition-all sm:text-sm">
                    </div>
                </div>

                <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                    <div>
                        <label for="gender"
                            class="block text-[11px] font-black text-slate-500 uppercase tracking-[2px] mb-3 ml-1">Gender</label>
                        <select name="gender" id="gender"
                            class="block w-full bg-slate-950/50 border-white/5 text-white focus:border-indigo-500 focus:ring-indigo-500 rounded-2xl py-4 transition-all sm:text-sm">
                            <option value="">Select</option>
                            <option value="Male" {{ old('gender') == 'Male' ? 'selected' : '' }}>Male</option>
                            <option value="Female" {{ old('gender') == 'Female' ? 'selected' : '' }}>Female</option>
                        </select>
                    </div>
                    <div class="md:col-span-2">
                        <label
                            class="block text-[11px] font-black text-slate-500 uppercase tracking-[2px] mb-3 ml-1">Birth
                            Day (Day/Month)</label>
                        <div class="grid grid-cols-2 gap-3">
                            <select name="birth_day_day" required
                                class="block w-full bg-slate-950/50 border-white/5 text-white focus:border-indigo-500 focus:ring-indigo-500 rounded-2xl py-4 transition-all sm:text-sm">
                                <option value="">Day</option>
                                @for($i = 1; $i <= 31; $i++)
                                    <option value="{{ sprintf('%02d', $i) }}" {{ old('birth_day_day') == sprintf('%02d', $i) ? 'selected' : '' }}>{{ $i }}</option>
                                @endfor
                            </select>
                            <select name="birth_day_month" required
                                class="block w-full bg-slate-950/50 border-white/5 text-white focus:border-indigo-500 focus:ring-indigo-500 rounded-2xl py-4 transition-all sm:text-sm">
                                <option value="">Month</option>
                                @foreach(['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'] as $index => $month)
                                    <option value="{{ sprintf('%02d', $index + 1) }}" {{ old('birth_day_month') == sprintf('%02d', $index + 1) ? 'selected' : '' }}>
                                        {{ $month }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div>
                        <label for="marital_status"
                            class="block text-[11px] font-black text-slate-500 uppercase tracking-[2px] mb-3 ml-1">Marital Status</label>
                        <select name="marital_status" id="marital_status"
                            class="block w-full bg-slate-950/50 border-white/5 text-white focus:border-indigo-500 focus:ring-indigo-500 rounded-2xl py-4 transition-all sm:text-sm">
                            <option value="">Select</option>
                            @foreach(['Single', 'Married'] as $status)
                                <option value="{{ $status }}" {{ old('marital_status') == $status ? 'selected' : '' }}>
                                    {{ $status }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="occupation"
                            class="block text-[11px] font-black text-slate-500 uppercase tracking-[2px] mb-3 ml-1">Occupation</label>
                        <input type="text" name="occupation" id="occupation" placeholder="e.g. Professional"
                            value="{{ old('occupation') }}"
                            class="block w-full bg-slate-950/50 border-white/5 text-white focus:border-indigo-500 focus:ring-indigo-500 rounded-2xl py-4 transition-all sm:text-sm">
                    </div>
                </div>
            </div>
        </div>

        {{-- SECURITY SECTION --}}
        <div
            class="p-8 rounded-3xl bg-white/5 border border-white/10 shadow-2xl backdrop-blur-xl relative overflow-hidden group">
            <h3 class="text-[10px] font-black text-rose-500 uppercase tracking-[3px] flex items-center gap-3 mb-8">
                <span class="w-8 h-px bg-rose-500/30"></span>
                Security
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="password"
                        class="block text-[11px] font-black text-slate-500 uppercase tracking-[2px] mb-3 ml-1">Password</label>
                    <input type="password" name="password" id="password" required autocomplete="new-password"
                        class="block w-full bg-slate-950/50 border-white/5 text-white focus:border-indigo-500 focus:ring-indigo-500 rounded-2xl py-4 transition-all sm:text-sm">
                </div>
                <div>
                    <label for="password_confirmation"
                        class="block text-[11px] font-black text-slate-500 uppercase tracking-[2px] mb-3 ml-1">Confirm
                        Password</label>
                    <input type="password" name="password_confirmation" id="password_confirmation" required
                        autocomplete="new-password"
                        class="block w-full bg-slate-950/50 border-white/5 text-white focus:border-indigo-500 focus:ring-indigo-500 rounded-2xl py-4 transition-all sm:text-sm">
                </div>
            </div>
        </div>

        <div class="pt-6">
            <button type="submit" id="submit-btn"
                class="w-full py-5 bg-indigo-600 hover:bg-indigo-500 rounded-2xl font-black text-xs text-white uppercase tracking-[4px] active:scale-[0.98] transition-all shadow-2xl shadow-indigo-600/40">
                COMPLETE REGISTRATION
            </button>
            <div class="mt-8 text-center text-[10px] font-bold text-slate-500 uppercase tracking-[2px]">
                Already registered? <a href="{{ route('login') }}"
                    class="text-indigo-400 hover:text-indigo-300 transition-colors">Log in here</a>
            </div>
        </div>
    </form>

    <script>
        function debounce(func, wait) {
            let timeout;
            return function (...args) {
                clearTimeout(timeout);
                timeout = setTimeout(() => func.apply(this, args), wait);
            };
        }

        const contactInput = document.getElementById('contact');
        const warningDiv = document.getElementById('contact-warning');
        const warningMessage = document.getElementById('warning-message');
        const continueBtn = document.getElementById('continue-as-pastor');
        const ignoreInput = document.getElementById('ignore_group_duplicate');
        const submitBtn = document.getElementById('submit-btn');

        if (contactInput) {
            contactInput.addEventListener('input', debounce(function () {
                const contact = this.value;

                // Reset bypass on input
                if (ignoreInput) ignoreInput.value = "0";
                if (continueBtn) continueBtn.classList.add('hidden');

                if (contact.length < 5) {
                    warningDiv.classList.add('hidden');
                    if (warningMessage) warningMessage.innerText = '';
                    else warningDiv.innerText = '';
                    submitBtn.disabled = false;
                    submitBtn.classList.remove('opacity-50', 'cursor-not-allowed');
                    return;
                }

                fetch(`/church-groups/check-contact?contact=${encodeURIComponent(contact)}&exclude_type=church`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.exists) {
                            const msg = `⚠️ This contact belongs to ${data.owner} in ${data.entity}`;
                            if (warningMessage) warningMessage.innerText = msg;
                            else warningDiv.innerText = msg;

                            warningDiv.classList.remove('hidden');

                            if (data.type === 'group' && continueBtn) {
                                continueBtn.classList.remove('hidden');
                            }

                            submitBtn.disabled = true;
                            submitBtn.classList.add('opacity-50', 'cursor-not-allowed');
                        } else {
                            warningDiv.classList.add('hidden');
                            if (warningMessage) warningMessage.innerText = '';
                            else warningDiv.innerText = '';
                            submitBtn.disabled = false;
                            submitBtn.classList.remove('opacity-50', 'cursor-not-allowed');
                        }
                    })
                    .catch(error => {
                        console.error('Error checking contact:', error);
                    });
            }, 400));
        }

        if (continueBtn) {
            continueBtn.addEventListener('click', function () {
                if (ignoreInput) ignoreInput.value = "1";
                warningDiv.classList.add('hidden');
                submitBtn.disabled = false;
                submitBtn.classList.remove('opacity-50', 'cursor-not-allowed');
            });
        }
    </script>
</x-guest-layout>