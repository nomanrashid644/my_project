<?php

namespace App\Http\Controllers;

use App\Services\WalletService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class WalletController extends Controller
{
    public function __construct(private WalletService $wallets)
    {
    }

    public function index(Request $request): View
    {
        $wallet = $this->wallets->customerWallet($request->user());
        $transactions = $wallet->transactions()->latest()->paginate(15);

        return view('wallet.index', compact('wallet', 'transactions'));
    }
}