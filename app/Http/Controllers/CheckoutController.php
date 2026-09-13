<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Campaign;
use App\Models\Medicine;
use App\Models\Order;
use App\Services\WalletService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class CheckoutController extends Controller
{
    public function __construct(private WalletService $wallets)
    {
    }

    public function create(Request $request): View
    {
        $cart = Cart::with('items.medicine')->where('user_id', $request->user()->id)->where('status', 'active')->firstOrFail();
        abort_if($cart->items->isEmpty(), 302, redirect()->route('cart.index'));

        return view('checkout.create', compact('cart'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'delivery_address' => ['required', 'string', 'max:255'],
            'delivery_phone' => ['required', 'regex:/^[0-9]{10,15}$/'],
            'customer_notes' => ['nullable', 'string', 'max:1000'],
                    'payment_method' => ['nullable', 'in:cash_on_delivery,wallet'],
                    'promo_code' => ['nullable', 'string', 'max:50', 'alpha_dash'],
        ]);

        $order = DB::transaction(function () use ($request, $validated) {
            $cart = Cart::with('items')->where('user_id', $request->user()->id)->where('status', 'active')->lockForUpdate()->first();
            if (! $cart || $cart->items->isEmpty()) {
                throw ValidationException::withMessages(['cart' => 'Your cart is empty.']);
            }

            $lockedItems = [];
            $subtotal = 0;
            foreach ($cart->items as $cartItem) {
                $medicine = Medicine::whereKey($cartItem->medicine_id)->lockForUpdate()->first();
                if (! $medicine || ! $medicine->is_active || $medicine->stock_quantity < $cartItem->quantity) {
                    throw ValidationException::withMessages(['cart' => 'One or more medicines no longer have enough stock.']);
                }
                $unitPrice = (float) $medicine->price;
                $subtotal += $unitPrice * $cartItem->quantity;
                $lockedItems[] = [$cartItem, $medicine, $unitPrice];
            }

            $discount = 0;
            $campaign = null;
            if (! empty($validated['promo_code'])) {
                $campaign = Campaign::with('medicines')->where('promo_code', strtoupper($validated['promo_code']))->lockForUpdate()->first();
                if (! $campaign || $campaign->pharmacy_id !== $lockedItems[0][1]->pharmacy_id || ! $campaign->isCurrentlyValid()) {
                    throw ValidationException::withMessages(['promo_code' => 'This promo code is invalid, expired, or unavailable.']);
                }
                $campaignMedicineIds = $campaign->medicines->pluck('id');
                $eligibleSubtotal = collect($lockedItems)->filter(fn ($item) => $campaignMedicineIds->isEmpty() || $campaignMedicineIds->contains($item[1]->id))->sum(fn ($item) => $item[2] * $item[0]->quantity);
                $discount = $campaign->discountFor((float) $eligibleSubtotal);
            }

            $paymentMethod = $validated['payment_method'] ?? 'cash_on_delivery';
            $order = Order::create([
                'user_id' => $request->user()->id,
                'pharmacy_id' => $lockedItems[0][1]->pharmacy_id,
                'order_number' => 'ORD-'.now()->format('YmdHis').'-'.Str::upper(Str::random(5)),
                'status' => 'pending',
                'subtotal' => $subtotal,
                'discount_amount' => $discount,
                'payment_method' => $paymentMethod,
                'payment_status' => $paymentMethod === 'wallet' ? 'paid' : 'pending',
                'total_amount' => $subtotal - $discount,
                'delivery_address' => $validated['delivery_address'],
                'delivery_phone' => $validated['delivery_phone'],
                'customer_notes' => $validated['customer_notes'] ?? null,
            ]);

            if ($paymentMethod === 'wallet') {
                $this->wallets->adjust(
                    $this->wallets->customerWallet($request->user()),
                    'debit',
                    $subtotal - $discount,
                    'Payment for order '.$order->order_number,
                    $request->user(),
                    $order->order_number
                );
            }
            if ($campaign) {
                $campaign->increment('used_count');
            }

            foreach ($lockedItems as [$cartItem, $medicine, $unitPrice]) {
                $order->items()->create([
                    'medicine_id' => $medicine->id,
                    'medicine_name_snapshot' => $medicine->medicine_name,
                    'unit_price' => $unitPrice,
                    'quantity' => $cartItem->quantity,
                    'subtotal' => $unitPrice * $cartItem->quantity,
                ]);
                $medicine->decrement('stock_quantity', $cartItem->quantity);
            }
            $cart->update(['status' => 'checked_out']);

            return $order;
        });

        return redirect()->route('orders.show', $order)->with('success', 'Order placed successfully.');
    }
}