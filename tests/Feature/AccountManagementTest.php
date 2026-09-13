<?php

namespace Tests\Feature;

use App\Models\Pharmacy;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AccountManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_staff_account_assigned_to_pharmacy(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);
        $pharmacy = Pharmacy::create(['name' => 'Test Pharmacy', 'slug' => 'test-pharmacy', 'is_active' => true]);

        $this->actingAs($admin)->post(route('admin.users.store'), [
            'name' => 'New Staff', 'email' => 'newstaff@example.com', 'phone_number' => '03001234567',
            'password' => 'TestPassword123!', 'password_confirmation' => 'TestPassword123!',
            'role' => 'pharmacy_staff', 'pharmacy_id' => $pharmacy->id,
        ])->assertRedirect(route('admin.users.index'));

        $staff = User::where('email', 'newstaff@example.com')->firstOrFail();
        $this->assertSame('pharmacy_staff', $staff->role);
        $this->assertTrue($staff->pharmacies->contains($pharmacy));
    }

    public function test_user_must_verify_current_password_to_change_password(): void
    {
        $user = User::factory()->create(['password' => 'OldPassword123', 'role' => 'customer']);

        $this->actingAs($user)->put(route('profile.update'), [
            'name' => $user->name, 'phone_number' => '03001234567', 'password' => 'NewPassword123',
            'password_confirmation' => 'NewPassword123',
        ])->assertSessionHasErrors('current_password');
        $this->assertTrue(Hash::check('OldPassword123', $user->fresh()->password));

        $this->actingAs($user)->put(route('profile.update'), [
            'name' => 'Updated Name', 'phone_number' => '03001234567', 'current_password' => 'OldPassword123',
            'password' => 'NewPassword123', 'password_confirmation' => 'NewPassword123',
        ])->assertRedirect();
        $this->assertTrue(Hash::check('NewPassword123', $user->fresh()->password));
    }
}