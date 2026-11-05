{{-- resources/views/components/profile-modal.blade.php --}}
@php
    $modalUser = auth()->user();
    $totalDokumen = $modalUser ? $modalUser->soals()->count() : 0;
    $totalKuis = $modalUser ? $modalUser->quizzes()->count() : 0;
    $totalKategori = $modalUser ? $modalUser->categories()->count() : 0;
@endphp

@if($modalUser)
<div id="profile-settings-modal"
     class="fixed inset-0 z-[110] hidden flex items-center justify-center p-3 sm:p-5"
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
             if (!p) return { score: 0, label: 'Belum diisi', color: 'bg-gray-600', textCol: 'text-gray-400', pct: 0 };
             let score = 0;
             if (p.length >= 8) score++;
             if (/[A-Z]/.test(p) && /[a-z]/.test(p)) score++;
             if (/[0-9]/.test(p)) score++;
             if (/[^A-Za-z0-9]/.test(p)) score++;

             if (score <= 1) return { score: 1, label: 'Lemah', color: 'bg-rose-500', textCol: 'text-rose-400', pct: 25 };
             if (score === 2) return { score: 2, label: 'Cukup', color: 'bg-amber-500', textCol: 'text-amber-400', pct: 50 };
             if (score === 3) return { score: 3, label: 'Kuat', color: 'bg-teal-500', textCol: 'text-teal-400', pct: 75 };
             return { score: 4, label: 'Sangat Kuat', color: 'bg-emerald-500', textCol: 'text-emerald-400', pct: 100 };
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

    {{-- Backdrop with high-class blur --}}
    <div class="fixed inset-0 bg-black/80 backdrop-blur-md transition-opacity duration-300"
         onclick="window.closeProfileModal()"></div>

    {{-- Main Modal Window (2-Column Desktop Architecture) --}}
    <div class="profile-modal-card relative rounded-3xl w-full max-w-4xl h-auto max-h-[92vh] md:h-[620px] flex flex-col md:flex-row shadow-2xl z-10 overflow-hidden border">

        {{-- Mobile Top Bar with Close Button (Visible on small screens only) --}}
        <div class="md:hidden flex items-center justify-between p-4 border-b profile-card-border profile-modal-mobile-header">
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 rounded-xl bg-gradient-to-tr from-emerald-600 to-teal-500 p-0.5 flex items-center justify-center text-white text-xs font-bold">
                    {{ strtoupper(substr($modalUser->name, 0, 2)) }}
                </div>
                <div>
                    <h3 class="text-sm font-bold profile-text-main leading-tight">{{ $modalUser->name }}</h3>
                    <p class="text-[10px] text-gray-400">Pengaturan Akun</p>
                </div>
            </div>
            <button type="button"
                    onclick="window.closeProfileModal()"
                    class="w-8 h-8 rounded-lg flex items-center justify-center text-gray-400 hover:text-white bg-gray-800/50">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        {{-- ─── LEFT COLUMN: Profile Identity & Navigation Sidebar (md:w-80) ─── --}}
        <aside class="profile-modal-sidebar w-full md:w-80 md:flex-shrink-0 flex flex-col border-b md:border-b-0 md:border-r profile-card-border p-5 relative overflow-hidden">
            
            {{-- Cyber Background Ambient Glow --}}
            <div class="absolute -top-12 -left-12 w-44 h-44 bg-emerald-500/10 rounded-full blur-3xl pointer-events-none"></div>

            {{-- User Identity Block --}}
            <div class="relative z-10 flex flex-col items-center text-center pb-5 border-b profile-card-border">
                {{-- Big Hexagon-styled Avatar with Glow --}}
                <div class="relative mb-3 group">
                    <div class="w-20 h-20 rounded-3xl bg-gradient-to-tr from-emerald-500 via-teal-400 to-emerald-600 p-1 shadow-xl shadow-emerald-500/20 flex items-center justify-center group-hover:scale-105 transition-transform duration-300">
                        <div class="profile-avatar-inner w-full h-full rounded-[20px] flex items-center justify-center">
                            <span class="text-2xl font-black text-transparent bg-clip-text bg-gradient-to-r from-emerald-400 to-teal-200">
                                {{ strtoupper(substr($modalUser->name, 0, 2)) }}
                            </span>
                        </div>
                    </div>
                    {{-- Active Status Dot Indicator --}}
                    <span class="absolute bottom-0 right-0 flex h-4 w-4">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-4 w-4 bg-emerald-500 border-2 border-[#0F1713] shadow-md shadow-emerald-500/40"></span>
                    </span>
                </div>

                {{-- Name & Role --}}
                <h3 class="profile-title text-base font-bold leading-tight truncate w-full px-2" title="{{ $modalUser->name }}">
                    {{ $modalUser->name }}
                </h3>
                
                {{-- Email & Copy Button --}}
                <div class="flex items-center justify-center gap-1.5 mt-1.5 max-w-full">
                    <span class="text-xs text-gray-400 font-mono truncate max-w-[180px]">{{ $modalUser->email }}</span>
                    <button type="button"
                            @click="copyEmail()"
                            class="text-emerald-400 hover:text-emerald-300 p-1 rounded-md transition-colors cursor-pointer"
                            title="Salin Alamat Email">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                    </button>
                    <span x-show="copiedEmail" x-cloak class="text-[10px] text-emerald-400 font-semibold animate-fade-in">Tersalin!</span>
                </div>

                {{-- Role Badge --}}
                <div class="mt-2.5">
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11px] font-bold border {{ $modalUser->isSuperAdmin() ? 'bg-purple-500/15 text-purple-300 border-purple-500/30' : 'bg-emerald-500/15 text-emerald-300 border-emerald-500/30' }}">
                        <span>{{ $modalUser->isSuperAdmin() ? '⚡' : '🛡️' }}</span>
                        <span>{{ $modalUser->isSuperAdmin() ? 'Super Administrator' : 'Admin Asisten / Dosen' }}</span>
                    </span>
                </div>
            </div>

            {{-- Vertical Navigation Tabs --}}
            <nav class="relative z-10 py-4 space-y-1.5 flex-1">
                {{-- Tab: Biodata --}}
                <button type="button"
                        @click="tab = 'profile'"
                        class="w-full flex items-center justify-between px-3.5 py-2.5 rounded-2xl text-xs font-semibold transition-all cursor-pointer group"
                        :class="tab === 'profile' ? 'profile-nav-active shadow-md' : 'profile-nav-inactive hover:bg-white/5'">
                    <div class="flex items-center gap-3">
                        <div class="w-7 h-7 rounded-xl flex items-center justify-center transition-colors"
                             :class="tab === 'profile' ? 'bg-emerald-500 text-white shadow-md shadow-emerald-500/30' : 'bg-emerald-500/15 text-emerald-400 group-hover:bg-emerald-500/25'">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                        </div>
                        <div class="text-left">
                            <span class="block font-bold">Biodata Diri</span>
                            <span class="block text-[10px] opacity-70">Identitas & email</span>
                        </div>
                    </div>
                    <svg class="w-3.5 h-3.5 transition-transform" :class="tab === 'profile' ? 'translate-x-0.5 text-emerald-400' : 'opacity-30'" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
                </button>

                {{-- Tab: Security --}}
                <button type="button"
                        @click="tab = 'security'"
                        class="w-full flex items-center justify-between px-3.5 py-2.5 rounded-2xl text-xs font-semibold transition-all cursor-pointer group"
                        :class="tab === 'security' ? 'profile-nav-active shadow-md' : 'profile-nav-inactive hover:bg-white/5'">
                    <div class="flex items-center gap-3">
                        <div class="w-7 h-7 rounded-xl flex items-center justify-center transition-colors"
                             :class="tab === 'security' ? 'bg-emerald-500 text-white shadow-md shadow-emerald-500/30' : 'bg-teal-500/15 text-teal-400 group-hover:bg-teal-500/25'">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                        </div>
                        <div class="text-left">
                            <span class="block font-bold">Kata Sandi</span>
                            <span class="block text-[10px] opacity-70">Keamanan kredensial</span>
                        </div>
                    </div>
                    <svg class="w-3.5 h-3.5 transition-transform" :class="tab === 'security' ? 'translate-x-0.5 text-emerald-400' : 'opacity-30'" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
                </button>

                {{-- Tab: Danger --}}
                <button type="button"
                        @click="tab = 'danger'"
                        class="w-full flex items-center justify-between px-3.5 py-2.5 rounded-2xl text-xs font-semibold transition-all cursor-pointer group"
                        :class="tab === 'danger' ? 'profile-nav-danger-active shadow-md' : 'profile-nav-inactive hover:bg-rose-500/10 hover:text-rose-400'">
                    <div class="flex items-center gap-3">
                        <div class="w-7 h-7 rounded-xl flex items-center justify-center transition-colors"
                             :class="tab === 'danger' ? 'bg-rose-600 text-white shadow-md shadow-rose-600/30' : 'bg-rose-500/15 text-rose-400 group-hover:bg-rose-500/25'">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                        </div>
                        <div class="text-left">
                            <span class="block font-bold">Zona Bahaya</span>
                            <span class="block text-[10px] opacity-70">Penghapusan akun</span>
                        </div>
                    </div>
                    <svg class="w-3.5 h-3.5 transition-transform" :class="tab === 'danger' ? 'translate-x-0.5 text-rose-400' : 'opacity-30'" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
                </button>
            </nav>

            {{-- Quick Stats Box at Sidebar Bottom --}}
            <div class="relative z-10 pt-4 border-t profile-card-border">
                <div class="grid grid-cols-3 gap-1.5 text-center">
                    <div class="p-2 rounded-xl border profile-stat-box">
                        <div class="text-xs font-black text-emerald-400">{{ $totalDokumen }}</div>
                        <div class="text-[9px] text-gray-400 leading-tight">Soal</div>
                    </div>
                    <div class="p-2 rounded-xl border profile-stat-box">
                        <div class="text-xs font-black text-teal-400">{{ $totalKuis }}</div>
                        <div class="text-[9px] text-gray-400 leading-tight">Kuis</div>
                    </div>
                    <div class="p-2 rounded-xl border profile-stat-box">
                        <div class="text-xs font-black text-sky-400">{{ $totalKategori }}</div>
                        <div class="text-[9px] text-gray-400 leading-tight">Kategori</div>
                    </div>
                </div>
            </div>

        </aside>

        {{-- ─── RIGHT COLUMN: Content Panel & Forms (flex-1) ─── --}}
        <main class="profile-modal-main flex-1 flex flex-col min-w-0 overflow-hidden">

            {{-- Desktop Top Bar with Section Info & Close Button --}}
            <div class="hidden md:flex items-center justify-between px-7 py-5 border-b profile-card-border flex-shrink-0">
                <div>
                    <h4 class="text-base font-bold profile-text-main flex items-center gap-2">
                        <span x-show="tab === 'profile'">Pengaturan Biodata Diri</span>
                        <span x-show="tab === 'security'">Pengaturan Kata Sandi & Keamanan</span>
                        <span x-show="tab === 'danger'" class="text-rose-400">Peringatan: Zona Bahaya</span>
                    </h4>
                    <p class="text-xs text-gray-400 mt-0.5">
                        <span x-show="tab === 'profile'">Perbarui nama lengkap dan email resmi akun administrator Anda.</span>
                        <span x-show="tab === 'security'">Gunakan kata sandi yang kuat dan unik untuk melindungi akses admin.</span>
                        <span x-show="tab === 'danger'">Tindakan destruktif permanen pada data dan akun administrator.</span>
                    </p>
                </div>

                {{-- Close Button --}}
                <button type="button"
                        onclick="window.closeProfileModal()"
                        class="profile-modal-close-btn w-9 h-9 rounded-2xl flex items-center justify-center transition-all cursor-pointer flex-shrink-0"
                        title="Tutup (Esc)">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            {{-- Flash Alert Inside Modal (If profile/password updated) --}}
            @if(session('status') === 'profile-updated' || session('success'))
                <div class="mx-7 mt-4 p-3.5 rounded-2xl bg-emerald-500/10 border border-emerald-500/30 flex items-center gap-3 text-xs text-emerald-300 animate-fade-in flex-shrink-0">
                    <div class="w-6 h-6 rounded-lg bg-emerald-500/20 text-emerald-400 flex items-center justify-center flex-shrink-0">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                    </div>
                    <span class="font-medium">{{ session('success') ?: 'Perubahan data profil Anda telah berhasil disimpan.' }}</span>
                </div>
            @elseif(session('status') === 'password-updated')
                <div class="mx-7 mt-4 p-3.5 rounded-2xl bg-teal-500/10 border border-teal-500/30 flex items-center gap-3 text-xs text-teal-300 animate-fade-in flex-shrink-0">
                    <div class="w-6 h-6 rounded-lg bg-teal-500/20 text-teal-400 flex items-center justify-center flex-shrink-0">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                    </div>
                    <span class="font-medium">Kata sandi Anda berhasil diperbarui. Silakan gunakan password baru pada login berikutnya.</span>
                </div>
            @endif

            {{-- Scrollable Form Container --}}
            <div class="p-6 md:p-7 overflow-y-auto flex-1 space-y-6">

                {{-- ─── TAB 1: BIODATA DIRI ─── --}}
                <div x-show="tab === 'profile'" x-cloak class="space-y-6">
                    <form method="post" action="{{ route('profile.update') }}" class="space-y-5">
                        @csrf
                        @method('patch')

                        <div class="space-y-4">
                            {{-- Field 1: Nama Lengkap --}}
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
                                           class="profile-text-input block w-full pl-10 pr-4 py-2.5 text-xs sm:text-sm rounded-2xl border focus:outline-none focus:ring-2 focus:ring-emerald-500/20 transition-all font-medium"
                                           value="{{ old('name', $modalUser->name) }}"
                                           required
                                           autofocus
                                           autocomplete="name"
                                           placeholder="Nama lengkap admin" />
                                </div>
                                <x-input-error class="mt-1.5" :messages="$errors->get('name')" />
                            </div>

                            {{-- Field 2: Alamat Email --}}
                            <div>
                                <label for="profile_modal_email" class="profile-input-label block text-xs font-bold uppercase tracking-wider mb-2">
                                    Alamat Email Resmi <span class="text-rose-500">*</span>
                                </label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-teal-400">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                    </div>
                                    <input id="profile_modal_email"
                                           name="email"
                                           type="email"
                                           class="profile-text-input block w-full pl-10 pr-4 py-2.5 text-xs sm:text-sm rounded-2xl border focus:outline-none focus:ring-2 focus:ring-emerald-500/20 transition-all font-medium"
                                           value="{{ old('email', $modalUser->email) }}"
                                           required
                                           autocomplete="username"
                                           placeholder="admin@ic-labs.ac.id" />
                                </div>
                                <x-input-error class="mt-1.5" :messages="$errors->get('email')" />
                            </div>
                        </div>

                        {{-- Info Cards: Unit & Verification --}}
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-1">
                            <div class="p-3.5 rounded-2xl border profile-info-card flex items-center gap-3">
                                <div class="w-8 h-8 rounded-xl bg-emerald-500/10 text-emerald-400 flex items-center justify-center flex-shrink-0">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                                </div>
                                <div class="min-w-0">
                                    <div class="text-[10px] text-gray-400 uppercase font-semibold">Unit Laboratorium</div>
                                    <div class="text-xs font-bold profile-text-main truncate">IC-Labs Repository</div>
                                </div>
                            </div>

                            <div class="p-3.5 rounded-2xl border profile-info-card flex items-center gap-3">
                                <div class="w-8 h-8 rounded-xl bg-teal-500/10 text-teal-400 flex items-center justify-center flex-shrink-0">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                                </div>
                                <div class="min-w-0">
                                    <div class="text-[10px] text-gray-400 uppercase font-semibold">Status Akun</div>
                                    <div class="text-xs font-bold text-emerald-400 flex items-center gap-1">
                                        <span>Aktif & Terverifikasi</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Form Footer Actions --}}
                        <div class="pt-4 border-t profile-card-border flex items-center justify-end gap-3">
                            <button type="button"
                                    onclick="window.closeProfileModal()"
                                    class="px-4 py-2.5 rounded-2xl text-xs font-semibold text-gray-400 hover:text-gray-200 transition-colors cursor-pointer">
                                Batal
                            </button>
                            <button type="submit"
                                    class="inline-flex items-center gap-2 px-5 py-2.5 rounded-2xl text-xs font-bold text-white bg-gradient-to-r from-emerald-600 via-emerald-500 to-teal-600 hover:from-emerald-500 hover:to-teal-500 transition-all shadow-lg shadow-emerald-600/25 hover:shadow-emerald-500/35 cursor-pointer active:scale-95">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                <span>Simpan Biodata</span>
                            </button>
                        </div>
                    </form>
                </div>

                {{-- ─── TAB 2: KATA SANDI & KEAMANAN ─── --}}
                <div x-show="tab === 'security'" x-cloak class="space-y-6">
                    <form method="post" action="{{ route('password.update') }}" class="space-y-5">
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
                                       class="profile-text-input block w-full pl-10 pr-11 py-2.5 text-xs sm:text-sm rounded-2xl border focus:outline-none focus:ring-2 focus:ring-emerald-500/20 transition-all font-mono"
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
                                <span class="text-[11px] font-bold" :class="passwordStrength.textCol" x-text="'Tingkat: ' + passwordStrength.label"></span>
                            </div>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-emerald-400">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/></svg>
                                </div>
                                <input id="profile_modal_password"
                                       name="password"
                                       x-model="newPasswordInput"
                                       :type="showNewPassword ? 'text' : 'password'"
                                       class="profile-text-input block w-full pl-10 pr-11 py-2.5 text-xs sm:text-sm rounded-2xl border focus:outline-none focus:ring-2 focus:ring-emerald-500/20 transition-all font-mono"
                                       autocomplete="new-password"
                                       placeholder="Minimal 8 karakter kombinasi" />
                                <button type="button"
                                        @click="showNewPassword = !showNewPassword"
                                        class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-gray-400 hover:text-gray-200 cursor-pointer focus:outline-none"
                                        tabindex="-1">
                                    <svg x-show="!showNewPassword" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    <svg x-show="showNewPassword" x-cloak class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18"/></svg>
                                </button>
                            </div>

                            {{-- Visual Strength Bar --}}
                            <div class="w-full bg-gray-800/40 rounded-full h-1.5 mt-2.5 overflow-hidden">
                                <div class="h-full transition-all duration-300 rounded-full"
                                     :class="passwordStrength.color"
                                     :style="'width: ' + passwordStrength.pct + '%'"></div>
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
                                       class="profile-text-input block w-full pl-10 pr-11 py-2.5 text-xs sm:text-sm rounded-2xl border focus:outline-none focus:ring-2 focus:ring-emerald-500/20 transition-all font-mono"
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

                        {{-- Form Footer Actions --}}
                        <div class="pt-4 border-t profile-card-border flex items-center justify-end gap-3">
                            <button type="button"
                                    onclick="window.closeProfileModal()"
                                    class="px-4 py-2.5 rounded-2xl text-xs font-semibold text-gray-400 hover:text-gray-200 transition-colors cursor-pointer">
                                Batal
                            </button>
                            <button type="submit"
                                    class="inline-flex items-center gap-2 px-5 py-2.5 rounded-2xl text-xs font-bold text-white bg-gradient-to-r from-teal-600 via-emerald-600 to-teal-500 hover:from-teal-500 hover:to-emerald-500 transition-all shadow-lg shadow-teal-600/25 hover:shadow-teal-500/35 cursor-pointer active:scale-95">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                                <span>Perbarui Kata Sandi</span>
                            </button>
                        </div>
                    </form>
                </div>

                {{-- ─── TAB 3: ZONA BAHAYA ─── --}}
                <div x-show="tab === 'danger'" x-cloak class="space-y-6">
                    <form method="post" action="{{ route('profile.destroy') }}" class="space-y-5">
                        @csrf
                        @method('delete')

                        {{-- Danger Notice Box --}}
                        <div class="p-4 rounded-2xl bg-rose-500/10 border border-rose-500/30 text-rose-300 text-xs leading-relaxed flex items-start gap-3.5">
                            <div class="w-9 h-9 rounded-xl bg-rose-500/20 text-rose-400 flex items-center justify-center flex-shrink-0 mt-0.5 shadow-md shadow-rose-500/20">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                            </div>
                            <div class="space-y-1">
                                <h5 class="font-bold text-sm text-rose-400">Peringatan Penghapusan Akun Permanen</h5>
                                <p class="text-rose-300/90">
                                    Tindakan ini akan menghapus akun administrator Anda beserta seluruh dokumen soal, kuis, pengumpulan, dan rekam jejak aktivitas yang terkait dari server. Data <strong>tidak dapat dipulihkan kembali</strong>.
                                </p>
                            </div>
                        </div>

                        {{-- Password Confirmation Input --}}
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
                                       class="profile-text-input block w-full pl-10 pr-11 py-2.5 text-xs sm:text-sm rounded-2xl border border-rose-500/30 focus:outline-none focus:ring-2 focus:ring-rose-500/30 transition-all font-mono"
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
                        <label class="flex items-start gap-3 p-3.5 rounded-2xl border border-rose-500/20 bg-rose-500/5 cursor-pointer select-none">
                            <input type="checkbox"
                                   x-model="confirmDeleteChecked"
                                   class="mt-0.5 rounded border-rose-400/40 text-rose-600 focus:ring-rose-500/30 accent-rose-600 w-4 h-4" />
                            <span class="text-xs text-rose-300 leading-normal">
                                Saya telah memahami seluruh risiko kehilangan data dan secara sadar mengonfirmasi untuk menghapus akun ini secara permanen dari server.
                            </span>
                        </label>

                        {{-- Delete Action Button --}}
                        <div class="pt-4 border-t profile-card-border flex items-center justify-end gap-3">
                            <button type="button"
                                    onclick="window.closeProfileModal()"
                                    class="px-4 py-2.5 rounded-2xl text-xs font-semibold text-gray-400 hover:text-gray-200 transition-colors cursor-pointer">
                                Batal
                            </button>
                            <button type="submit"
                                    :disabled="!confirmDeleteChecked"
                                    :class="confirmDeleteChecked ? 'bg-rose-600 hover:bg-rose-500 cursor-pointer shadow-lg shadow-rose-600/30 active:scale-95' : 'bg-gray-700 opacity-50 cursor-not-allowed'"
                                    class="inline-flex items-center gap-2 px-5 py-2.5 rounded-2xl text-xs font-bold text-white transition-all"
                                    onclick="return confirm('Peringatan Terakhir: Apakah Anda yakin 100% ingin menghapus akun ini secara permanen?')">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                <span>Hapus Akun Permanen</span>
                            </button>
                        </div>
                    </form>
                </div>

            </div>

            {{-- Keyboard Hint Strip at Bottom --}}
            <div class="px-7 py-3 border-t profile-card-border flex items-center justify-between text-[11px] text-gray-400 flex-shrink-0">
                <span class="inline-flex items-center gap-1.5">
                    <kbd class="px-1.5 py-0.5 text-[10px] font-mono rounded-md bg-gray-800/80 border border-gray-700 text-gray-300">Esc</kbd>
                    <span>untuk menutup</span>
                </span>
                <span class="text-gray-400">IC-Labs Profile Management</span>
            </div>

        </main>

    </div>

    {{-- Comprehensive Adaptive Styles (Dark & Light Mode) --}}
    <style>
        /* ── Dark Mode (Default) ── */
        .profile-modal-card {
            background-color: #0F1713;
            border-color: #1A2820;
            color: #E5E7EB;
        }
        .profile-modal-sidebar {
            background-color: #090D0B;
        }
        .profile-avatar-inner {
            background-color: #0F1713;
        }
        .profile-modal-main {
            background-color: #0F1713;
        }
        .profile-title {
            color: #FFFFFF;
        }
        .profile-text-main {
            color: #FFFFFF;
        }
        .profile-card-border {
            border-color: #1A2820;
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
        .profile-nav-inactive {
            color: #9CA3AF;
        }
        .profile-nav-active {
            background-color: rgba(16, 185, 129, 0.12);
            color: #34D399;
            border: 1px solid rgba(16, 185, 129, 0.25);
        }
        .profile-nav-danger-active {
            background-color: rgba(244, 63, 94, 0.15);
            color: #FB7185;
            border: 1px solid rgba(244, 63, 94, 0.35);
        }
        .profile-stat-box {
            background-color: #142019;
            border-color: #1E3327;
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

        /* ── Light Mode Overrides ── */
        html:not(.dark) .profile-modal-card {
            background-color: #FFFFFF !important;
            border-color: #E2EAE5 !important;
            color: #0F1F17 !important;
            box-shadow: 0 25px 60px rgba(0, 0, 0, 0.14) !important;
        }
        html:not(.dark) .profile-modal-sidebar {
            background-color: #F8FAF9 !important;
        }
        html:not(.dark) .profile-avatar-inner {
            background-color: #FFFFFF !important;
        }
        html:not(.dark) .profile-modal-main {
            background-color: #FFFFFF !important;
        }
        html:not(.dark) .profile-title {
            color: #0F1F17 !important;
        }
        html:not(.dark) .profile-text-main {
            color: #0F1F17 !important;
        }
        html:not(.dark) .profile-card-border {
            border-color: #E2EAE5 !important;
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
        html:not(.dark) .profile-nav-inactive {
            color: #475569 !important;
        }
        html:not(.dark) .profile-nav-active {
            background-color: rgba(16, 185, 129, 0.1) !important;
            color: #059669 !important;
            border: 1px solid rgba(16, 185, 129, 0.25) !important;
        }
        html:not(.dark) .profile-nav-danger-active {
            background-color: rgba(244, 63, 94, 0.1) !important;
            color: #DC2626 !important;
            border: 1px solid rgba(244, 63, 94, 0.3) !important;
        }
        html:not(.dark) .profile-stat-box {
            background-color: #FFFFFF !important;
            border-color: #E2EAE5 !important;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04) !important;
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
        }
        html:not(.dark) .profile-info-card {
            background-color: #F8FAF9 !important;
            border-color: #E2EAE5 !important;
        }
        html:not(.dark) .profile-modal-mobile-header {
            background-color: #F8FAF9 !important;
        }
    </style>

</div>
@endif
