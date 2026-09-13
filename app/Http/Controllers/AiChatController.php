<?php

namespace App\Http\Controllers;

use App\Models\AiChatSession;
use App\Services\MedicalInformationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;
use RuntimeException;

class AiChatController extends Controller
{
    public function __construct(private MedicalInformationService $medicalInformation)
    {
    }

    public function index(Request $request): View
    {
        $session = $request->user()->aiChatSessions()->latest()->first();
        $messages = $session?->messages()->oldest()->get() ?? collect();

        return view('ai-chat.index', [
            'session' => $session,
            'messages' => $messages,
            'configured' => $this->medicalInformation->provider() !== null,
            'maxLength' => config('ai.max_message_length'),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'message' => ['required', 'string', 'max:'.config('ai.max_message_length')],
            'session_id' => ['nullable', 'integer'],
        ]);
        $session = $this->sessionFor($request, $validated['session_id'] ?? null);
        $message = trim($validated['message']);
        $session->messages()->create(['role' => 'user', 'message' => $message]);
        $history = $session->messages()->oldest()->get()->map(fn ($item) => ['role' => $item->role, 'content' => $item->message])->all();

        try {
            $answer = $this->medicalInformation->answer(array_slice($history, 0, -1), $message);
            $session->messages()->create(['role' => 'assistant', 'message' => $answer['message'], 'provider' => $this->medicalInformation->provider()?->name(), 'tokens_used' => $answer['tokens_used']]);
        } catch (RuntimeException $exception) {
            return back()->withErrors(['message' => $exception->getMessage()])->withInput();
        }

        return redirect()->route('ai-chat.index', ['session' => $session->id]);
    }

    public function clear(Request $request): RedirectResponse
    {
        $request->user()->aiChatSessions()->delete();

        return redirect()->route('ai-chat.index')->with('success', 'Your saved AI conversation was cleared.');
    }

    private function sessionFor(Request $request, ?int $sessionId): AiChatSession
    {
        if ($sessionId) {
            return $request->user()->aiChatSessions()->whereKey($sessionId)->firstOrFail();
        }

        return $request->user()->aiChatSessions()->create(['title' => Str::limit(trim($request->string('message')->toString()), 60)]);
    }
}