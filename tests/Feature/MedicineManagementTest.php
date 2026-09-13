<?php

namespace Tests\Feature;

use App\Models\Medicine;
use App\Models\MedicineCategory;
use App\Models\Pharmacy;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MedicineManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_staff_can_create_and_update_medicine(): void
    {
        [$staff, $pharmacy, $category] = $this->pharmacySetup();
        $this->actingAs($staff);

        $response = $this->post(route('medicines.store'), [
            'medicine_name' => 'Panadol 500mg',
            'category_id' => $category->id,
            'price' => '3.50',
            'stock_quantity' => 20,
            'description' => 'Pain relief tablets.',
        ]);

        $medicine = Medicine::firstOrFail();
        $response->assertRedirect(route('medicines.show', $medicine));
        $this->assertSame('Available', $medicine->availability);

        $this->put(route('medicines.update', $medicine), [
            'medicine_name' => 'Panadol 500mg',
            'category_id' => $category->id,
            'price' => '3.50',
            'stock_quantity' => 0,
        ])->assertRedirect(route('medicines.show', $medicine));

        $this->assertSame('Out of Stock', $medicine->fresh()->availability);
        $this->assertSame($pharmacy->id, $medicine->pharmacy_id);
    }

    public function test_customer_can_search_but_cannot_manage_medicines(): void
    {
        [$staff, $pharmacy, $category] = $this->pharmacySetup();
        $medicine = Medicine::create([
            'pharmacy_id' => $pharmacy->id,
            'category_id' => $category->id,
            'medicine_name' => 'Vitamin C 1000mg',
            'price' => 2.99,
            'stock_quantity' => 10,
            'created_by' => $staff->id,
        ]);
        $customer = User::factory()->create(['role' => 'customer', 'is_active' => true]);

        $this->actingAs($customer)
            ->get(route('medicines.index', ['q' => 'Vitamin']))
            ->assertOk()
            ->assertSee($medicine->medicine_name);

        $this->get(route('medicines.create'))->assertForbidden();
    }

    private function pharmacySetup(): array
    {
        $staff = User::factory()->create(['role' => 'pharmacy_staff', 'is_active' => true]);
        $pharmacy = Pharmacy::create(['name' => 'Test Pharmacy', 'slug' => 'test-pharmacy', 'is_active' => true]);
        $pharmacy->staff()->attach($staff);
        $category = MedicineCategory::create(['name' => 'Pain Relief', 'slug' => 'pain-relief', 'is_active' => true]);

        return [$staff, $pharmacy, $category];
    }
}