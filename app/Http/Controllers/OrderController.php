<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Notifications\OrderStatusNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OrderController extends Controller
{
    public function index(Request $request): View
    {
        if (in_array($request->user()->role, ['admin', 'pharmacy_staff'], true)) {
            $this->authorize('viewAny', Order::class);
            $orders = Order::with(['user', 'pharmacy'])->when(
                $request->user()->role === 'pharmacy_staff',
                fn ($query) => $query->whereIn('pharmacy_id', $request->user()->pharmacies()->pluck('pharmacies.id'))
            )->latest()->paginate(15);
        } else {
            $orders = $request->user()->orders()->with('pharmacy')->latest()->paginate(10);
        }

        return view('orders.index', ['orders' => $orders, 'management' => in_array($request->user()->role, ['admin', 'pharmacy_staff'], true)]);
    }

    public function show(Request $request, Order $order): View
    {
        $this->authorize('view', $order);
        $order->load(['items', 'pharmacy']);

        return view('orders.show', compact('order'));
    }

    public function updateStatus(Request $request, Order $order): RedirectResponse
    {
        $this->authorize('updateStatus', $order);
        $validated = $request->validate(['status' => ['required', 'in:'.implode(',', Order::STATUSES)]]);

        if (! $order->canTransitionTo($validated['status'])) {
            return back()->withErrors(['status' => "An order cannot move from {$order->status} to {$validated['status']}."]);
        }

        $status = $validated['status'];
        $timestamps = match ($status) {
            'confirmed' => ['confirmed_at' => now()],
            'dispatched' => ['dispatched_at' => now()],
            'delivered' => ['delivered_at' => now()],
            'cancelled' => ['cancelled_at' => now()],
            default => [],
        };
        $order->update(['status' => $status, ...$timestamps]);
        $order->user->notify(new OrderStatusNotification($order));

        return back()->with('success', 'Order status updated.');
    }
}