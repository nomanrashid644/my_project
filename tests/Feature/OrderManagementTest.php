<?php

namespace Tests\Feature;

use App\Models\MedicineCategory;
use App\Models\Order;
use App\Models\Pharmacy;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_assigned_staff_can_progress_an_order(): void
    {
        [$staff, $order] = $this->orderSetup();

        $this->actingAs($staff)->patch(route('manage.orders.status', $order), ['status' => 'confirmed'])
            ->assertRedirect();
        $this->assertSame('confirmed', $order->fresh()->status);
        $this->assertNotNull($order->fresh()->confirmed_at);

        $this->patch(route('manage.orders.status', $order), ['status' => 'preparing'])->assertRedirect();
        $this->assertSame('preparing', $order->fresh()->status);
    }

    public function test_invalid_transition_is_rejected(): void
    {
        [$staff, $order] = $this->orderSetup();

        $this->actingAs($staff)
            ->from(route('manage.orders.show', $order))
            ->patch(route('manage.orders.status', $order), ['status' => 'delivered'])
            ->assertRedirect(route('manage.orders.show', $order))
            ->assertSessionHasErrors('status');

        $this->assertSame('pending', $order->fresh()->status);
    }

    public function test_customer_cannot_access_management_orders(): void
    {
        [$staff, $order] = $this->orderSetup();
        $customer = User::factory()->create(['role' => 'customer', 'is_active' => true]);

        $this->actingAs($customer)->get(route('manage.orders.index'))->assertForbidden();
        $this->actingAs($customer)->patch(route('manage.orders.status', $order), ['status' => 'confirmed'])->assertForbidden();
    }

    private function orderSetup(): array
    {
        $customer = User::factory()->create(['role' => 'customer', 'is_active' => true]);
        $staff = User::factory()->create(['role' => 'pharmacy_staff', 'is_active' => true]);
        $pharmacy = Pharmacy::create(['name' => 'Test Pharmacy', 'slug' => 'test-pharmacy', 'is_active' => true]);
        $pharmacy->staff()->attach($staff);
        MedicineCategory::create(['name' => 'Pain Relief', 'slug' => 'pain-relief', 'is_active' => true]);
        $order = Order::create([
            'user_id' => $customer->id,
            'pharmacy_id' => $pharmacy->id,
            'order_number' => 'ORD-TEST-001',
            'status' => 'pending',
            'subtotal' => 10,
            'total_amount' => 10,
            'delivery_address' => 'Test address',
            'delivery_phone' => '03001234567',
        ]);

        return [$staff, $order];
    }
}