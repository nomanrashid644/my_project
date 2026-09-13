<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\Pharmacy;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminReportingTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_dashboard_contains_database_backed_summary(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);
        $customer = User::factory()->create(['role' => 'customer', 'is_active' => true]);
        $pharmacy = Pharmacy::create(['name' => 'Test Pharmacy', 'slug' => 'test-pharmacy', 'is_active' => true]);
        Order::create([
            'user_id' => $customer->id,
            'pharmacy_id' => $pharmacy->id,
            'order_number' => 'ORD-REPORT-001',
            'status' => 'delivered',
            'subtotal' => 100,
            'total_amount' => 90,
            'delivery_address' => 'Test address',
            'delivery_phone' => '03001234567',
        ]);

        $this->actingAs($admin)->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee('Rs 90.00')
            ->assertSee('Delivered');
    }

    public function test_admin_can_toggle_other_user_but_customers_cannot_manage_users(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);
        $customer = User::factory()->create(['role' => 'customer', 'is_active' => true]);

        $this->actingAs($admin)->patch(route('admin.users.toggle', $customer))->assertRedirect();
        $this->assertFalse($customer->fresh()->is_active);
        $this->actingAs($customer)->get(route('admin.users.index'))->assertForbidden();
    }

    public function test_non_admin_cannot_access_reports(): void
    {
        $staff = User::factory()->create(['role' => 'pharmacy_staff', 'is_active' => true]);

        $this->actingAs($staff)->get(route('admin.reports'))->assertForbidden();
    }
}