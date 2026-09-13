<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function edit(Request $request): View
    {
        return view('profile.edit');
    }

    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'regex:/^[A-Za-z ]{3,50}$/'],
            'phone_number' => ['required', 'regex:/^[0-9]{10,15}$/'],
            'current_password' => ['required_with:password', 'nullable', 'current_password'],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
        ]);
        $request->user()->update([
            'name' => trim($validated['name']),
            'phone_number' => trim($validated['phone_number']),
            ...(! empty($validated['password']) ? ['password' => Hash::make($validated['password'])] : []),
        ]);

        return back()->with('success', 'Profile updated successfully.');
    }
}