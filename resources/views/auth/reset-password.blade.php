@extends('layouts.app', ['title' => 'Reset Password'])

@section('content')
<div class="row justify-content-center">
    <div class="col-md-6 col-lg-5">
        <div class="card">
            <div class="card-body p-4 p-md-5">
                <div class="eyebrow mb-2">Account recovery</div>
                <h1 class="h3 mb-4">Choose a new password</h1>
                @if ($errors->any())
                    <div class="alert alert-danger"><ul class="mb-0">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
                @endif
                <form method="POST" action="{{ route('password.update') }}">
                    @csrf
                    <input name="token" type="hidden" value="{{ $token }}">
                    <div class="mb-3"><label class="form-label" for="email">Email address</label><input class="form-control" id="email" name="email" type="email" value="{{ old('email', $email) }}" required></div>
                    <div class="mb-3"><label class="form-label" for="password">New password</label><input class="form-control" id="password" name="password" type="password" minlength="8" required></div>
                    <div class="mb-4"><label class="form-label" for="password_confirmation">Confirm password</label><input class="form-control" id="password_confirmation" name="password_confirmation" type="password" minlength="8" required></div>
                    <button class="btn btn-primary w-100" type="submit">Reset password</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
