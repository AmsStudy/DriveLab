<x-guest-layout>
    <div class="mb-6 text-center">
        <h2 class="text-xl font-bold text-gray-100">Lupa Password?</h2>
        <p class="text-sm text-gray-400 mt-2">Tidak masalah. Masukkan alamat email Anda dan kami akan mengirimkan link untuk mereset password Anda.</p>
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('password.email') }}">
        @csrf

        <!-- Email Address -->
        <div>
            <label for="email" class="block text-sm font-medium text-gray-300 mb-2">Email</label>
            <input id="email" class="w-full bg-gray-800 border {{ $errors->has('email') ? 'border-red-500' : 'border-gray-700' }} text-gray-100 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-pink-500 focus:ring-1 focus:ring-pink-500" type="email" name="email" :value="old('email')" required autofocus />
            <x-input-error :messages="$errors->get('email')" class="mt-2 text-red-400" />
        </div>

        <div class="flex items-center justify-between mt-6">
            <a href="{{ route('login') }}" class="text-sm text-gray-400 hover:text-gray-300 transition-colors">
                &larr; Kembali
            </a>
            <button type="submit" class="bg-gradient-to-r from-pink-600 to-fuchsia-700 hover:from-pink-700 hover:to-fuchsia-800 text-white font-semibold py-2.5 px-5 rounded-xl shadow-lg shadow-pink-500/30 transition-all active:scale-[0.98]">
                Kirim Link Reset
            </button>
        </div>
    </form>
</x-guest-layout>
