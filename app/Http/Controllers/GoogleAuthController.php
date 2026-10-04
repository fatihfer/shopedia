<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

class GoogleAuthController extends Controller
{
    public function redirect(): RedirectResponse
    {
        return Socialite::driver('google')->redirect();
    }

    public function callback(): RedirectResponse
    {
        try {
            $googleUser = Socialite::driver('google')->user();
        } catch (\Exception) {
            return redirect()->route('login')->with('error', 'Login Google gagal, silakan coba lagi.');
        }

        $user = User::where('google_id', $googleUser->getId())->first();

        if (! $user && $googleUser->getEmail()) {
            $user = User::where('email', $googleUser->getEmail())->first();

            if ($user) {
                // Akun manual sudah ada dengan email yang sama: tautkan Google.
                $user->update(['google_id' => $googleUser->getId()]);
            }
        }

        if (! $user) {
            $user = User::create([
                'name' => $googleUser->getName() ?: explode('@', (string) $googleUser->getEmail())[0],
                'email' => $googleUser->getEmail(),
                'password' => null,
                'role' => 'customer',
                'google_id' => $googleUser->getId(),
            ]);
        }

        Auth::login($user, true);
        request()->session()->regenerate();

        return redirect()->intended(
            $user->isAdmin() ? route('admin.dashboard') : route('products.index')
        );
    }
}
