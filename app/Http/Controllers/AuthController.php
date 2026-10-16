<?php

namespace App\Http\Controllers;

use App\Mail\OtpMail;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function showLogin(): View
    {
        return view('auth.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

            /** @var User $user */
            $user = Auth::user();

            return redirect()->intended(
                $user->isAdmin() ? route('admin.dashboard') : route('products.index')
            );
        }

        return back()->withErrors(['email' => 'Email atau password salah.'])->onlyInput('email');
    }

    public function showRegister(): View
    {
        return view('auth.register');
    }

    public function register(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:100', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $this->sendOtp($request, $validated['name'], $validated['email'], $validated['password']);

        return redirect()->route('register.verify')
            ->with('success', 'Kode OTP dikirim ke email Anda.');
    }

    public function showVerify(Request $request): View|RedirectResponse
    {
        if (! $request->session()->has('register_pending')) {
            return redirect()->route('register');
        }

        return view('auth.verify-otp');
    }

    public function verify(Request $request): RedirectResponse
    {
        $request->validate([
            'code' => ['required', 'string', 'size:6'],
        ]);

        $pending = $request->session()->get('register_pending');

        if (! $pending || now()->greaterThan($pending['expires_at'])) {
            $request->session()->forget('register_pending');

            return redirect()->route('register')->with('error', 'Kode kedaluwarsa, silakan daftar ulang.');
        }

        $pending['attempts']++;

        if ($pending['attempts'] > 5) {
            $request->session()->forget('register_pending');

            return redirect()->route('register')->with('error', 'Terlalu banyak percobaan, silakan daftar ulang.');
        }

        if (! Hash::check($request->string('code')->toString(), $pending['code_hash'])) {
            $request->session()->put('register_pending', $pending);

            return back()->withErrors(['code' => 'Kode salah, sisa ' . (5 - $pending['attempts']) . ' percobaan.']);
        }

        $user = User::create([
            'name' => $pending['name'],
            'email' => $pending['email'],
            'password' => $pending['password'],
            'role' => 'customer',
        ]);

        $request->session()->forget('register_pending');

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('products.index')->with('success', 'Akun terverifikasi, selamat datang!');
    }

    public function resend(Request $request): RedirectResponse
    {
        $pending = $request->session()->get('register_pending');

        if (! $pending) {
            return redirect()->route('register');
        }

        if (now()->lessThan($pending['resend_available_at'])) {
            return back()->with('error', 'Tunggu sebentar sebelum meminta kode baru.');
        }

        $this->sendOtp($request, $pending['name'], $pending['email'], $pending['password']);

        return back()->with('success', 'Kode baru dikirim ke email Anda.');
    }

    private function sendOtp(Request $request, string $name, string $email, string $password): void
    {
        $code = (string) random_int(100000, 999999);

        // Password polos hanya singgah di session server-side sampai terverifikasi.
        $request->session()->put('register_pending', [
            'name' => $name,
            'email' => $email,
            'password' => $password,
            'code_hash' => Hash::make($code),
            'attempts' => 0,
            'expires_at' => now()->addMinutes(10)->toDateTimeString(),
            'resend_available_at' => now()->addSeconds(60)->toDateTimeString(),
        ]);

        Mail::to($email)->send(new OtpMail($code));
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('products.index');
    }
}
