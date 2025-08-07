<?php

namespace Tests\Feature;

use App\Models\Agent;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ChatFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_trial_allows_three_messages_then_blocks(): void
    {
        $agent = Agent::factory()->create([
            'slug' => 'helper',
            'model' => 'gpt-4o-mini',
            'is_public' => true,
        ]);

        for ($i = 0; $i < 3; $i++) {
            $res = $this->postJson('/api/chat/helper/guest', ['message' => 'hi']);
            $res->assertStatus(200)->assertJsonStructure(['message','remaining_trial']);
        }
        $this->postJson('/api/chat/helper/guest', ['message' => 'hi'])->assertStatus(429);
    }

    public function test_authenticated_chat_deducts_credits_and_returns_response(): void
    {
        $user = User::factory()->create(['credits' => 10]);
        $agent = Agent::factory()->create([
            'slug' => 'writer','model' => 'gpt-4o-mini','is_public' => true
        ]);
        $this->actingAs($user);
        $res = $this->postJson('/api/chat/writer/send', ['message' => 'hello']);
        $res->assertStatus(200)->assertJsonStructure(['thread_id','message','deducted','remaining_credits']);
        $user->refresh();
        $this->assertLessThan(10, $user->credits);
    }

    public function test_tier_gating_prevents_low_credit_user(): void
    {
        $user = User::factory()->create(['credits' => 1]);
        $agent = Agent::factory()->create(['slug' => 'vip','model' => 'gpt','is_public' => true]);
        // Simulate tier min credits
        $agent->tiers()->create(['name' => 'VIP','slug' => 'vip','min_credits' => 100]);
        $this->actingAs($user);
        $this->postJson('/api/chat/vip/send', ['message' => 'hello'])->assertStatus(403);
    }
}
