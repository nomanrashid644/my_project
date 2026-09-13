<?php

namespace Tests\Feature;

use App\Models\MedicineCategory;
use App\Models\Order;
use App\Models\Pharmacy;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FeedbackTest extends TestCase
{
    use RefreshDatabase;

    public function test_status_change_creates_customer_notification(): void
    {
        [, $order, $staff] = $this->orderSetup();

        $this->actingAs($staff)->patch(route('manage.orders.status', $order), ['status' => 'confirmed'])->assertRedirect();

        $this->assertDatabaseHas('notifications', [
            'notifiable_type' => User::class,
            'notifiable_id' => $order->user_id,
        ]);
    }

    public function test_customer_can_rate_delivered_order_only_once(): void
    {
        [$customer, $order] = $this->orderSetup();
        $order->update(['status' => 'delivered']);

        $this->actingAs($customer)->post(route('orders.rating.store', $order), [
            'rating' => 5,
            'comment' => 'Fast delivery.',
        ])->assertRedirect();
        $this->assertDatabaseHas('pharmacy_ratings', ['order_id' => $order->id, 'rating' => 5]);

        $this->actingAs($customer)->post(route('orders.rating.store', $order), ['rating' => 4])->assertStatus(422);
        $this->assertDatabaseCount('pharmacy_ratings', 1);
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
            'order_number' => 'ORD-FEEDBACK-001',
            'status' => 'pending',
            'subtotal' => 10,
            'total_amount' => 10,
            'delivery_address' => 'Test address',
            'delivery_phone' => '03001234567',
        ]);

        return [$customer, $order, $staff];
    }
}