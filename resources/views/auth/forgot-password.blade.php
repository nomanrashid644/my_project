@extends('layouts.app', ['title' => 'Forgot Password'])

@section('content')
<div class="row justify-content-center">
    <div class="col-md-6 col-lg-5">
        <div class="card">
            <div class="card-body p-4 p-md-5">
                <div class="eyebrow mb-2">Account recovery</div>
                <h1 class="h3 mb-3">Reset your password</h1>
                <p class="text-secondary">Enter your login email. If it belongs to an account, a reset link will be sent through the configured mail service.</p>
                @if ($errors->any())
                    <div class="alert alert-danger"><ul class="mb-0">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
                @endif
                @if (session('status'))
                    <div class="alert alert-success">{{ session('status') }}</div>
                @endif
                <form method="POST" action="{{ route('password.email') }}">
                    @csrf
                    <div class="mb-4"><label class="form-label" for="email">Email address</label><input class="form-control" id="email" name="email" type="email" value="{{ old('email') }}" required autofocus></div>
                    <button class="btn btn-primary w-100" type="submit">Send reset link</button>
                </form>
                <p class="mt-4 mb-0"><a href="{{ route('login') }}">Back to login</a></p>
            </div>
        </div>
    </div>
</div>
@endsection
