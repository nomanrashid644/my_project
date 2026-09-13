@extends('layouts.app', ['title' => 'Register'])
@section('content')
<div class="row justify-content-center"><div class="col-md-7 col-lg-6"><div class="card border-0 shadow-sm"><div class="card-body p-4 p-md-5">
<h1 class="h3 mb-4">Create customer account</h1>
@if ($errors->any())<div class="alert alert-danger"><ul class="mb-0">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
<form method="POST" action="{{ route('register.store') }}" autocomplete="off">@csrf
<div class="mb-3"><label class="form-label" for="name">Full name</label><input class="form-control" id="name" name="name" value="{{ old('name') }}" autocomplete="off" required maxlength="50"></div>
<div class="mb-3"><label class="form-label" for="email">Email address</label><input class="form-control" id="email" name="email" type="email" value="{{ old('email') }}" autocomplete="off" required></div>
<div class="mb-3"><label class="form-label" for="phone_number">Phone number</label><input class="form-control" id="phone_number" name="phone_number" value="{{ old('phone_number') }}" autocomplete="off" required maxlength="15"></div>
<div class="mb-3"><label class="form-label" for="password">Password</label><input class="form-control" id="password" name="password" type="password" autocomplete="off" required minlength="8"></div>
<div class="mb-4"><label class="form-label" for="password_confirmation">Confirm password</label><input class="form-control" id="password_confirmation" name="password_confirmation" type="password" autocomplete="off" required minlength="8"></div>
<button class="btn btn-primary w-100" type="submit">Register</button></form>
<p class="text-muted mt-4 mb-0">Already registered? <a href="{{ route('login') }}">Sign in</a></p>
</div></div></div></div>
@endsection