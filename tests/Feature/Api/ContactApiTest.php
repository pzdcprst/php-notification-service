<?php

namespace Tests\Feature\Api;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ContactApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_unauthenticated_contact_list_returns_401(): void
    {
        $this->getJson('/api/v1/contacts')->assertStatus(401);
    }

    public function test_authenticated_user_can_create_contact(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user, 'sanctum')->postJson('/api/v1/contacts', [
            'name' => 'Ada Lovelace',
            'email' => 'ada@example.test',
        ]);

        $response->assertCreated()->assertJsonPath('data.name', 'Ada Lovelace');
        $this->assertDatabaseHas('contacts', [
            'user_id' => $user->id,
            'name' => 'Ada Lovelace',
        ]);
    }

    public function test_user_cannot_read_another_users_contact(): void
    {
        $owner = User::factory()->create();
        $otherUser = User::factory()->create();
        $contact = $owner->contacts()->create(['name' => 'Private contact']);

        $this->actingAs($otherUser, 'sanctum')
            ->getJson('/api/v1/contacts/'.$contact->id)
            ->assertNotFound();
    }

    public function test_registration_returns_a_sanctum_token(): void
    {
        $this->postJson('/api/v1/register', [
            'name' => 'New User',
            'email' => 'new@example.test',
            'password' => 'strong-password',
            'password_confirmation' => 'strong-password',
        ])->assertCreated()->assertJsonStructure(['data' => ['token', 'user' => ['id', 'name', 'email']]]);

        $this->assertDatabaseCount('personal_access_tokens', 1);
    }
}
