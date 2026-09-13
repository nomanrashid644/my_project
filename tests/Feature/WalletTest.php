<?php

namespace Tests\Feature;

use App\Models\Pharmacy;
use App\Models\User;
use App\Models\Wallet;
use App\Services\WalletService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class WalletTest extends TestCase
{
    use RefreshDatabase;

    public function test_wallet_credit_and_debit_record_auditable_balances(): void
    {
        $user = User::factory()->create(['role' => 'customer', 'is_active' => true]);
        $wallet = app(WalletService::class)->customerWallet($user);
        $service = app(WalletService::class);

        $credit = $service->adjust($wallet, 'credit', 100.50, 'Admin top-up');
        $debit = $service->adjust($wallet, 'debit', 25.25, 'Order payment');

        $this->assertSame('0.00', number_format((float) $credit->balance_before, 2));
        $this->assertSame('100.50', number_format((float) $credit->balance_after, 2));
        $this->assertSame('75.25', number_format((float) $debit->balance_after, 2));
        $this->assertSame('75.25', number_format((float) $wallet->fresh()->balance, 2));
        $this->assertDatabaseCount('wallet_transactions', 2);
    }

    public function test_insufficient_debit_does_not_change_wallet(): void
    {
        $user = User::factory()->create(['role' => 'customer', 'is_active' => true]);
        $wallet = app(WalletService::class)->customerWallet($user);
        $service = app(WalletService::class);
        $service->adjust($wallet, 'credit', 10, 'Top-up');

        $this->expectException(ValidationException::class);
        try {
            $service->adjust($wallet, 'debit', 11, 'Too expensive');
        } finally {
            $this->assertSame('10.00', number_format((float) $wallet->fresh()->balance, 2));
            $this->assertDatabaseCount('wallet_transactions', 1);
        }
    }

    public function test_only_admin_can_manage_wallets(): void
    {
        $customer = User::factory()->create(['role' => 'customer', 'is_active' => true]);
        $admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);
        $wallet = app(WalletService::class)->customerWallet($customer);

        $this->actingAs($customer)->get(route('admin.wallets.show', $wallet))->assertForbidden();
        $this->actingAs($admin)->get(route('admin.wallets.show', $wallet))->assertOk();
    }
}