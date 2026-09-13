<?php

namespace App\Policies;

use App\Models\Order;
use App\Models\User;

class OrderPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->is_active && in_array($user->role, ['admin', 'pharmacy_staff'], true);
    }

    public function view(User $user, Order $order): bool
    {
        return $user->is_active && ($user->role === 'admin'
            || $order->user_id === $user->id
            || ($user->role === 'pharmacy_staff' && $user->pharmacies()->whereKey($order->pharmacy_id)->exists()));
    }

    public function updateStatus(User $user, Order $order): bool
    {
        return $user->is_active && ($user->role === 'admin'
            || ($user->role === 'pharmacy_staff' && $user->pharmacies()->whereKey($order->pharmacy_id)->exists()));
    }
}