<section class="space-y-4">
    <header class="flex items-center gap-3 pb-4 border-b border-rose-900/30">
        <div class="w-10 h-10 rounded-xl bg-rose-500/10 border border-rose-500/25 text-rose-400 flex items-center justify-center flex-shrink-0">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
        </div>
        <div>
            <h2 class="text-base font-bold text-rose-400">
                {{ __('Hapus Akun Pengguna') }}
            </h2>
            <p class="text-xs text-gray-400 mt-0.5">
                {{ __('Setelah akun dihapus, semua berkas, data, dan konfigurasi yang terhubung akan dihapus secara permanen.') }}
            </p>
        </div>
    </header>

    <div class="pt-2">
        <button type="button"
                x-data=""
                x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')"
                class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-rose-500/15 hover:bg-rose-500/25 border border-rose-500/30 text-rose-300 hover:text-white text-xs font-semibold transition-all shadow-sm cursor-pointer">
            <svg class="w-4 h-4 text-rose-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
            {{ __('Hapus Akun Ini') }}
        </button>
    </div>

    <x-modal name="confirm-user-deletion" :show="$errors->userDeletion->isNotEmpty()" focusable>
        <form method="post" action="{{ route('profile.destroy') }}" class="p-6 bg-[#0F1713] border border-[#1A2820] rounded-2xl text-left">
            @csrf
            @method('delete')

            <div class="flex items-center gap-3 mb-4">
                <div class="w-10 h-10 rounded-xl bg-rose-500/15 border border-rose-500/30 text-rose-400 flex items-center justify-center flex-shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                </div>
                <div>
                    <h2 class="text-base font-bold text-white">
                        {{ __('Konfirmasi Penghapusan Akun') }}
                    </h2>
                    <p class="text-xs text-rose-400/80 mt-0.5">
                        {{ __('Tindakan ini permanen dan tidak dapat dibatalkan.') }}
                    </p>
                </div>
            </div>

            <p class="text-xs text-gray-300 leading-relaxed mb-4">
                {{ __('Silakan masukkan kata sandi Anda saat ini untuk mengonfirmasi bahwa Anda benar-benar ingin menghapus akun ini secara permanen dari sistem.') }}
            </p>

            <div class="mt-4">
                <x-input-label for="password" value="{{ __('Password Akun') }}" class="sr-only" />

                <x-text-input
                    id="password"
                    name="password"
                    type="password"
                    class="block w-full text-sm"
                    placeholder="{{ __('Ketik password akun Anda...') }}"
                />

                <x-input-error :messages="$errors->userDeletion->get('password')" class="mt-2" />
            </div>

            <div class="mt-6 flex items-center justify-end gap-3 pt-3 border-t border-[#1A2820]">
                <button type="button"
                        x-on:click="$dispatch('close')"
                        class="px-4 py-2 rounded-xl text-xs font-semibold text-gray-300 hover:text-white bg-[#142019] hover:bg-[#1C2E24] border border-[#1E3327] transition-all cursor-pointer">
                    {{ __('Batal') }}
                </button>

                <button type="submit"
                        class="px-4 py-2 rounded-xl text-xs font-semibold text-white bg-rose-600 hover:bg-rose-500 transition-all shadow-md shadow-rose-600/25 cursor-pointer">
                    {{ __('Ya, Hapus Akun') }}
                </button>
            </div>
        </form>
    </x-modal>
</section>
