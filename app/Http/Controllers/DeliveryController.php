<?php

namespace App\Http\Controllers;

use App\Models\DeliveryAssignment;
use App\Models\Order;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DeliveryController extends Controller
{
    public function index(Request $request): View
    {
        $deliveries = $request->user()->deliveryAssignments()->with('order.user')->latest()->paginate(15);

        return view('delivery.index', compact('deliveries'));
    }

    public function updateStatus(Request $request, DeliveryAssignment $delivery): RedirectResponse
    {
        abort_unless($delivery->rider_id === $request->user()->id, 403);
        $validated = $request->validate(['status' => ['required', 'in:picked_up,delivered']]);
        $allowed = $delivery->status === 'assigned' && $validated['status'] === 'picked_up'
            || $delivery->status === 'picked_up' && $validated['status'] === 'delivered';
        abort_unless($allowed, 422, 'Invalid delivery status transition.');

        $timestamp = $validated['status'] === 'picked_up' ? ['picked_up_at' => now()] : ['delivered_at' => now()];
        DB::transaction(function () use ($delivery, $validated, $timestamp): void {
            $delivery->update(['status' => $validated['status'], ...$timestamp]);
            $delivery->order->update(['status' => $validated['status'] === 'delivered' ? 'delivered' : 'dispatched', ...$timestamp]);
        });

        return back()->with('success', 'Delivery status updated.');
    }
}