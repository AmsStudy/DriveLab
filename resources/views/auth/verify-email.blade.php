<x-guest-layout>
    <div class="mb-6 text-center">
        <div class="w-12 h-12 rounded-2xl bg-emerald-500/10 border border-emerald-500/25 flex items-center justify-center text-emerald-400 mx-auto mb-3 shadow-lg shadow-emerald-500/20">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
        </div>
        <h2 class="text-xl font-bold text-gray-100">Verifikasi Email Anda</h2>
        <p class="text-xs text-gray-400 mt-2 leading-relaxed">
            Terima kasih telah mendaftar! Sebelum melanjutkan, silakan periksa kotak masuk email Anda dan klik tautan verifikasi yang telah kami kirimkan.
        </p>
    </div>

    @if (session('status') == 'verification-link-sent')
        <div class="mb-5 p-3 rounded-xl bg-emerald-500/10 border border-emerald-500/30 text-emerald-400 text-xs text-center font-medium leading-relaxed">
            Tautan verifikasi baru telah berhasil dikirimkan ke alamat email yang Anda daftarkan.
        </div>
    @endif

    <div class="mt-6 flex flex-col sm:flex-row items-center justify-between gap-3">
        <form method="POST" action="{{ route('verification.send') }}" class="w-full sm:w-auto">
            @csrf
            <button type="submit" class="w-full sm:w-auto bg-gradient-to-r from-emerald-500 to-teal-600 hover:from-emerald-600 hover:to-teal-700 text-white font-semibold py-2.5 px-5 rounded-xl shadow-lg shadow-emerald-500/20 text-xs transition-all active:scale-[0.98] cursor-pointer">
                Kirim Ulang Email Verifikasi
            </button>
        </form>

        <form method="POST" action="{{ route('logout') }}" class="w-full sm:w-auto text-center">
            @csrf
            <button type="submit" class="text-xs text-gray-400 hover:text-gray-200 underline transition-colors cursor-pointer">
                Keluar (Log Out)
            </button>
        </form>
    </div>
</x-guest-layout>
