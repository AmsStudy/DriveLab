<x-guest-layout>
    <h2>Login Admin</h2>
    <p>Masuk untuk mengelola Arsip Dokumen & modul</p>

    <x-auth-session-status class="session-status" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <div class="form-group">
            <label for="email">Email</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username"
                   style="{{ $errors->has('email') ? 'border-color:#EF4444;' : '' }}">
            @error('email')<p class="error-msg">{{ $message }}</p>@enderror
        </div>

        <div class="form-group">
            <div class="divider-row">
                <label for="password" style="margin-bottom:0;">Password</label>
                @if (Route::has('password.request'))
                    <a class="link-emerald" href="{{ route('password.request') }}" style="font-size:12px;">Lupa password?</a>
                @endif
            </div>
            <input id="password" type="password" name="password" required autocomplete="current-password"
                   style="{{ $errors->has('password') ? 'border-color:#EF4444;margin-top:6px;' : 'margin-top:6px;' }}">
            @error('password')<p class="error-msg">{{ $message }}</p>@enderror
        </div>

        <div class="checkbox-row">
            <input id="remember_me" type="checkbox" name="remember">
            <span>Ingat saya</span>
        </div>

        <button type="submit" class="btn-primary">Masuk →</button>

        <p style="text-align:center; margin-top:20px; font-size:13px; color:#6B7280;">
            Hanya asisten terdaftar yang bisa masuk. <br> Hubungi Laboran jika Anda belum memiliki akun.
        </p>
    </form>
</x-guest-layout>
