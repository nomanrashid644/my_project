<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? config('app.name') }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Manrope:wght@700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root { --ink:#173b3f; --mint:#dff3e8; --teal:#087f7b; --coral:#ef7258; --cream:#fffaf3; --line:#dce9e1; }
        body { color:var(--ink); background:var(--cream); font-family:'DM Sans',sans-serif; }
        h1,h2,h3,.navbar-brand { font-family:'Manrope',sans-serif; }
        .navbar { background:var(--ink)!important; }
        .navbar-brand { letter-spacing:-.03em; }
        .nav-link { color:#dceee5!important; font-weight:600; }
        .nav-link:hover { color:#fff!important; }
        .page-shell { min-height:calc(100vh - 72px); }
        .page-heading { padding:34px 0 24px; border-bottom:1px solid var(--line); margin-bottom:30px; }
        .eyebrow { color:var(--coral); font-size:.74rem; font-weight:700; letter-spacing:.14em; text-transform:uppercase; }
        .card { border:1px solid var(--line)!important; border-radius:6px!important; box-shadow:0 8px 24px rgba(23,59,63,.06)!important; }
        .btn-primary { border-color:var(--teal); background:var(--teal); }
        .btn-primary:hover { border-color:#05645f; background:#05645f; }
        .text-primary { color:var(--teal)!important; }
        .form-control,.form-select { border-color:#cbded4; border-radius:4px; }
        .form-control:focus,.form-select:focus { border-color:var(--teal); box-shadow:0 0 0 .2rem rgba(8,127,123,.14); }
        .chat-window { max-height: 560px; overflow-y: auto; }
        .message-bubble { line-height: 1.6; }
        .message-bubble h1,.message-bubble h2,.message-bubble h3 { margin-top: .2rem; margin-bottom: .7rem; font-size: 1.05rem; }
        .message-bubble p { margin-bottom: .7rem; }
        .message-bubble ul,.message-bubble ol { padding-left: 1.25rem; margin-bottom: .75rem; }
        .message-bubble table { width: 100%; margin: .8rem 0; border-collapse: collapse; font-size: .92rem; }
        .message-bubble th,.message-bubble td { padding: .55rem .65rem; border: 1px solid var(--line); text-align: left; vertical-align: top; }
        .message-bubble th { background: var(--mint); font-weight: 700; }
        .message-bubble blockquote { margin: .8rem 0; padding-left: 1rem; border-left: 3px solid var(--coral); color: #527171; }
        .message-bubble code { padding: .1rem .3rem; color: var(--ink); background: #e9f2ec; }
    </style>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark shadow-sm">
    <div class="container">
        <a class="navbar-brand fw-bold" href="{{ url('/') }}"><span class="d-inline-grid me-2" style="width:30px;height:30px;place-items:center;border-radius:8px;color:var(--ink);background:var(--mint)">+</span>CityCare</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#appNav" aria-label="Toggle navigation"><span class="navbar-toggler-icon"></span></button>
        <div class="collapse navbar-collapse" id="appNav">
        <div class="navbar-nav ms-auto align-items-lg-center gap-lg-2">
            @auth
                <a class="nav-link" href="{{ route('dashboard') }}">Dashboard</a>
                <a class="nav-link" href="{{ route('medicines.index') }}">Medicines</a>
                @if (auth()->user()->role === 'customer')
                    <a class="nav-link" href="{{ route('cart.index') }}">Cart</a><a class="nav-link" href="{{ route('orders.index') }}">Orders</a><a class="nav-link" href="{{ route('wallet.index') }}">Wallet</a><a class="nav-link" href="{{ route('campaigns.index') }}">Deals</a><a class="nav-link" href="{{ route('ai-chat.index') }}">Medical AI</a>
                @endif
                @if (auth()->user()->role === 'admin')<a class="nav-link" href="{{ route('admin.dashboard') }}">Admin</a>@endif
                @if (auth()->user()->role === 'rider')<a class="nav-link" href="{{ route('delivery.index') }}">My deliveries</a>@endif
                <a class="nav-link" href="{{ route('profile.edit') }}">{{ auth()->user()->name }}</a>
                <form method="POST" action="{{ route('logout') }}">@csrf<button class="btn btn-sm btn-light" type="submit">Logout</button></form>
            @else
                <a class="nav-link" href="{{ route('login') }}">Login</a><a class="btn btn-sm btn-light" href="{{ route('register') }}">Register</a>
            @endauth
        </div>
        </div>
    </div>
</nav>
<main class="container page-shell py-4">
    @if (session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
    @yield('content')
</main>
<footer class="py-4 mt-5" style="background:var(--ink);color:#c5d8d2"><div class="container d-flex flex-wrap justify-content-between gap-2 small"><span>CityCare · Wah Cantt</span><span>General information only. Consult a qualified healthcare professional for medical advice.</span></div></footer>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>