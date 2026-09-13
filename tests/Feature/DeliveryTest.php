<?php

namespace Tests\Feature;

use App\Models\DeliveryAssignment;
use App\Models\Order;
use App\Models\Pharmacy;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DeliveryTest extends TestCase
{
    use RefreshDatabase;

    public function test_assigned_rider_can_complete_delivery(): void
    {
        [$rider, $assignment] = $this->deliverySetup();
        $this->actingAs($rider)->patch(route('delivery.status', $assignment), ['status' => 'picked_up'])->assertRedirect();
        $this->assertSame('picked_up', $assignment->fresh()->status);
        $this->assertSame('dispatched', $assignment->order->fresh()->status);

        $this->patch(route('delivery.status', $assignment), ['status' => 'delivered'])->assertRedirect();
        $this->assertSame('delivered', $assignment->fresh()->status);
        $this->assertSame('delivered', $assignment->order->fresh()->status);
    }

    public function test_rider_cannot_update_another_riders_delivery(): void
    {
        [, $assignment] = $this->deliverySetup();
        $otherRider = User::factory()->create(['role' => 'rider', 'is_active' => true]);

        $this->actingAs($otherRider)->patch(route('delivery.status', $assignment), ['status' => 'picked_up'])->assertForbidden();
    }

    private function deliverySetup(): array
    {
        $customer = User::factory()->create(['role' => 'customer', 'is_active' => true]);
        $rider = User::factory()->create(['role' => 'rider', 'is_active' => true]);
        $pharmacy = Pharmacy::create(['name' => 'Test Pharmacy', 'slug' => 'test-pharmacy', 'is_active' => true]);
        $order = Order::create(['user_id' => $customer->id, 'pharmacy_id' => $pharmacy->id, 'order_number' => 'ORD-DELIVERY-001', 'status' => 'confirmed', 'subtotal' => 10, 'total_amount' => 10, 'delivery_address' => 'Test address', 'delivery_phone' => '03001234567']);
        $assignment = DeliveryAssignment::create(['order_id' => $order->id, 'rider_id' => $rider->id, 'status' => 'assigned', 'assigned_at' => now()]);

        return [$rider, $assignment];
    }
}