<?php

namespace App\Services;

use App\Models\Campaign;
use App\Models\Medicine;
use App\Models\Order;
use App\Models\User;
use App\Models\WalletTransaction;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

class ReportService
{
    public function summary(?Carbon $from = null, ?Carbon $to = null): array
    {
        $orders = Order::query()->when($from, fn ($query) => $query->where('created_at', '>=', $from))->when($to, fn ($query) => $query->where('created_at', '<=', $to));
        $sales = (clone $orders)->where('status', 'delivered')->sum('total_amount');

        return [
            'users' => User::count(),
            'customers' => User::where('role', 'customer')->count(),
            'medicines' => Medicine::where('is_active', true)->count(),
            'low_stock' => Medicine::where('is_active', true)->whereBetween('stock_quantity', [1, 10])->count(),
            'out_of_stock' => Medicine::where('is_active', true)->where('stock_quantity', 0)->count(),
            'orders' => (clone $orders)->count(),
            'delivered_orders' => (clone $orders)->where('status', 'delivered')->count(),
            'sales' => (float) $sales,
            'active_campaigns' => Campaign::where('is_active', true)->where('starts_at', '<=', now())->where('ends_at', '>=', now())->count(),
        ];
    }

    public function ordersByStatus(?Carbon $from = null, ?Carbon $to = null): Collection
    {
        return Order::query()->selectRaw('status, COUNT(*) as total')->when($from, fn ($query) => $query->where('created_at', '>=', $from))->when($to, fn ($query) => $query->where('created_at', '<=', $to))->groupBy('status')->orderBy('status')->get();
    }

    public function topMedicines(?Carbon $from = null, ?Carbon $to = null): Collection
    {
        return Order::query()->join('order_items', 'orders.id', '=', 'order_items.order_id')->selectRaw('order_items.medicine_name_snapshot, SUM(order_items.quantity) as quantity, SUM(order_items.subtotal) as revenue')->where('orders.status', 'delivered')->when($from, fn ($query) => $query->where('orders.created_at', '>=', $from))->when($to, fn ($query) => $query->where('orders.created_at', '<=', $to))->groupBy('order_items.medicine_name_snapshot')->orderByDesc('quantity')->limit(10)->get();
    }

    public function walletMovement(?Carbon $from = null, ?Carbon $to = null): Collection
    {
        return WalletTransaction::query()->selectRaw('type, COUNT(*) as transactions, SUM(amount) as total')->when($from, fn ($query) => $query->where('created_at', '>=', $from))->when($to, fn ($query) => $query->where('created_at', '<=', $to))->groupBy('type')->orderBy('type')->get();
    }
}