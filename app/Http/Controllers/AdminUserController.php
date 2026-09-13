<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Pharmacy;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminUserController extends Controller
{
    public function index(): View
    {
        return view('admin.users.index', ['users' => User::latest()->paginate(20)]);
    }

    public function create(): View
    {
        return view('admin.users.create', ['pharmacies' => Pharmacy::where('is_active', true)->orderBy('name')->get()]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'regex:/^[A-Za-z ]{3,50}$/'],
            'email' => ['required', 'email', 'max:100', 'unique:users,email'],
            'phone_number' => ['required', 'regex:/^[0-9]{10,15}$/'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'role' => ['required', 'in:pharmacy_staff,rider'],
            'pharmacy_id' => ['nullable', 'exists:pharmacies,id'],
        ]);
        $user = User::create([
            'name' => trim($validated['name']),
            'email' => trim($validated['email']),
            'phone_number' => trim($validated['phone_number']),
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'],
            'is_active' => true,
        ]);
        if (! empty($validated['pharmacy_id'])) {
            $user->pharmacies()->attach($validated['pharmacy_id']);
        }

        return redirect()->route('admin.users.index')->with('success', 'Team account created successfully.');
    }

    public function toggle(User $user, Request $request): RedirectResponse
    {
        abort_if($user->is($request->user()), 422, 'You cannot deactivate your own admin account.');
        $user->update(['is_active' => ! $user->is_active]);

        return back()->with('success', 'User account status updated.');
    }

    public function resetPassword(Request $request, User $user): RedirectResponse
    {
        abort_if($user->role === 'admin', 403, 'Admin passwords must be recovered by the account owner.');
        $validated = $request->validate(['password' => ['required', 'string', 'min:8', 'confirmed']]);
        $user->update(['password' => Hash::make($validated['password'])]);

        return back()->with('success', 'Team member password reset successfully.');
    }
}