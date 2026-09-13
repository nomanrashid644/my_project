<?php

namespace App\Services;

use App\Models\User;
use App\Models\Pharmacy;
use App\Models\Wallet;
use App\Models\WalletTransaction;
use Illuminate\Database\DatabaseManager;
use Illuminate\Validation\ValidationException;

class WalletService
{
    public function __construct(private DatabaseManager $database)
    {
    }

    public function customerWallet(User $user): Wallet
    {
        return Wallet::firstOrCreate(['user_id' => $user->id], ['currency' => 'PKR', 'is_active' => true]);
    }

    public function pharmacyWallet(Pharmacy $pharmacy): Wallet
    {
        return Wallet::firstOrCreate(['pharmacy_id' => $pharmacy->id], ['currency' => 'PKR', 'is_active' => true]);
    }

    public function adjust(Wallet $wallet, string $type, float $amount, string $description, ?User $actor = null, ?string $reference = null): WalletTransaction
    {
        if (! in_array($type, ['credit', 'debit', 'refund', 'promotional_charge', 'manual_adjustment'], true)) {
            throw ValidationException::withMessages(['amount' => 'Invalid wallet transaction type.']);
        }
        $amountCents = (int) round($amount * 100);
        if ($amountCents <= 0) {
            throw ValidationException::withMessages(['amount' => 'Amount must be greater than zero.']);
        }

        return $this->database->transaction(function () use ($wallet, $type, $amountCents, $description, $actor, $reference) {
            $lockedWallet = Wallet::whereKey($wallet->id)->lockForUpdate()->firstOrFail();
            if (! $lockedWallet->is_active) {
                throw ValidationException::withMessages(['wallet' => 'This wallet is inactive.']);
            }
            $beforeCents = (int) round((float) $lockedWallet->balance * 100);
            $isCredit = in_array($type, ['credit', 'refund'], true);
            $afterCents = $isCredit ? $beforeCents + $amountCents : $beforeCents - $amountCents;
            if ($afterCents < 0) {
                throw ValidationException::withMessages(['amount' => 'Insufficient wallet balance.']);
            }
            $lockedWallet->update(['balance' => $afterCents / 100]);

            return $lockedWallet->transactions()->create([
                'type' => $type,
                'amount' => $amountCents / 100,
                'balance_before' => $beforeCents / 100,
                'balance_after' => $afterCents / 100,
                'reference' => $reference,
                'description' => $description,
                'status' => 'completed',
                'created_by' => $actor?->id,
            ]);
        });
    }
}