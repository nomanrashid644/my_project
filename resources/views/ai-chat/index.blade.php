@extends('layouts.app', ['title' => 'Medical Information'])

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-9">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <p class="text-primary text-uppercase small fw-bold mb-1">General information</p>
                <h1 class="h2 mb-0">Medical information assistant</h1>
            </div>
            <div class="d-flex align-items-center gap-3">
                @if ($session)
                    <form method="POST" action="{{ route('ai-chat.clear') }}" onsubmit="return confirm('Clear all saved AI questions and answers?')">
                        @csrf
                        @method('DELETE')
                        <button class="btn btn-sm btn-outline-danger" type="submit">Clear conversation</button>
                    </form>
                @endif
                <a href="{{ route('dashboard') }}">Back to dashboard</a>
            </div>
        </div>

        <div class="alert alert-warning">
            <strong>Important:</strong> This assistant provides general educational information only.
            It does not diagnose conditions, prescribe medicines, or replace a qualified healthcare professional.
            For emergency symptoms, contact local emergency services immediately.
        </div>

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if (! $configured)
            <div class="alert alert-secondary">
                AI chat is not configured yet. Add <code>AI_PROVIDER</code> and <code>AI_API_KEY</code> to the environment before sending requests.
            </div>
        @endif

        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body chat-window" style="min-height: 280px">
                @forelse ($messages as $message)
                    <div class="mb-3">
                        <p class="small text-muted mb-1">{{ ucfirst($message->role) }}</p>
                        <div class="message-bubble p-3 rounded bg-{{ $message->role === 'user' ? 'primary text-white' : 'light' }}">
                            @if ($message->role === 'assistant')
                                {!! $message->formatted_message !!}
                            @else
                                {{ $message->message }}
                            @endif
                        </div>
                    </div>
                @empty
                    <p class="text-muted text-center mt-5">Ask a general health-information question to begin.</p>
                @endforelse
            </div>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <form method="POST" action="{{ route('ai-chat.store') }}">
                    @csrf
                    @if ($session)
                        <input name="session_id" type="hidden" value="{{ $session->id }}">
                    @endif
                    <label class="form-label" for="message">Your question</label>
                    <textarea class="form-control mb-3" id="message" name="message" rows="4" maxlength="{{ $maxLength }}" required placeholder="Describe your question without sharing unnecessary personal details.">{{ old('message') }}</textarea>
                    <button class="btn btn-primary" type="submit" @if (! $configured) disabled @endif>Send question</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
