<?php

namespace App\Services;

use App\Contracts\MedicalInformationProvider;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\ConnectionException;
use RuntimeException;

class OpenAiCompatibleMedicalProvider implements MedicalInformationProvider
{
    public function __construct(private string $provider, private string $endpoint, private string $apiKey, private string $model, private int $timeout)
    {
    }

    public function name(): string
    {
        return $this->provider;
    }

    public function answer(array $messages): array
    {
        try {
            $response = Http::withToken($this->apiKey)->timeout($this->timeout)->acceptJson()->post($this->endpoint, [
                'model' => $this->model,
                'messages' => $messages,
                'temperature' => 0.2,
                'max_tokens' => 600,
            ]);
        } catch (ConnectionException $exception) {
            throw new RuntimeException('The AI provider could not be reached. Check your internet connection and PHP SSL certificate configuration.', 0, $exception);
        }
        if ($response->failed()) {
            $providerMessage = $response->json('error.message');
            $detail = is_string($providerMessage) ? ' '.$providerMessage : '';
            throw new RuntimeException('The AI provider rejected the request (HTTP '.$response->status().').'.$detail);
        }
        $message = $response->json('choices.0.message.content');
        if (! is_string($message) || trim($message) === '') {
            throw new RuntimeException('The AI provider returned an invalid response.');
        }

        return ['message' => trim($message), 'tokens_used' => $response->json('usage.total_tokens')];
    }
}