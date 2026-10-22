<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NotebookFeatureTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_to_login(): void
    {
        $this->get('/dashboard')->assertRedirect(route('login'));
    }

    public function test_user_can_register_and_open_dashboard(): void
    {
        $this->withoutVite();

        $this->post(route('register.store'), [
            'name' => 'Ada Lovelace',
            'email' => 'ada@example.test',
            'password' => 'a-long-strong-password',
            'password_confirmation' => 'a-long-strong-password',
        ])->assertRedirect(route('dashboard'));

        $this->assertAuthenticated();
        $this->get(route('dashboard'))->assertOk()->assertSee('Ada');
    }

    public function test_user_cannot_delete_another_users_contact(): void
    {
        $owner = User::factory()->create();
        $otherUser = User::factory()->create();
        $contact = $owner->contacts()->create(['name' => 'Private contact']);

        $this->actingAs($otherUser)
            ->delete(route('contacts.destroy', $contact))
            ->assertNotFound();

        $this->assertDatabaseHas('contacts', ['id' => $contact->id]);
    }

    public function test_contact_content_is_escaped_in_html(): void
    {
        $this->withoutVite();
        $user = User::factory()->create();
        $user->contacts()->create([
            'name' => '<script>alert(1)</script>',
            'notes' => '<img src=x onerror=alert(1)>',
        ]);

        $this->actingAs($user)
            ->get(route('contacts.index'))
            ->assertOk()
            ->assertSee('&lt;script&gt;', escape: false)
            ->assertDontSee('<script>alert(1)</script>', escape: false)
            ->assertDontSee('<img src=x onerror=alert(1)>', escape: false);
    }
}
