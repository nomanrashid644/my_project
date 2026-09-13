<?php

namespace Tests\Feature;

use App\Models\Campaign;
use App\Models\Medicine;
use App\Models\MedicineCategory;
use App\Models\Pharmacy;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class CampaignTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_a_campaign(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);
        $pharmacy = Pharmacy::create(['name' => 'Test Pharmacy', 'slug' => 'test-pharmacy', 'is_active' => true]);

        $this->actingAs($admin)->post(route('admin.campaigns.store'), [
            'pharmacy_id' => $pharmacy->id,
            'title' => 'Summer savings',
            'discount_type' => 'percentage',
            'discount_value' => 10,
            'promo_code' => 'SUMMER10',
            'starts_at' => now()->subDay()->format('Y-m-d H:i'),
            'ends_at' => now()->addDay()->format('Y-m-d H:i'),
            'is_active' => 1,
        ])->assertRedirect(route('admin.campaigns.index'));

        $this->assertDatabaseHas('campaigns', ['promo_code' => 'SUMMER10', 'discount_type' => 'percentage']);
    }

    public function test_valid_campaign_reduces_order_total_and_counts_usage(): void
    {
        [$customer, $medicine, $campaign] = $this->campaignSetup();
        $this->actingAs($customer)->post(route('cart.store', $medicine), ['quantity' => 2]);

        $this->post(route('checkout.store'), [
            'delivery_address' => '12 Main Street',
            'delivery_phone' => '03001234567',
            'promo_code' => 'SAVE10',
        ])->assertRedirect();

        $order = $customer->orders()->firstOrFail();
        $this->assertSame('20.00', number_format((float) $order->subtotal, 2));
        $this->assertSame('2.00', number_format((float) $order->discount_amount, 2));
        $this->assertSame('18.00', number_format((float) $order->total_amount, 2));
        $this->assertSame(1, $campaign->fresh()->used_count);
    }

    public function test_expired_campaign_is_rejected(): void
    {
        [$customer, $medicine, $campaign] = $this->campaignSetup();
        $campaign->update(['ends_at' => Carbon::now()->subMinute()]);
        $this->actingAs($customer)->post(route('cart.store', $medicine), ['quantity' => 1]);

        $this->from(route('checkout.create'))->post(route('checkout.store'), [
            'delivery_address' => '12 Main Street',
            'delivery_phone' => '03001234567',
            'promo_code' => 'SAVE10',
        ])->assertRedirect(route('checkout.create'))->assertSessionHasErrors('promo_code');
    }

    private function campaignSetup(): array
    {
        $customer = User::factory()->create(['role' => 'customer', 'is_active' => true]);
        $pharmacy = Pharmacy::create(['name' => 'Test Pharmacy', 'slug' => 'test-pharmacy', 'is_active' => true]);
        $category = MedicineCategory::create(['name' => 'Pain Relief', 'slug' => 'pain-relief', 'is_active' => true]);
        $medicine = Medicine::create(['pharmacy_id' => $pharmacy->id, 'category_id' => $category->id, 'medicine_name' => 'Test Medicine', 'price' => 10, 'stock_quantity' => 5, 'is_active' => true]);
        $campaign = Campaign::create(['pharmacy_id' => $pharmacy->id, 'title' => 'Save ten', 'discount_type' => 'percentage', 'discount_value' => 10, 'promo_code' => 'SAVE10', 'starts_at' => Carbon::now()->subDay(), 'ends_at' => Carbon::now()->addDay(), 'is_active' => true]);

        return [$customer, $medicine, $campaign];
    }
}