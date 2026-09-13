<?php

namespace App\Http\Controllers;

use App\Models\DeliveryAssignment;
use App\Models\Order;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class AdminDeliveryController extends Controller
{
    public function assign(Request $request, Order $order): RedirectResponse
    {
        $validated = $request->validate(['rider_id' => ['required', 'exists:users,id']]);
        abort_unless(User::whereKey($validated['rider_id'])->where('role', 'rider')->where('is_active', true)->exists(), 422, 'Select an active rider.');
        DeliveryAssignment::updateOrCreate(
            ['order_id' => $order->id],
            ['rider_id' => $validated['rider_id'], 'status' => 'assigned', 'assigned_at' => now()]
        );

        return back()->with('success', 'Rider assigned successfully.');
    }
}