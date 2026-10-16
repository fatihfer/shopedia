<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function edit(Request $request): View
    {
        return view('profile.edit', ['user' => $request->user()]);
    }

    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'phone' => ['nullable', 'string', 'max:20', 'regex:/^(\+62|62|0)8\d{7,11}$/'],
            'address' => ['nullable', 'string', 'max:500'],
        ]);

        $request->user()->update($validated);

        return back()->with('success', 'Profil diperbarui.');
    }
}
