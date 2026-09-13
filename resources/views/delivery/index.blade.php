@extends('layouts.app', ['title' => 'My Deliveries'])
@section('content')
<div class="page-heading"><div class="eyebrow mb-2">Rider workspace</div><h1 class="h2 mb-0">My deliveries</h1><p class="text-secondary mt-2 mb-0">Update assigned delivery progress from pickup to completion.</p></div>
@if (session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
<div class="row g-4">
@forelse ($deliveries as $delivery)
<div class="col-md-6"><div class="card h-100"><div class="card-body p-4"><div class="d-flex justify-content-between"><span class="eyebrow">{{ $delivery->status }}</span><span class="badge text-bg-primary">{{ $delivery->order->order_number }}</span></div><h2 class="h5 mt-3">{{ $delivery->order->user->name }}</h2><p class="text-secondary mb-1">{{ $delivery->order->delivery_address }}</p><p class="text-secondary">{{ $delivery->order->delivery_phone }}</p>
@if ($delivery->status === 'assigned')<form method="POST" action="{{ route('delivery.status', $delivery) }}">@csrf @method('PATCH')<input name="status" type="hidden" value="picked_up"><button class="btn btn-primary" type="submit">Mark picked up</button></form>@elseif ($delivery->status === 'picked_up')<form method="POST" action="{{ route('delivery.status', $delivery) }}">@csrf @method('PATCH')<input name="status" type="hidden" value="delivered"><button class="btn btn-primary" type="submit">Mark delivered</button></form>@else<span class="text-success fw-semibold">Delivery completed</span>@endif
</div></div></div>
@empty
<div class="col-12"><div class="card"><div class="card-body text-center text-secondary py-5">No deliveries are assigned to you.</div></div></div>
@endforelse
</div><div class="mt-4">{{ $deliveries->links() }}</div>
@endsection
