@extends('layouts.app', ['title' => 'Login'])
@section('content')
<div class="row justify-content-center"><div class="col-md-6 col-lg-5"><div class="card border-0 shadow-sm"><div class="card-body p-4 p-md-5">
<div class="eyebrow mb-2">Welcome back</div><h1 class="h3 mb-2">Sign in to CityCare</h1><p class="text-secondary mb-4">Customers, pharmacy staff, riders, and admins all use their assigned email and password here.</p>
@if ($errors->any())<div class="alert alert-danger"><ul class="mb-0">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
<form method="POST" action="{{ route('login.store') }}" autocomplete="off">@csrf
<div class="mb-3"><label class="form-label" for="email">Email address</label><input class="form-control" id="email" name="email" type="email" value="{{ old('email') }}" autocomplete="off" required autofocus></div>
<div class="mb-3"><label class="form-label" for="password">Password</label><input class="form-control" id="password" name="password" type="password" autocomplete="off" required></div>
<div class="form-check mb-4"><input class="form-check-input" id="remember" name="remember" type="checkbox" autocomplete="off"><label class="form-check-label" for="remember">Remember me</label></div>
<button class="btn btn-primary w-100" type="submit">Login</button></form>
<div class="d-flex justify-content-between mt-3"><a class="small" href="{{ route('password.request') }}">Forgot password?</a><a class="small" href="{{ route('register') }}">Create customer account</a></div>
@if (session('status'))<div class="alert alert-success mt-4 mb-0">{{ session('status') }}</div>@endif
</div></div></div></div>
@endsection