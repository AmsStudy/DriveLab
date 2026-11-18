<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class PasswordResetLinkController extends Controller
{
    /**
     * Display the password reset link request view.
     */
    public function create(): View
    {
        return view('auth.forgot-password');
    }

    /**
     * Handle an incoming password reset link request.
     *
     * @throws ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => ['required', 'email'],
        ]);

        // We will send the password reset link to this user. Once we have attempted
        // to send the link, we will examine the response then see the message we
        // need to show to the user. Finally, we'll send out a proper response.
        $status = Password::sendResetLink(
            $request->only('email')
        );

        $statusMsg = $status == Password::RESET_LINK_SENT
            ? 'Tautan reset password telah kami kirimkan ke email Anda.'
            : ($status == Password::INVALID_USER
                ? 'Kami tidak dapat menemukan pengguna dengan alamat email tersebut.'
                : 'Silakan tunggu beberapa saat sebelum mencoba kembali.');

        return $status == Password::RESET_LINK_SENT
                    ? back()->with('status', $statusMsg)
                    : back()->withInput($request->only('email'))
                        ->withErrors(['email' => $statusMsg]);
    }
}
