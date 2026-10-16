<?php

namespace App\Http\Controllers;

use App\Models\Subscriber;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class SubscriberController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'email' => ['required', 'email', 'max:100', 'unique:subscribers,email'],
        ], [
            'email.unique' => 'Email ini sudah terdaftar, terima kasih!',
        ]);

        Subscriber::create($validated);

        return back()->with('success', 'Berhasil berlangganan info promo! Cek email ya.');
    }
}
