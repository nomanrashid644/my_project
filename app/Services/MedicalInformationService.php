<?php

namespace App\Services;

use App\Contracts\MedicalInformationProvider;
use RuntimeException;

class MedicalInformationService
{
    public function provider(): ?MedicalInformationProvider
    {
        $provider = config('ai.provider');
        $key = config('ai.api_key');
        if (! $key || ! in_array($provider, ['groq', 'cerebras', 'openrouter'], true)) {
            return null;
        }
        $endpoints = [
            'groq' => 'https://api.groq.com/openai/v1/chat/completions',
            'cerebras' => 'https://api.cerebras.ai/v1/chat/completions',
            'openrouter' => 'https://openrouter.ai/api/v1/chat/completions',
        ];

        return new OpenAiCompatibleMedicalProvider($provider, $endpoints[$provider], $key, config('ai.model'), config('ai.timeout'));
    }

    public function systemPrompt(): string
    {
        return 'You provide general educational medical information only. Do not diagnose conditions, prescribe medicines or dosages, or tell anyone to stop prescribed treatment. Ask the user to consult a qualified healthcare professional. If symptoms suggest an emergency, advise contacting local emergency services immediately. Format every answer in clean Markdown: use a short heading, short paragraphs, bullet lists for options or safety points, and a small table only when comparison is genuinely useful. Keep answers concise and easy to scan. Do not use HTML.';
    }

    public function answer(array $history, string $message): array
    {
        $provider = $this->provider();
        if (! $provider) {
            throw new RuntimeException('AI chat is not configured. Add AI_PROVIDER and AI_API_KEY to the environment.');
        }
        return $provider->answer([
            ['role' => 'system', 'content' => $this->systemPrompt()],
            ...$history,
            ['role' => 'user', 'content' => $message],
        ]);
    }
}