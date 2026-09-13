<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="CityCare online pharmacy for trusted medicines and everyday health essentials.">
    <title>{{ config('app.name', 'Online Quick Pharmacy') }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Manrope:wght@700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root { --ink: #173b3f; --mint: #dff3e8; --teal: #087f7b; --coral: #ef7258; --cream: #fffaf3; --line: #dce9e1; }
        * { box-sizing: border-box; }
        body { margin: 0; color: var(--ink); background: var(--cream); font-family: 'DM Sans', sans-serif; }
        h1, h2, h3, .brand { font-family: 'Manrope', sans-serif; }
        .topline { background: var(--ink); color: #e9f7ee; font-size: .82rem; letter-spacing: .02em; }
        .navbar { background: rgba(255,250,243,.96); border-bottom: 1px solid var(--line); }
        .brand { color: var(--ink); font-size: 1.3rem; text-decoration: none; }
        .brand-mark { display: inline-grid; width: 34px; height: 34px; margin-right: 8px; place-items: center; border-radius: 10px; color: white; background: var(--teal); font-family: 'DM Sans', sans-serif; font-weight: 700; }
        .nav-link { color: var(--ink); font-weight: 600; }
        .hero { overflow: hidden; background: var(--mint); }
        .hero-copy { padding: 86px 0 92px; }
        .eyebrow { color: var(--coral); font-size: .78rem; font-weight: 700; letter-spacing: .16em; text-transform: uppercase; }
        .hero h1 { max-width: 650px; font-size: clamp(2.7rem, 5vw, 5.2rem); line-height: 1.02; letter-spacing: -.04em; }
        .hero p { max-width: 540px; color: #47666a; font-size: 1.12rem; }
        .btn-teal { border: 0; color: #fff; background: var(--teal); }
        .btn-teal:hover { color: #fff; background: #05645f; }
        .hero-art { min-height: 430px; background: linear-gradient(135deg, rgba(8,127,123,.08), rgba(239,114,88,.18)), url('https://images.unsplash.com/photo-1584308666744-24d5c474f2ae?auto=format&fit=crop&w=1000&q=85') center/cover; }
        .trust-row { border-bottom: 1px solid var(--line); background: #fffdf8; }
        .trust-item { padding: 23px 14px; color: #4a6768; font-size: .93rem; }
        .trust-icon { color: var(--coral); font-size: 1.35rem; }
        .section { padding: 76px 0; }
        .section-heading { max-width: 610px; }
        .section-heading h2 { font-size: clamp(2rem, 3vw, 3rem); letter-spacing: -.035em; }
        .category { display: block; height: 100%; padding: 24px; color: var(--ink); text-decoration: none; background: #fff; border: 1px solid var(--line); border-radius: 5px; transition: transform .2s, border-color .2s; }
        .category:hover { border-color: var(--teal); transform: translateY(-4px); }
        .category-icon { display: grid; width: 52px; height: 52px; margin-bottom: 22px; place-items: center; border-radius: 50%; color: var(--teal); background: var(--mint); font-size: 1.5rem; }
        .category h3 { font-size: 1.05rem; }
        .category p { margin: 0; color: #6a8080; font-size: .9rem; }
        .feature-image { min-height: 370px; background: url('https://images.unsplash.com/photo-1585435557343-3b092031a831?auto=format&fit=crop&w=1000&q=85') center/cover; }
        .feature-copy { padding: 48px; background: var(--ink); color: white; }
        .feature-copy p { color: #c5d8d2; }
        .feature-list { padding: 0; list-style: none; color: #e6f3ec; }
        .feature-list li { margin-top: 16px; }
        .feature-list span { display: inline-grid; width: 23px; height: 23px; margin-right: 8px; place-items: center; border-radius: 50%; color: var(--ink); background: #9ed9ba; font-size: .75rem; }
        .medicine-card { height: 100%; padding: 22px; background: white; border: 1px solid var(--line); }
        .medicine-card .pill { display: inline-block; padding: 5px 9px; color: var(--teal); background: var(--mint); font-size: .72rem; font-weight: 700; text-transform: uppercase; }
        .medicine-card h3 { margin: 22px 0 8px; font-size: 1.1rem; }
        .medicine-card p { min-height: 42px; color: #6a8080; font-size: .88rem; }
        .price { color: var(--coral); font-size: 1.2rem; font-weight: 700; }
        .cta { padding: 65px 0; background: #f8d4c6; }
        footer { padding: 46px 0 24px; color: #d2e6dc; background: var(--ink); }
        footer a { color: #d2e6dc; text-decoration: none; }
        .footer-note { border-top: 1px solid rgba(210,230,220,.2); margin-top: 34px; padding-top: 20px; color: #91b4aa; font-size: .8rem; }
        @media (max-width: 767px) { .hero-copy { padding: 58px 0; } .hero-art { min-height: 270px; } .feature-copy { padding: 32px 24px; } .nav-actions { margin-top: 14px; } }
    </style>
</head>
<body>
    <div class="topline py-2"><div class="container d-flex justify-content-between"><span>Trusted care, delivered with clarity</span><span class="d-none d-md-inline">CityCare Pharmacy · Wah Cantt</span></div></div>
    <nav class="navbar navbar-expand-lg sticky-top">
        <div class="container py-2">
            <a class="brand" href="{{ url('/') }}"><span class="brand-mark">+</span>CityCare</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav" aria-label="Toggle navigation"><span class="navbar-toggler-icon"></span></button>
            <div class="collapse navbar-collapse" id="mainNav">
                <div class="navbar-nav ms-auto align-items-lg-center gap-lg-3 nav-actions">
                    <a class="nav-link" href="#categories">Categories</a>
                    <a class="nav-link" href="#services">Our services</a>
                    <a class="nav-link" href="#medicines">Medicines</a>
                    @auth <a class="btn btn-teal px-4" href="{{ route('dashboard') }}">Dashboard</a> @else <a class="btn btn-teal px-4" href="{{ route('login') }}">Login</a><a class="btn btn-outline-dark px-4" href="{{ route('register') }}">Register</a> @endauth
                </div>
            </div>
        </div>
    </nav>
    <header class="hero"><div class="container"><div class="row align-items-stretch"><div class="col-lg-7"><div class="hero-copy"><div class="eyebrow mb-3">Everyday health, made easier</div><h1>Care that comes to your door.</h1><p class="mt-4 mb-4">Find trusted medicines, check availability, and place your order from CityCare Pharmacy in a few simple steps.</p><div class="d-flex flex-wrap gap-2"><a class="btn btn-teal btn-lg px-4" href="{{ route('medicines.index') }}">Browse medicines</a><a class="btn btn-outline-dark btn-lg px-4" href="#services">Explore services</a></div></div></div><div class="col-lg-5 hero-art"></div></div></div></header>
    <div class="trust-row"><div class="container"><div class="row text-center"><div class="col-md-3 trust-item"><span class="trust-icon">✓</span> Genuine products</div><div class="col-md-3 trust-item"><span class="trust-icon">⌁</span> Fast local delivery</div><div class="col-md-3 trust-item"><span class="trust-icon">▣</span> Secure checkout</div><div class="col-md-3 trust-item"><span class="trust-icon">♡</span> Human-first care</div></div></div></div>
    <section class="section" id="categories"><div class="container"><div class="section-heading mb-5"><div class="eyebrow mb-2">Shop by need</div><h2>Good health starts with the essentials.</h2><p class="text-secondary">Explore our organized medicine catalog and find what you need without the guesswork.</p></div><div class="row g-3"><div class="col-6 col-lg-3"><a class="category" href="{{ route('medicines.index') }}?category=1"><span class="category-icon">+</span><h3>Pain relief</h3><p>Comfort for everyday aches</p></a></div><div class="col-6 col-lg-3"><a class="category" href="{{ route('medicines.index') }}?category=4"><span class="category-icon">◌</span><h3>Diabetes care</h3><p>Reliable daily support</p></a></div><div class="col-6 col-lg-3"><a class="category" href="{{ route('medicines.index') }}?category=7"><span class="category-icon">✦</span><h3>Vitamins</h3><p>Simple wellness essentials</p></a></div><div class="col-6 col-lg-3"><a class="category" href="{{ route('medicines.index') }}?category=9"><span class="category-icon">○</span><h3>Skin care</h3><p>Everyday personal care</p></a></div></div></div></section>
    <section class="section pt-0" id="services"><div class="container"><div class="row g-0"><div class="col-lg-6 feature-image"></div><div class="col-lg-6 feature-copy"><div class="eyebrow mb-3">More than a medicine list</div><h2 class="mb-3">A calmer way to manage your health.</h2><p>From finding an item to following an order, CityCare keeps the important details visible and simple.</p><ul class="feature-list mt-4"><li><span>✓</span> Clear prices and live stock availability</li><li><span>✓</span> Convenient cart and checkout experience</li><li><span>✓</span> Order updates from confirmation to delivery</li><li><span>✓</span> General health information assistant</li></ul><a class="btn btn-light mt-3 px-4" href="{{ route('register') }}">Start your account</a></div></div></div></section>
    <section class="section pt-0" id="medicines"><div class="container"><div class="section-heading mb-5"><div class="eyebrow mb-2">Featured catalog</div><h2>Ready when you are.</h2></div><div class="row g-3">@php($featured = \App\Models\Medicine::with('category')->where('is_active', true)->orderBy('medicine_name')->limit(3)->get()) @forelse($featured as $medicine)<div class="col-md-4"><div class="medicine-card"><span class="pill">{{ $medicine->category->name }}</span><h3>{{ $medicine->medicine_name }}</h3><p>{{ $medicine->description ?? 'Quality medicine from CityCare Pharmacy.' }}</p><div class="d-flex justify-content-between align-items-center"><span class="price">Rs {{ number_format((float) $medicine->price, 2) }}</span><a class="btn btn-sm btn-outline-dark" href="{{ route('medicines.show', $medicine) }}">View details</a></div></div></div>@empty<div class="col-12"><p class="text-secondary">Our catalog is being refreshed. Please check back soon.</p></div>@endforelse</div></div></section>
    <section class="cta"><div class="container text-center"><div class="eyebrow mb-2">Your health, your pace</div><h2 class="mb-3">Ready to make pharmacy simpler?</h2><p class="text-secondary mb-4">Create an account to browse, order, and keep your essentials close.</p><a class="btn btn-teal btn-lg px-5" href="{{ route('register') }}">Create free account</a></div></section>
    <footer><div class="container"><div class="row g-4"><div class="col-lg-5"><a class="brand text-white" href="{{ url('/') }}"><span class="brand-mark">+</span>CityCare</a><p class="mt-3 mb-0">A dependable online pharmacy experience for everyday care.</p></div><div class="col-6 col-lg-3"><h3 class="h6 text-white">Explore</h3><p class="mb-2"><a href="#categories">Categories</a></p><p class="mb-2"><a href="#services">Services</a></p><p><a href="{{ route('medicines.index') }}">Medicine catalog</a></p></div><div class="col-6 col-lg-4"><h3 class="h6 text-white">CityCare Pharmacy</h3><p class="mb-2">Wah Cantt</p><p class="mb-0">Open for your everyday health needs.</p></div></div><div class="footer-note">© {{ date('Y') }} CityCare. General information only; consult a qualified healthcare professional for medical advice.</div></div></footer>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
