{{-- resources/views/components/profile-modal.blade.php --}}
@php
    $modalUser = auth()->user();
    $totalDokumen = $modalUser ? $modalUser->soals()->count() : 0;
    $totalKuis = $modalUser ? $modalUser->quizzes()->count() : 0;
    $totalKategori = $modalUser ? $modalUser->categories()->count() : 0;
@endphp

@if($modalUser)
<div id="profile-settings-modal"
     class="fixed inset-0 z-[110] hidden flex items-center justify-center p-3 sm:p-5 profile-modal-backdrop-wrap"
     style="z-index: 110;"
     x-data="{
         tab: 'profile',
         open: false,
         showCurrentPassword: false,
         showNewPassword: false,
         showConfirmPassword: false,
         showDeletePassword: false,
         confirmDeleteChecked: false,
         copiedEmail: false,
         newPasswordInput: '',

         get passwordStrength() {
             const p = this.newPasswordInput || '';
             if (!p) return { score: 0, label: 'Belum diisi', color: 'bg-gray-600', textCol: 'text-gray-400' };
             let score = 0;
             if (p.length >= 8) score++;
             if (/[A-Z]/.test(p) && /[a-z]/.test(p)) score++;
             if (/[0-9]/.test(p)) score++;
             if (/[^A-Za-z0-9]/.test(p)) score++;

             if (score <= 1) return { score: 1, label: 'Lemah', color: 'bg-rose-500', textCol: 'text-rose-400' };
             if (score === 2) return { score: 2, label: 'Cukup', color: 'bg-amber-500', textCol: 'text-amber-400' };
             if (score === 3) return { score: 3, label: 'Kuat', color: 'bg-teal-500', textCol: 'text-teal-400' };
             return { score: 4, label: 'Sangat Kuat', color: 'bg-emerald-500', textCol: 'text-emerald-400' };
         },

         copyEmail() {
             navigator.clipboard.writeText('{{ $modalUser->email }}');
             this.copiedEmail = true;
             setTimeout(() => { this.copiedEmail = false; }, 2200);
         },

         init() {
             window.openProfileModal = (targetTab = 'profile') => {
                 this.tab = targetTab;
                 document.getElementById('profile-settings-modal').classList.remove('hidden');
                 document.body.style.overflow = 'hidden';
                 this.open = true;
             };
             window.closeProfileModal = () => {
                 document.getElementById('profile-settings-modal').classList.add('hidden');
                 document.body.style.overflow = '';
                 this.open = false;
             };

             @if($errors->updatePassword->isNotEmpty() || session('status') === 'password-updated')
                 this.$nextTick(() => window.openProfileModal('security'));
             @elseif($errors->userDeletion->isNotEmpty())
                 this.$nextTick(() => window.openProfileModal('danger'));
             @elseif($errors->any() && ($errors->has('name') || $errors->has('email')) || session('status') === 'profile-updated')
                 this.$nextTick(() => window.openProfileModal('profile'));
             @endif
         }
     }"
     @keydown.escape.window="window.closeProfileModal()">

    {{-- Backdrop with soft blur --}}
    <div class="fixed inset-0 bg-black/75 backdrop-blur-md transition-opacity duration-300"
         onclick="window.closeProfileModal()"></div>

    {{-- Modal Card --}}
    <div class="profile-modal-card relative rounded-3xl w-full max-w-4xl max-h-[92vh] flex flex-col shadow-2xl z-10 overflow-hidden transform transition-all duration-300 border">

        {{-- Top Cyber Glow Line --}}
        <div class="h-1 w-full bg-gradient-to-r from-emerald-500 via-teal-400 to-emerald-600 flex-shrink-0"></div>

        {{-- Header Bar (User Hero Preview & Close) --}}
        <div class="profile-modal-header px-6 py-4.5 border-b flex items-center justify-between gap-4 flex-shrink-0 relative overflow-hidden">
            {{-- Background Radial Glow --}}
            <div class="absolute -right-16 -top-16 w-56 h-56 bg-emerald-500/10 rounded-full blur-3xl pointer-events-none"></div>

            <div class="flex items-center gap-4 relative z-10 min-w-0">
                {{-- Avatar with Online Pulse --}}
                <div class="relative flex-shrink-0">
                    <div class="w-13 h-13 sm:w-14 sm:h-14 rounded-2xl bg-gradient-to-tr from-emerald-600 via-teal-500 to-emerald-400 p-0.5 shadow-lg shadow-emerald-500/20 flex items-center justify-center">
                        <div class="profile-avatar-inner w-full h-full rounded-[14px] flex items-center justify-center">
                            <span class="text-base sm:text-lg font-black text-transparent bg-clip-text bg-gradient-to-r from-emerald-400 to-teal-200">
                                {{ strtoupper(substr($modalUser->name, 0, 2)) }}
                            </span>
                        </div>
                    </div>
                    <span class="absolute -bottom-0.5 -right-0.5 flex h-3.5 w-3.5">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-3.5 w-3.5 bg-emerald-500 border-2 border-[#0F1713]"></span>
                    </span>
                </div>

                {{-- User Info Title & Meta --}}
                <div class="min-w-0 flex-1">
                    <div class="flex items-center gap-2 flex-wrap">
                        <h3 class="profile-title text-base sm:text-lg font-bold leading-tight truncate">
                            {{ $modalUser->name }}
                        </h3>
                        <span class="text-[10px] font-bold px-2 py-0.5 rounded-full border {{ $modalUser->isSuperAdmin() ? 'bg-purple-500/15 text-purple-300 border-purple-500/30' : 'bg-emerald-500/15 text-emerald-300 border-emerald-500/30' }}">
                            {{ $modalUser->isSuperAdmin() ? '⚡ Super Admin' : '🛡️ Admin Asisten / Dosen' }}
                        </span>
                    </div>

                    <div class="flex items-center gap-2 mt-1 text-xs text-gray-400 flex-wrap">
                        <span class="font-mono text-gray-300">{{ $modalUser->email }}</span>
                        <span class="text-gray-500">•</span>
                        <button type="button"
                                @click="copyEmail()"
                                class="inline-flex items-center gap-1 text-[11px] text-emerald-400 hover:text-emerald-300 hover:underline cursor-pointer focus:outline-none"
                                title="Salin Email">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                            <span x-text="copiedEmail ? 'Tersalin!' : 'Salin'"></span>
                        </button>
                    </div>
                </div>
            </div>

            {{-- Close Button --}}
            <button type="button"
                    onclick="window.closeProfileModal()"
                    class="profile-modal-close-btn w-9 h-9 rounded-xl flex items-center justify-center transition-all cursor-pointer flex-shrink-0 z-10"
                    title="Tutup (Esc)">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        {{-- Interactive Activity Stats Strip --}}
        <div class="profile-stats-strip px-6 py-2.5 border-b flex items-center justify-between gap-3 flex-shrink-0 overflow-x-auto">
            <div class="flex items-center gap-2 sm:gap-4 w-full">
                <div class="profile-stat-box flex-1 min-w-[100px] px-3 py-1.5 rounded-xl border flex items-center gap-2.5 transition-all">
                    <div class="w-7 h-7 rounded-lg bg-emerald-500/15 text-emerald-400 flex items-center justify-center flex-shrink-0">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    </div>
                    <div class="min-w-0">
                        <div class="text-xs font-bold text-emerald-400 leading-tight">{{ $totalDokumen }}</div>
                        <div class="text-[10px] text-gray-400 truncate">Dokumen Soal</div>
                    </div>
                </div>

                <div class="profile-stat-box flex-1 min-w-[100px] px-3 py-1.5 rounded-xl border flex items-center gap-2.5 transition-all">
                    <div class="w-7 h-7 rounded-lg bg-teal-500/15 text-teal-400 flex items-center justify-center flex-shrink-0">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>
                    </div>
                    <div class="min-w-0">
                        <div class="text-xs font-bold text-teal-400 leading-tight">{{ $totalKuis }}</div>
                        <div class="text-[10px] text-gray-400 truncate">Kuis CBT</div>
                    </div>
                </div>

                <div class="profile-stat-box flex-1 min-w-[100px] px-3 py-1.5 rounded-xl border flex items-center gap-2.5 transition-all">
                    <div class="w-7 h-7 rounded-lg bg-sky-500/15 text-sky-400 flex items-center justify-center flex-shrink-0">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
                    </div>
                    <div class="min-w-0">
                        <div class="text-xs font-bold text-sky-400 leading-tight">{{ $totalKategori }}</div>
                        <div class="text-[10px] text-gray-400 truncate">Kategori</div>
                    </div>
                </div>

                <div class="profile-stat-box hidden md:flex flex-1 min-w-[100px] px-3 py-1.5 rounded-xl border items-center gap-2.5 transition-all">
                    <div class="w-7 h-7 rounded-lg bg-indigo-500/15 text-indigo-400 flex items-center justify-center flex-shrink-0">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    </div>
                    <div class="min-w-0">
                        <div class="text-xs font-bold text-indigo-300 leading-tight">{{ $modalUser->created_at ? $modalUser->created_at->format('M Y') : '—' }}</div>
                        <div class="text-[10px] text-gray-400 truncate">Terdaftar</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Nav Segmented Tabs --}}
        <div class="profile-tabs-wrap px-6 pt-2 border-b flex items-center gap-1 flex-shrink-0 overflow-x-auto">
            <button type="button"
                    @click="tab = 'profile'"
                    class="profile-tab-btn pb-2.5 pt-2 px-3.5 text-xs font-semibold flex items-center gap-2 border-b-2 transition-all cursor-pointer whitespace-nowrap"
                    :class="tab === 'profile' ? 'profile-tab-active border-emerald-500 text-emerald-400 font-bold' : 'border-transparent text-gray-400 hover:text-gray-200'">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                <span>Biodata Diri</span>
            </button>

            <button type="button"
                    @click="tab = 'security'"
                    class="profile-tab-btn pb-2.5 pt-2 px-3.5 text-xs font-semibold flex items-center gap-2 border-b-2 transition-all cursor-pointer whitespace-nowrap"
                    :class="tab === 'security' ? 'profile-tab-active border-emerald-500 text-emerald-400 font-bold' : 'border-transparent text-gray-400 hover:text-gray-200'">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                <span>Kata Sandi & Keamanan</span>
            </button>

            <button type="button"
                    @click="tab = 'danger'"
                    class="profile-tab-btn pb-2.5 pt-2 px-3.5 text-xs font-semibold flex items-center gap-2 border-b-2 transition-all cursor-pointer whitespace-nowrap"
                    :class="tab === 'danger' ? 'profile-tab-active border-rose-500 text-rose-400 font-bold' : 'border-transparent text-gray-400 hover:text-gray-200'">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                <span>Zona Bahaya</span>
            </button>
        </div>

        {{-- Status Notification Banner (if any) --}}
        @if(session('status') === 'profile-updated' || session('success'))
            <div class="mx-6 mt-4 p-3 rounded-2xl bg-emerald-500/10 border border-emerald-500/30 flex items-center gap-3 text-xs text-emerald-300">
                <div class="w-6 h-6 rounded-lg bg-emerald-500/20 text-emerald-400 flex items-center justify-center flex-shrink-0">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                </div>
                <span>{{ session('success') ?: 'Perubahan data profil Anda telah berhasil disimpan.' }}</span>
            </div>
        @elseif(session('status') === 'password-updated')
            <div class="mx-6 mt-4 p-3 rounded-2xl bg-teal-500/10 border border-teal-500/30 flex items-center gap-3 text-xs text-teal-300">
                <div class="w-6 h-6 rounded-lg bg-teal-500/20 text-teal-400 flex items-center justify-center flex-shrink-0">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                </div>
                <span>Kata sandi Anda berhasil diperbarui. Silakan gunakan password baru pada sesi masuk berikutnya.</span>
            </div>
        @endif

        {{-- Modal Body (Scrollable) --}}
        <div class="profile-modal-body p-6 overflow-y-auto flex-1 space-y-6">

            {{-- ─── TAB 1: BIODATA DIRI & INFORMASI AKUN ─── --}}
            <div x-show="tab === 'profile'" x-cloak class="space-y-6">

                <form method="post" action="{{ route('profile.update') }}" class="space-y-5">
                    @csrf
                    @method('patch')

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        {{-- Nama Lengkap --}}
                        <div>
                            <label for="profile_modal_name" class="profile-input-label block text-xs font-bold uppercase tracking-wider mb-2">
                                Nama Lengkap <span class="text-rose-500">*</span>
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-emerald-400">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                </div>
                                <input id="profile_modal_name"
                                       name="name"
                                       type="text"
                                       class="profile-text-input block w-full pl-10 pr-4 py-2.5 text-xs sm:text-sm rounded-xl border focus:outline-none focus:ring-2 focus:ring-emerald-500/30 transition-all font-medium"
                                       value="{{ old('name', $modalUser->name) }}"
                                       required
                                       autofocus
                                       autocomplete="name"
                                       placeholder="Nama lengkap admin" />
                            </div>
                            <x-input-error class="mt-1.5" :messages="$errors->get('name')" />
                        </div>

                        {{-- Alamat Email --}}
                        <div>
                            <label for="profile_modal_email" class="profile-input-label block text-xs font-bold uppercase tracking-wider mb-2">
                                Alamat Email <span class="text-rose-500">*</span>
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-teal-400">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                </div>
                                <input id="profile_modal_email"
                                       name="email"
                                       type="email"
                                       class="profile-text-input block w-full pl-10 pr-4 py-2.5 text-xs sm:text-sm rounded-xl border focus:outline-none focus:ring-2 focus:ring-emerald-500/30 transition-all font-medium"
                                       value="{{ old('email', $modalUser->email) }}"
                                       required
                                       autocomplete="username"
                                       placeholder="admin@ic-labs.ac.id" />
                            </div>
                            <x-input-error class="mt-1.5" :messages="$errors->get('email')" />
                        </div>
                    </div>

                    {{-- Additional Meta Info Badges --}}
                    <div class="p-4 rounded-2xl border profile-info-card grid grid-cols-1 sm:grid-cols-2 gap-3 text-xs">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-xl bg-emerald-500/10 text-emerald-400 flex items-center justify-center flex-shrink-0">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                            </div>
                            <div>
                                <div class="text-[10px] text-gray-400 uppercase tracking-wider">Unit & Laboratorium</div>
                                <div class="font-bold text-gray-200 profile-text-main">IC-Labs Repository Management</div>
                            </div>
                        </div>

                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-xl bg-teal-500/10 text-teal-400 flex items-center justify-center flex-shrink-0">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                            </div>
                            <div>
                                <div class="text-[10px] text-gray-400 uppercase tracking-wider">Status Verifikasi</div>
                                <div class="font-bold text-emerald-400 flex items-center gap-1">
                                    <span>Terverifikasi & Aktif</span>
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Submit Button --}}
                    <div class="pt-2 flex items-center justify-end gap-3 border-t profile-card-border">
                        <button type="submit"
                                class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl text-xs sm:text-sm font-bold text-white bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-500 hover:to-teal-500 transition-all shadow-lg shadow-emerald-600/25 cursor-pointer">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            <span>Simpan Biodata</span>
                        </button>
                    </div>
                </form>

            </div>

            {{-- ─── TAB 2: KEAMANAN KATA SANDI (DENGAN STRENGTH METER & EYE TOGGLE) ─── --}}
            <div x-show="tab === 'security'" x-cloak class="space-y-5">

                <div class="p-3.5 rounded-2xl border profile-info-card text-xs text-gray-300 leading-relaxed flex items-center gap-3">
                    <div class="w-8 h-8 rounded-xl bg-teal-500/15 text-teal-400 flex items-center justify-center flex-shrink-0">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                    </div>
                    <div>
                        Pastikan kata sandi Anda menggunakan minimal 8 karakter dengan kombinasi huruf besar, huruf kecil, dan angka untuk menjaga keamanan akses ke panel admin.
                    </div>
                </div>

                <form method="post" action="{{ route('password.update') }}" class="space-y-4">
                    @csrf
                    @method('put')

                    {{-- Current Password --}}
                    <div>
                        <label for="profile_modal_current_password" class="profile-input-label block text-xs font-bold uppercase tracking-wider mb-2">
                            Kata Sandi Saat Ini <span class="text-rose-500">*</span>
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-gray-400">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                            </div>
                            <input id="profile_modal_current_password"
                                   name="current_password"
                                   :type="showCurrentPassword ? 'text' : 'password'"
                                   class="profile-text-input block w-full pl-10 pr-11 py-2.5 text-xs sm:text-sm rounded-xl border focus:outline-none focus:ring-2 focus:ring-emerald-500/30 transition-all font-mono"
                                   autocomplete="current-password"
                                   placeholder="Ketik kata sandi saat ini" />
                            <button type="button"
                                    @click="showCurrentPassword = !showCurrentPassword"
                                    class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-gray-400 hover:text-gray-200 cursor-pointer focus:outline-none"
                                    tabindex="-1">
                                <svg x-show="!showCurrentPassword" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                <svg x-show="showCurrentPassword" x-cloak class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18"/></svg>
                            </button>
                        </div>
                        <x-input-error :messages="$errors->updatePassword->get('current_password')" class="mt-1.5" />
                    </div>

                    {{-- New Password with Live Strength Bar --}}
                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <label for="profile_modal_password" class="profile-input-label block text-xs font-bold uppercase tracking-wider">
                                Kata Sandi Baru <span class="text-rose-500">*</span>
                            </label>
                            <span class="text-[11px] font-bold" :class="passwordStrength.textCol" x-text="'Kekuatan: ' + passwordStrength.label"></span>
                        </div>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-emerald-400">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/></svg>
                            </div>
                            <input id="profile_modal_password"
                                   name="password"
                                   x-model="newPasswordInput"
                                   :type="showNewPassword ? 'text' : 'password'"
                                   class="profile-text-input block w-full pl-10 pr-11 py-2.5 text-xs sm:text-sm rounded-xl border focus:outline-none focus:ring-2 focus:ring-emerald-500/30 transition-all font-mono"
                                   autocomplete="new-password"
                                   placeholder="Minimal 8 karakter unik" />
                            <button type="button"
                                    @click="showNewPassword = !showNewPassword"
                                    class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-gray-400 hover:text-gray-200 cursor-pointer focus:outline-none"
                                    tabindex="-1">
                                <svg x-show="!showNewPassword" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                <svg x-show="showNewPassword" x-cloak class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18"/></svg>
                            </button>
                        </div>

                        {{-- Strength Bars Indicator --}}
                        <div class="grid grid-cols-4 gap-1.5 mt-2">
                            <div class="h-1.5 rounded-full transition-all duration-300" :class="passwordStrength.score >= 1 ? passwordStrength.color : 'bg-gray-700/50'"></div>
                            <div class="h-1.5 rounded-full transition-all duration-300" :class="passwordStrength.score >= 2 ? passwordStrength.color : 'bg-gray-700/50'"></div>
                            <div class="h-1.5 rounded-full transition-all duration-300" :class="passwordStrength.score >= 3 ? passwordStrength.color : 'bg-gray-700/50'"></div>
                            <div class="h-1.5 rounded-full transition-all duration-300" :class="passwordStrength.score >= 4 ? passwordStrength.color : 'bg-gray-700/50'"></div>
                        </div>
                        <x-input-error :messages="$errors->updatePassword->get('password')" class="mt-1.5" />
                    </div>

                    {{-- Confirm New Password --}}
                    <div>
                        <label for="profile_modal_password_confirmation" class="profile-input-label block text-xs font-bold uppercase tracking-wider mb-2">
                            Konfirmasi Kata Sandi Baru <span class="text-rose-500">*</span>
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-emerald-400">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                            </div>
                            <input id="profile_modal_password_confirmation"
                                   name="password_confirmation"
                                   :type="showConfirmPassword ? 'text' : 'password'"
                                   class="profile-text-input block w-full pl-10 pr-11 py-2.5 text-xs sm:text-sm rounded-xl border focus:outline-none focus:ring-2 focus:ring-emerald-500/30 transition-all font-mono"
                                   autocomplete="new-password"
                                   placeholder="Ulangi kata sandi baru" />
                            <button type="button"
                                    @click="showConfirmPassword = !showConfirmPassword"
                                    class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-gray-400 hover:text-gray-200 cursor-pointer focus:outline-none"
                                    tabindex="-1">
                                <svg x-show="!showConfirmPassword" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                <svg x-show="showConfirmPassword" x-cloak class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18"/></svg>
                            </button>
                        </div>
                        <x-input-error :messages="$errors->updatePassword->get('password_confirmation')" class="mt-1.5" />
                    </div>

                    {{-- Submit Button --}}
                    <div class="pt-2 flex items-center justify-end gap-3 border-t profile-card-border">
                        <button type="submit"
                                class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl text-xs sm:text-sm font-bold text-white bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-500 hover:to-teal-500 transition-all shadow-lg shadow-emerald-600/25 cursor-pointer">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                            <span>Perbarui Kata Sandi</span>
                        </button>
                    </div>
                </form>

            </div>

            {{-- ─── TAB 3: ZONA BAHAYA (DENGAN SAFEGUARD CHECKBOX) ─── --}}
            <div x-show="tab === 'danger'" x-cloak class="space-y-5">

                <div class="p-4 rounded-2xl bg-rose-500/10 border border-rose-500/30 text-rose-300 text-xs leading-relaxed flex items-start gap-3">
                    <div class="w-9 h-9 rounded-xl bg-rose-500/20 text-rose-400 flex items-center justify-center flex-shrink-0 mt-0.5 shadow-md shadow-rose-500/20">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    </div>
                    <div>
                        <h4 class="font-bold text-sm text-rose-400 mb-1">Peringatan Penghapusan Akun Permanen</h4>
                        <p class="text-rose-300/90">
                            Tindakan ini akan menghapus akun administrator Anda beserta berkas lampiran, kuis, pengumpulan, dan rekam jejak aktivitas yang terkait dari sistem. Setelah dihapus, data <strong>tidak dapat dikembalikan</strong>.
                        </p>
                    </div>
                </div>

                <form method="post" action="{{ route('profile.destroy') }}" class="space-y-4">
                    @csrf
                    @method('delete')

                    <div>
                        <label for="profile_modal_delete_password" class="profile-input-label block text-xs font-bold uppercase tracking-wider mb-2">
                            Konfirmasi Password Akun <span class="text-rose-500">*</span>
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-rose-400">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                            </div>
                            <input id="profile_modal_delete_password"
                                   name="password"
                                   :type="showDeletePassword ? 'text' : 'password'"
                                   class="profile-text-input block w-full pl-10 pr-11 py-2.5 text-xs sm:text-sm rounded-xl border border-rose-500/30 focus:outline-none focus:ring-2 focus:ring-rose-500/30 transition-all font-mono"
                                   placeholder="Ketik kata sandi untuk validasi"
                                   required />
                            <button type="button"
                                    @click="showDeletePassword = !showDeletePassword"
                                    class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-gray-400 hover:text-gray-200 cursor-pointer focus:outline-none"
                                    tabindex="-1">
                                <svg x-show="!showDeletePassword" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                <svg x-show="showDeletePassword" x-cloak class="w-4 h-4 text-rose-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18"/></svg>
                            </button>
                        </div>
                        <x-input-error :messages="$errors->userDeletion->get('password')" class="mt-1.5" />
                    </div>

                    {{-- Safeguard Checkbox --}}
                    <label class="flex items-start gap-2.5 p-3 rounded-xl border border-rose-500/20 bg-rose-500/5 cursor-pointer select-none">
                        <input type="checkbox"
                               x-model="confirmDeleteChecked"
                               class="mt-0.5 rounded border-rose-400/40 text-rose-600 focus:ring-rose-500/30 accent-rose-600" />
                        <span class="text-xs text-rose-300">
                            Saya telah membaca risiko di atas dan secara sadar mengonfirmasi untuk menghapus akun ini secara permanen.
                        </span>
                    </label>

                    {{-- Delete Submit Button --}}
                    <div class="pt-2 flex items-center justify-end gap-3 border-t profile-card-border">
                        <button type="submit"
                                :disabled="!confirmDeleteChecked"
                                :class="confirmDeleteChecked ? 'bg-rose-600 hover:bg-rose-500 cursor-pointer shadow-rose-600/30' : 'bg-gray-700 opacity-50 cursor-not-allowed'"
                                class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl text-xs sm:text-sm font-bold text-white transition-all shadow-lg"
                                onclick="return confirm('Peringatan Terakhir: Apakah Anda yakin 100% ingin menghapus akun ini secara permanen?')">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            <span>Hapus Akun Permanen</span>
                        </button>
                    </div>
                </form>

            </div>

        </div>

        {{-- Footer Modal Bar (Quick Exit) --}}
        <div class="profile-modal-footer px-6 py-3 border-t flex items-center justify-between text-xs text-gray-400 flex-shrink-0">
            <span class="inline-flex items-center gap-1.5 text-[11px]">
                <kbd class="px-1.5 py-0.5 text-[10px] font-mono rounded bg-gray-800 border border-gray-700 text-gray-300">Esc</kbd>
                untuk keluar
            </span>
            <button type="button"
                    onclick="window.closeProfileModal()"
                    class="text-xs font-semibold text-gray-400 hover:text-gray-200 transition-colors cursor-pointer">
                Tutup Jendela
            </button>
        </div>

    </div>

    {{-- Seamless Adaptive Theme Styles for Light Mode & Dark Mode --}}
    <style>
        /* Dark Mode (Default) */
        .profile-modal-card {
            background-color: #0F1713;
            border-color: #1A2820;
            color: #E5E7EB;
        }
        .profile-modal-header {
            background-color: rgba(9, 13, 11, 0.7);
            border-color: #1A2820;
        }
        .profile-avatar-inner {
            background-color: #090D0B;
        }
        .profile-title {
            color: #FFFFFF;
        }
        .profile-modal-close-btn {
            background-color: #142019;
            border: 1px solid #1E3327;
            color: #9CA3AF;
        }
        .profile-modal-close-btn:hover {
            background-color: rgba(244, 63, 94, 0.18);
            border-color: rgba(244, 63, 94, 0.4);
            color: #FB7185;
        }
        .profile-stats-strip {
            background-color: rgba(15, 23, 19, 0.85);
            border-color: #1A2820;
        }
        .profile-stat-box {
            background-color: #142019;
            border-color: #1E3327;
        }
        .profile-tabs-wrap {
            background-color: #0F1713;
            border-color: #1A2820;
        }
        .profile-modal-body {
            background-color: #0F1713;
        }
        .profile-modal-footer {
            background-color: rgba(9, 13, 11, 0.8);
            border-color: #1A2820;
        }
        .profile-input-label {
            color: #9CA3AF;
        }
        .profile-text-input {
            background-color: #090D0B;
            border-color: #1A2820;
            color: #FFFFFF;
        }
        .profile-text-input:focus {
            border-color: #10B981;
        }
        .profile-info-card {
            background-color: #090D0B;
            border-color: #1A2820;
        }
        .profile-card-border {
            border-color: #1A2820;
        }

        /* Light Mode Adaptive Overrides */
        html:not(.dark) .profile-modal-card {
            background-color: #FFFFFF !important;
            border-color: #E2EAE5 !important;
            color: #0F1F17 !important;
            box-shadow: 0 25px 60px rgba(0, 0, 0, 0.15) !important;
        }
        html:not(.dark) .profile-modal-header {
            background-color: #F8FAF9 !important;
            border-color: #E2EAE5 !important;
        }
        html:not(.dark) .profile-avatar-inner {
            background-color: #F0FDF4 !important;
        }
        html:not(.dark) .profile-title {
            color: #0F1F17 !important;
        }
        html:not(.dark) .profile-modal-close-btn {
            background-color: #FFFFFF !important;
            border: 1px solid #E2EAE5 !important;
            color: #64748B !important;
        }
        html:not(.dark) .profile-modal-close-btn:hover {
            background-color: #FEE2E2 !important;
            border-color: #FCA5A5 !important;
            color: #EF4444 !important;
        }
        html:not(.dark) .profile-stats-strip {
            background-color: #F8FAF9 !important;
            border-color: #E2EAE5 !important;
        }
        html:not(.dark) .profile-stat-box {
            background-color: #FFFFFF !important;
            border-color: #E2EAE5 !important;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04) !important;
        }
        html:not(.dark) .profile-tabs-wrap {
            background-color: #FFFFFF !important;
            border-color: #E2EAE5 !important;
        }
        html:not(.dark) .profile-tab-btn {
            color: #64748B !important;
        }
        html:not(.dark) .profile-tab-btn:hover {
            color: #0F1F17 !important;
        }
        html:not(.dark) .profile-tab-active {
            color: #059669 !important;
            border-color: #059669 !important;
        }
        html:not(.dark) .profile-modal-body {
            background-color: #FFFFFF !important;
        }
        html:not(.dark) .profile-modal-footer {
            background-color: #F8FAF9 !important;
            border-color: #E2EAE5 !important;
        }
        html:not(.dark) .profile-input-label {
            color: #475569 !important;
        }
        html:not(.dark) .profile-text-input {
            background-color: #FFFFFF !important;
            border-color: #CBD5E1 !important;
            color: #0F1F17 !important;
        }
        html:not(.dark) .profile-text-input:focus {
            border-color: #10B981 !important;
            background-color: #FFFFFF !important;
        }
        html:not(.dark) .profile-info-card {
            background-color: #F8FAF9 !important;
            border-color: #E2EAE5 !important;
        }
        html:not(.dark) .profile-text-main {
            color: #0F1F17 !important;
        }
        html:not(.dark) .profile-card-border {
            border-color: #E2EAE5 !important;
        }
    </style>
</div>
@endif
