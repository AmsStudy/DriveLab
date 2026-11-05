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
             @elseif($errors->any() && ($errors->has('name') || $errors->has('email')))
                 this.$nextTick(() => window.openProfileModal('profile'));
             @endif
         }
     }"
     @keydown.escape.window="window.closeProfileModal()">

    {{-- Backdrop --}}
    <div class="fixed inset-0 bg-black/80 backdrop-blur-md transition-opacity"
         onclick="window.closeProfileModal()"></div>

    {{-- Modal Card --}}
    <div class="relative bg-[#0F1713] border border-[#1A2820] rounded-3xl w-full max-w-4xl max-h-[92vh] flex flex-col shadow-2xl z-10 overflow-hidden transform transition-all duration-200">
        
        {{-- Header Bar --}}
        <div class="px-6 py-4 border-b border-[#1A2820] flex items-center justify-between gap-4 bg-[#090D0B]/60 backdrop-blur-sm flex-shrink-0">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-2xl bg-gradient-to-br from-emerald-500 to-teal-600 flex items-center justify-center text-white font-bold text-sm shadow-md shadow-emerald-500/20 border border-emerald-400/30 flex-shrink-0">
                    {{ strtoupper(substr($modalUser->name, 0, 2)) }}
                </div>
                <div>
                    <h3 class="text-base font-bold text-white leading-tight">Pengaturan Profil & Akun</h3>
                    <p class="text-xs text-gray-400 mt-0.5">Kelola identitas, kredensial login, dan preferensi akun Anda</p>
                </div>
            </div>

            <button type="button"
                    onclick="window.closeProfileModal()"
                    class="w-9 h-9 rounded-xl flex items-center justify-center text-gray-400 hover:text-white bg-[#142019] hover:bg-rose-500/20 hover:text-rose-400 border border-[#1E3327] hover:border-rose-500/40 transition-all cursor-pointer"
                    title="Tutup (Esc)">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        {{-- Nav Tabs --}}
        <div class="px-6 pt-3 border-b border-[#1A2820] flex items-center gap-2 bg-[#0F1713] flex-shrink-0 overflow-x-auto">
            <button type="button"
                    @click="tab = 'profile'"
                    class="pb-3 px-3 text-xs font-semibold flex items-center gap-2 border-b-2 transition-all cursor-pointer whitespace-nowrap"
                    :class="tab === 'profile' ? 'border-emerald-500 text-emerald-400' : 'border-transparent text-gray-400 hover:text-gray-200'">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                <span>Biodata Diri</span>
            </button>

            <button type="button"
                    @click="tab = 'security'"
                    class="pb-3 px-3 text-xs font-semibold flex items-center gap-2 border-b-2 transition-all cursor-pointer whitespace-nowrap"
                    :class="tab === 'security' ? 'border-emerald-500 text-emerald-400' : 'border-transparent text-gray-400 hover:text-gray-200'">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                <span>Kata Sandi</span>
            </button>

            <button type="button"
                    @click="tab = 'danger'"
                    class="pb-3 px-3 text-xs font-semibold flex items-center gap-2 border-b-2 transition-all cursor-pointer whitespace-nowrap"
                    :class="tab === 'danger' ? 'border-rose-500 text-rose-400' : 'border-transparent text-gray-400 hover:text-gray-200'">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                <span>Zona Bahaya</span>
            </button>
        </div>

        {{-- Modal Body (Scrollable) --}}
        <div class="p-6 overflow-y-auto flex-1 space-y-6">

            {{-- ─── TAB 1: BIODATA DIRI & STATISTIK ─── --}}
            <div x-show="tab === 'profile'" x-cloak class="space-y-6">
                {{-- Quick ID Card Strip --}}
                <div class="p-4 rounded-2xl bg-[#090D0B] border border-[#1A2820] flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 rounded-2xl bg-gradient-to-tr from-emerald-600 to-teal-400 p-0.5 shadow-md flex items-center justify-center">
                            <div class="w-full h-full rounded-[14px] bg-[#0F1713] flex items-center justify-center">
                                <span class="text-base font-black text-emerald-400">{{ strtoupper(substr($modalUser->name, 0, 2)) }}</span>
                            </div>
                        </div>
                        <div>
                            <div class="flex items-center gap-2 flex-wrap">
                                <h4 class="font-bold text-white text-sm">{{ $modalUser->name }}</h4>
                                <span class="text-[10px] px-2 py-0.5 rounded-full font-bold {{ $modalUser->isSuperAdmin() ? 'bg-purple-500/20 text-purple-300' : 'bg-emerald-500/20 text-emerald-300' }}">
                                    {{ $modalUser->isSuperAdmin() ? 'Super Admin' : 'Admin Dosen / Asisten' }}
                                </span>
                            </div>
                            <p class="text-xs text-gray-400 font-mono mt-0.5">{{ $modalUser->email }}</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-3 gap-2 w-full sm:w-auto text-center">
                        <div class="px-3 py-1.5 rounded-xl bg-[#142019] border border-[#1E3327]">
                            <div class="text-xs font-bold text-emerald-400">{{ $totalDokumen }}</div>
                            <div class="text-[9px] text-gray-400">Dokumen</div>
                        </div>
                        <div class="px-3 py-1.5 rounded-xl bg-[#142019] border border-[#1E3327]">
                            <div class="text-xs font-bold text-teal-400">{{ $totalKuis }}</div>
                            <div class="text-[9px] text-gray-400">Kuis</div>
                        </div>
                        <div class="px-3 py-1.5 rounded-xl bg-[#142019] border border-[#1E3327]">
                            <div class="text-xs font-bold text-sky-400">{{ $totalKategori }}</div>
                            <div class="text-[9px] text-gray-400">Kategori</div>
                        </div>
                    </div>
                </div>

                {{-- Edit Profile Form --}}
                <form method="post" action="{{ route('profile.update') }}" class="space-y-4">
                    @csrf
                    @method('patch')

                    <div>
                        <x-input-label for="profile_modal_name" :value="__('Nama Lengkap')" />
                        <x-text-input id="profile_modal_name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $modalUser->name)" required autofocus autocomplete="name" />
                        <x-input-error class="mt-2" :messages="$errors->get('name')" />
                    </div>

                    <div>
                        <x-input-label for="profile_modal_email" :value="__('Alamat Email')" />
                        <x-text-input id="profile_modal_email" name="email" type="email" class="mt-1 block w-full" :value="old('email', $modalUser->email)" required autocomplete="username" />
                        <x-input-error class="mt-2" :messages="$errors->get('email')" />
                    </div>

                    <div class="pt-2 flex items-center justify-end gap-3">
                        <x-primary-button>
                            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            {{ __('Simpan Perubahan') }}
                        </x-primary-button>
                    </div>
                </form>
            </div>

            {{-- ─── TAB 2: KEAMANAN KATA SANDI ─── --}}
            <div x-show="tab === 'security'" x-cloak class="space-y-4">
                <form method="post" action="{{ route('password.update') }}" class="space-y-4">
                    @csrf
                    @method('put')

                    <div>
                        <x-input-label for="profile_modal_current_password" :value="__('Password Saat Ini')" />
                        <x-text-input id="profile_modal_current_password" name="current_password" type="password" class="mt-1 block w-full" autocomplete="current-password" placeholder="Ketik kata sandi saat ini" />
                        <x-input-error :messages="$errors->updatePassword->get('current_password')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="profile_modal_password" :value="__('Password Baru')" />
                        <x-text-input id="profile_modal_password" name="password" type="password" class="mt-1 block w-full" autocomplete="new-password" placeholder="Minimal 8 karakter acak" />
                        <x-input-error :messages="$errors->updatePassword->get('password')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="profile_modal_password_confirmation" :value="__('Konfirmasi Password Baru')" />
                        <x-text-input id="profile_modal_password_confirmation" name="password_confirmation" type="password" class="mt-1 block w-full" autocomplete="new-password" placeholder="Ulangi kata sandi baru" />
                        <x-input-error :messages="$errors->updatePassword->get('password_confirmation')" class="mt-2" />
                    </div>

                    <div class="pt-2 flex items-center justify-end gap-3">
                        <x-primary-button>
                            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                            {{ __('Perbarui Password') }}
                        </x-primary-button>
                    </div>
                </form>
            </div>

            {{-- ─── TAB 3: ZONA BAHAYA ─── --}}
            <div x-show="tab === 'danger'" x-cloak class="space-y-4">
                <div class="p-4 rounded-2xl bg-rose-500/10 border border-rose-500/25 text-rose-300 text-xs leading-relaxed">
                    <h4 class="font-bold text-sm text-rose-400 mb-1 flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                        Peringatan Penting
                    </h4>
                    Setelah akun Anda dihapus, semua berkas, kuis, pengumpulan, dan data yang terhubung akan terhapus secara permanen dari server. Tindakan ini tidak dapat dibatalkan.
                </div>

                <form method="post" action="{{ route('profile.destroy') }}" class="space-y-4 pt-2"
                      onsubmit="return confirm('Apakah Anda yakin ingin menghapus akun ini secara permanen?')">
                    @csrf
                    @method('delete')

                    <div>
                        <x-input-label for="profile_modal_delete_password" :value="__('Konfirmasi Password Akun')" />
                        <x-text-input id="profile_modal_delete_password" name="password" type="password" class="mt-1 block w-full" placeholder="Masukkan password untuk verifikasi penghapusan" required />
                        <x-input-error :messages="$errors->userDeletion->get('password')" class="mt-2" />
                    </div>

                    <div class="pt-2 flex items-center justify-end">
                        <button type="submit"
                                class="px-4 py-2.5 rounded-xl text-xs font-bold text-white bg-rose-600 hover:bg-rose-500 transition-all shadow-md shadow-rose-600/30 cursor-pointer">
                            {{ __('Hapus Akun Permanen') }}
                        </button>
                    </div>
                </form>
            </div>

        </div>

    </div>
</div>
@endif
