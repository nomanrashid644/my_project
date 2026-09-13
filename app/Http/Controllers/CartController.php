<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Medicine;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CartController extends Controller
{
    public function index(Request $request): View
    {
        $cart = Cart::firstOrCreate(['user_id' => $request->user()->id, 'status' => 'active']);
        $cart->load('items.medicine');

        return view('cart.index', compact('cart'));
    }

    public function store(Request $request, Medicine $medicine): RedirectResponse
    {
        $validated = $request->validate(['quantity' => ['required', 'integer', 'min:1']]);
        abort_unless($medicine->is_active, 404);

        if ($validated['quantity'] > $medicine->stock_quantity) {
            return back()->withErrors(['quantity' => 'Requested quantity is not available.']);
        }

        $cart = Cart::firstOrCreate(['user_id' => $request->user()->id, 'status' => 'active']);
        $item = $cart->items()->firstOrNew(['medicine_id' => $medicine->id]);
        $newQuantity = $item->exists ? $item->quantity + $validated['quantity'] : $validated['quantity'];
        if ($newQuantity > $medicine->stock_quantity) {
            return back()->withErrors(['quantity' => 'Cart quantity exceeds available stock.']);
        }
        $item->fill(['quantity' => $newQuantity, 'unit_price' => $medicine->price])->save();

        return redirect()->route('cart.index')->with('success', 'Medicine added to cart.');
    }

    public function update(Request $request, CartItem $cartItem): RedirectResponse
    {
        $this->ensureOwnItem($request, $cartItem);
        $validated = $request->validate(['quantity' => ['required', 'integer', 'min:1']]);
        abort_unless($validated['quantity'] <= $cartItem->medicine->stock_quantity, 422, 'Requested quantity is not available.');
        $cartItem->update(['quantity' => $validated['quantity'], 'unit_price' => $cartItem->medicine->price]);

        return back()->with('success', 'Cart updated.');
    }

    public function destroy(Request $request, CartItem $cartItem): RedirectResponse
    {
        $this->ensureOwnItem($request, $cartItem);
        $cartItem->delete();

        return back()->with('success', 'Item removed from cart.');
    }

    private function ensureOwnItem(Request $request, CartItem $cartItem): void
    {
        abort_unless($cartItem->cart->user_id === $request->user()->id && $cartItem->cart->status === 'active', 403);
    }
}