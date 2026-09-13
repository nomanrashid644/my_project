<?php

namespace Tests\Feature;

use App\Models\Medicine;
use App\Models\MedicineCategory;
use App\Models\Pharmacy;
use App\Models\User;
use App\Services\WalletService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ShoppingFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_customer_can_add_to_cart_and_place_order(): void
    {
        [$customer, $medicine] = $this->shoppingSetup();
        $this->actingAs($customer);

        $this->post(route('cart.store', $medicine), ['quantity' => 2])
            ->assertRedirect(route('cart.index'));

        $this->assertDatabaseHas('cart_items', ['medicine_id' => $medicine->id, 'quantity' => 2]);

        $response = $this->post(route('checkout.store'), [
            'delivery_address' => '12 Main Street, Lahore',
            'delivery_phone' => '03001234567',
        ]);

        $order = $customer->orders()->firstOrFail();
        $response->assertRedirect(route('orders.show', $order));
        $this->assertSame(3, $medicine->fresh()->stock_quantity);
        $this->assertDatabaseHas('order_items', [
            'order_id' => $order->id,
            'medicine_name_snapshot' => 'Test Medicine',
            'quantity' => 2,
        ]);
        $this->assertDatabaseHas('carts', ['user_id' => $customer->id, 'status' => 'checked_out']);
    }

    public function test_customer_cannot_view_another_customers_order(): void
    {
        [$customer, $medicine] = $this->shoppingSetup();
        $otherCustomer = User::factory()->create(['role' => 'customer', 'is_active' => true]);
        $this->actingAs($customer)->post(route('cart.store', $medicine), ['quantity' => 1]);
        $this->post(route('checkout.store'), ['delivery_address' => 'Address', 'delivery_phone' => '03001234567']);
        $order = $customer->orders()->firstOrFail();

        $this->actingAs($otherCustomer)->get(route('orders.show', $order))->assertForbidden();
    }

    public function test_customer_can_pay_order_from_wallet(): void
    {
        [$customer, $medicine] = $this->shoppingSetup();
        $wallet = app(WalletService::class)->customerWallet($customer);
        app(WalletService::class)->adjust($wallet, 'credit', 10, 'Wallet top-up');
        $this->actingAs($customer)->post(route('cart.store', $medicine), ['quantity' => 1]);

        $this->post(route('checkout.store'), [
            'delivery_address' => '12 Main Street, Lahore',
            'delivery_phone' => '03001234567',
            'payment_method' => 'wallet',
        ])->assertRedirect();

        $order = $customer->orders()->firstOrFail();
        $this->assertSame('wallet', $order->payment_method);
        $this->assertSame('paid', $order->payment_status);
        $this->assertSame('0.00', number_format((float) $wallet->fresh()->balance, 2));
    }

    private function shoppingSetup(): array
    {
        $customer = User::factory()->create(['role' => 'customer', 'is_active' => true]);
        $staff = User::factory()->create(['role' => 'pharmacy_staff', 'is_active' => true]);
        $pharmacy = Pharmacy::create(['name' => 'Test Pharmacy', 'slug' => 'test-pharmacy', 'is_active' => true]);
        $pharmacy->staff()->attach($staff);
        $category = MedicineCategory::create(['name' => 'Pain Relief', 'slug' => 'pain-relief', 'is_active' => true]);
        $medicine = Medicine::create([
            'pharmacy_id' => $pharmacy->id,
            'category_id' => $category->id,
            'medicine_name' => 'Test Medicine',
            'price' => 10,
            'stock_quantity' => 5,
            'created_by' => $staff->id,
        ]);

        return [$customer, $medicine];
    }
}