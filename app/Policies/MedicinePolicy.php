<?php

namespace App\Policies;

use App\Models\Medicine;
use App\Models\User;

class MedicinePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->is_active;
    }

    public function view(User $user, Medicine $medicine): bool
    {
        return $user->is_active;
    }

    public function create(User $user): bool
    {
        return $this->canManage($user);
    }

    public function update(User $user, Medicine $medicine): bool
    {
        return $this->canManage($user) && $this->ownsPharmacyRecord($user, $medicine);
    }

    public function delete(User $user, Medicine $medicine): bool
    {
        return $this->canManage($user) && $this->ownsPharmacyRecord($user, $medicine);
    }

    private function canManage(User $user): bool
    {
        return $user->is_active && in_array($user->role, ['admin', 'pharmacy_staff'], true);
    }

    private function ownsPharmacyRecord(User $user, Medicine $medicine): bool
    {
        return $user->role === 'admin' || $user->pharmacies()->whereKey($medicine->pharmacy_id)->exists();
    }
}