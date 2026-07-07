<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class WebhookVerificationTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function whatsapp_webhook_verification_succeeds_with_correct_token(): void
    {
        config(['whatsapp.verify_token' => 'my_secret_token']);

        $response = $this->get('/api/webhook/whatsapp?' . http_build_query([
            'hub_mode'         => 'subscribe',
            'hub_verify_token' => 'my_secret_token',
            'hub_challenge'    => 'challenge_123',
        ]));

        $response->assertStatus(200);
        $response->assertSee('challenge_123');
    }

    /** @test */
    public function whatsapp_webhook_verification_fails_with_wrong_token(): void
    {
        config(['whatsapp.verify_token' => 'correct_token']);

        $response = $this->get('/api/webhook/whatsapp?' . http_build_query([
            'hub_mode'         => 'subscribe',
            'hub_verify_token' => 'wrong_token',
            'hub_challenge'    => 'challenge_123',
        ]));

        $response->assertStatus(403);
    }

    /** @test */
    public function whatsapp_webhook_post_returns_200_immediately(): void
    {
        $payload = [
            'object' => 'whatsapp_business_account',
            'entry'  => [],
        ];

        $response = $this->postJson('/api/webhook/whatsapp', $payload);

        $response->assertStatus(200);
    }
}
