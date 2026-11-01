<x-guest-layout>
    <div class="mb-6 text-center">
        <div class="w-12 h-12 mx-auto mb-3 rounded-2xl bg-emerald-500/10 border border-emerald-500/20 flex items-center justify-center text-emerald-400">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
            </svg>
        </div>
        <h2 class="text-xl font-bold text-gray-100">Konfirmasi Password</h2>
        <p class="text-sm text-gray-400 mt-2">Ini adalah area aman aplikasi. Masukkan password akun Anda untuk melanjutkan tindakan ini.</p>
    </div>

    <form method="POST" action="{{ route('password.confirm') }}">
        @csrf

        <!-- Password -->
        <div>
            <label for="password" class="block text-sm font-medium text-gray-300 mb-2">Password Anda</label>
            <input id="password" class="w-full bg-[#090E0B] border {{ $errors->has('password') ? 'border-red-500' : 'border-[#1A2820]' }} text-gray-100 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500"
                   type="password"
                   name="password"
                   placeholder="••••••••"
                   required autocomplete="current-password" autofocus />

            <x-input-error :messages="$errors->get('password')" class="mt-2 text-red-400" />
        </div>

        <div class="mt-6 flex justify-end">
            <button type="submit" class="w-full sm:w-auto bg-gradient-to-r from-emerald-500 to-teal-600 hover:from-emerald-600 hover:to-teal-700 text-white font-semibold py-2.5 px-6 rounded-xl shadow-lg shadow-emerald-500/20 transition-all active:scale-[0.98]">
                Konfirmasi Password
            </button>
        </div>
    </form>
</x-guest-layout>
