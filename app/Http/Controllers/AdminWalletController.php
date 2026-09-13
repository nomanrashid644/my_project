<?php

namespace App\Http\Controllers;

use App\Models\Pharmacy;
use App\Models\User;
use App\Models\Wallet;
use App\Services\WalletService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminWalletController extends Controller
{
    public function show(Wallet $wallet): View
    {
        $wallet->load(['user', 'pharmacy']);

        return view('wallet.admin-show', [
            'wallet' => $wallet,
            'transactions' => $wallet->transactions()->with('creator')->latest()->paginate(20),
        ]);
    }

    public function customer(User $user): RedirectResponse
    {
        return redirect()->route('admin.wallets.show', app(WalletService::class)->customerWallet($user));
    }

    public function pharmacy(Pharmacy $pharmacy): RedirectResponse
    {
        return redirect()->route('admin.wallets.show', app(WalletService::class)->pharmacyWallet($pharmacy));
    }

    public function adjust(Request $request, Wallet $wallet, WalletService $wallets): RedirectResponse
    {
        $validated = $request->validate([
            'type' => ['required', 'in:credit,debit,refund,promotional_charge,manual_adjustment'],
            'amount' => ['required', 'numeric', 'gt:0'],
            'description' => ['required', 'string', 'max:255'],
        ]);
        $wallets->adjust($wallet, $validated['type'], (float) $validated['amount'], $validated['description'], $request->user());

        return back()->with('success', 'Wallet transaction recorded.');
    }
}