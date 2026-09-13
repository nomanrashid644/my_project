<?php

namespace Tests\Feature;

use App\Models\AiChatSession;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class AiChatTest extends TestCase
{
    use RefreshDatabase;

    public function test_customer_sees_clear_unconfigured_state(): void
    {
        $customer = User::factory()->create(['role' => 'customer', 'is_active' => true]);
        Config::set(['ai.provider' => 'none', 'ai.api_key' => null]);

        $this->actingAs($customer)->get(route('ai-chat.index'))
            ->assertOk()
            ->assertSee('AI chat is not configured yet');
    }

    public function test_unconfigured_chat_fails_gracefully_and_stores_no_external_call(): void
    {
        $customer = User::factory()->create(['role' => 'customer', 'is_active' => true]);
        Config::set('ai.provider', 'none');
        Http::fake();

        $this->actingAs($customer)->post(route('ai-chat.store'), ['message' => 'What is a fever?'])
            ->assertRedirect()
            ->assertSessionHasErrors('message');
        Http::assertNothingSent();
        $this->assertDatabaseHas('ai_chat_messages', ['role' => 'user', 'message' => 'What is a fever?']);
    }

    public function test_configured_provider_response_is_saved(): void
    {
        $customer = User::factory()->create(['role' => 'customer', 'is_active' => true]);
        Config::set(['ai.provider' => 'groq', 'ai.api_key' => 'test-key', 'ai.model' => 'test-model']);
        Http::fake(['https://api.groq.com/*' => Http::response(['choices' => [['message' => ['content' => "## General information\n\n| Option | Use |\n| --- | --- |\n| Rest | Recovery |\n\n- Stay hydrated"]]], 'usage' => ['total_tokens' => 12]], 200)]);

        $this->actingAs($customer)->post(route('ai-chat.store'), ['message' => 'What is a fever?'])
            ->assertRedirect();
        $this->assertDatabaseHas('ai_chat_messages', ['role' => 'assistant', 'provider' => 'groq']);
        $this->get(route('ai-chat.index'))->assertSee('<table>', false)->assertSee('Stay hydrated');
        $this->assertDatabaseCount('ai_chat_sessions', 1);
    }

    public function test_non_customer_cannot_use_ai_chat(): void
    {
        $staff = User::factory()->create(['role' => 'pharmacy_staff', 'is_active' => true]);

        $this->actingAs($staff)->get(route('ai-chat.index'))->assertForbidden();
    }

    public function test_customer_can_clear_only_their_saved_chat(): void
    {
        $customer = User::factory()->create(['role' => 'customer', 'is_active' => true]);
        $otherCustomer = User::factory()->create(['role' => 'customer', 'is_active' => true]);
        $session = $customer->aiChatSessions()->create(['title' => 'My chat']);
        $session->messages()->create(['role' => 'user', 'message' => 'Private question']);
        $otherSession = $otherCustomer->aiChatSessions()->create(['title' => 'Other chat']);
        $otherSession->messages()->create(['role' => 'user', 'message' => 'Keep this question']);

        $this->actingAs($customer)->delete(route('ai-chat.clear'))
            ->assertRedirect(route('ai-chat.index'))
            ->assertSessionHas('success');
        $this->assertDatabaseMissing('ai_chat_sessions', ['id' => $session->id]);
        $this->assertDatabaseHas('ai_chat_sessions', ['id' => $otherSession->id]);
    }
}