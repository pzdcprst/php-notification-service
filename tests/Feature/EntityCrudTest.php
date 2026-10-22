<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class EntityCrudTest extends TestCase
{
    use RefreshDatabase;

    public function test_contact_can_be_created_updated_and_deleted(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->post(route('contacts.store'), [
            'name' => 'Ada Lovelace',
            'email' => 'ada@example.test',
            'phone' => '+7 900 111-22-33',
            'notes' => 'Первый контакт',
        ])->assertRedirect(route('contacts.index'));

        $contact = $user->contacts()->sole();
        $this->assertDatabaseHas('contacts', ['id' => $contact->id, 'name' => 'Ada Lovelace']);

        $this->put(route('contacts.update', $contact), [
            'name' => 'Ada Byron',
            'email' => 'ada.byron@example.test',
            'phone' => '+7 900 444-55-66',
            'notes' => 'Обновленная заметка',
        ])->assertRedirect(route('contacts.index'));

        $this->assertDatabaseHas('contacts', [
            'id' => $contact->id,
            'name' => 'Ada Byron',
            'email' => 'ada.byron@example.test',
        ]);

        $this->delete(route('contacts.destroy', $contact))
            ->assertRedirect(route('contacts.index'));

        $this->assertDatabaseMissing('contacts', ['id' => $contact->id]);
    }

    public function test_event_can_be_created_updated_and_deleted(): void
    {
        $user = User::factory()->create();
        $contact = $user->contacts()->create(['name' => 'Grace Hopper']);

        $this->actingAs($user)->post(route('events.store'), [
            'contact_id' => $contact->id,
            'title' => 'Planning meeting',
            'description' => 'Review the project plan',
            'starts_at' => '2026-10-23T10:00',
            'ends_at' => '2026-10-23T11:00',
            'status' => 'planned',
        ])->assertRedirect(route('events.index'));

        $event = $user->events()->sole();
        $this->assertDatabaseHas('events', [
            'id' => $event->id,
            'contact_id' => $contact->id,
            'title' => 'Planning meeting',
        ]);

        $this->put(route('events.update', $event), [
            'contact_id' => $contact->id,
            'title' => 'Project review',
            'description' => 'Updated description',
            'starts_at' => '2026-10-24T12:00',
            'status' => 'completed',
        ])->assertRedirect(route('events.index'));

        $this->assertDatabaseHas('events', [
            'id' => $event->id,
            'title' => 'Project review',
            'status' => 'completed',
        ]);

        $this->delete(route('events.destroy', $event))
            ->assertRedirect(route('events.index'));

        $this->assertDatabaseMissing('events', ['id' => $event->id]);
    }

    public function test_important_date_can_be_created_updated_and_deleted(): void
    {
        $user = User::factory()->create();
        $contact = $user->contacts()->create(['name' => 'Katherine Johnson']);

        $this->actingAs($user)->post(route('important-dates.store'), [
            'contact_id' => $contact->id,
            'title' => 'Birthday',
            'date' => '1990-10-23',
            'repeat_yearly' => '1',
        ])->assertRedirect(route('important-dates.index'));

        $importantDate = $user->importantDates()->sole();
        $this->assertDatabaseHas('important_dates', [
            'id' => $importantDate->id,
            'title' => 'Birthday',
            'repeat_yearly' => true,
        ]);

        $this->put(route('important-dates.update', $importantDate), [
            'contact_id' => $contact->id,
            'title' => 'Anniversary',
            'date' => '1990-11-02',
            'repeat_yearly' => '0',
        ])->assertRedirect(route('important-dates.index'));

        $this->assertDatabaseHas('important_dates', [
            'id' => $importantDate->id,
            'title' => 'Anniversary',
            'repeat_yearly' => false,
        ]);

        $this->delete(route('important-dates.destroy', $importantDate))
            ->assertRedirect(route('important-dates.index'));

        $this->assertDatabaseMissing('important_dates', ['id' => $importantDate->id]);
    }

    public function test_reminder_can_be_created_updated_and_deleted(): void
    {
        $user = User::factory()->create();
        $event = $user->events()->create([
            'title' => 'Design review',
            'starts_at' => Carbon::parse('2026-10-24 10:00'),
        ]);

        $this->actingAs($user)->post(route('reminders.store'), [
            'event_id' => $event->id,
            'remind_at' => Carbon::parse('2026-10-23 10:00')->format('Y-m-d\TH:i'),
            'channel' => 'telegram',
        ])->assertRedirect(route('reminders.index'));

        $reminder = $user->reminders()->sole();
        $this->assertDatabaseHas('reminders', [
            'id' => $reminder->id,
            'event_id' => $event->id,
            'channel' => 'telegram',
            'status' => 'pending',
        ]);

        $this->put(route('reminders.update', $reminder), [
            'event_id' => $event->id,
            'remind_at' => Carbon::parse('2026-10-23 11:00')->format('Y-m-d\TH:i'),
            'channel' => 'database',
        ])->assertRedirect(route('reminders.index'));

        $this->assertDatabaseHas('reminders', [
            'id' => $reminder->id,
            'channel' => 'database',
            'status' => 'pending',
        ]);

        $this->delete(route('reminders.destroy', $reminder))
            ->assertRedirect(route('reminders.index'));

        $this->assertDatabaseMissing('reminders', ['id' => $reminder->id]);
    }

    /** @return array<string, array{string, array<string, mixed>, string}> */
    public static function invalidCreatePayloads(): array
    {
        return [
            'contact requires a name' => ['contacts.store', [], 'name'],
            'event requires a title' => ['events.store', [], 'title'],
            'important date requires a contact' => ['important-dates.store', [], 'contact_id'],
            'reminder requires an event' => ['reminders.store', [], 'event_id'],
        ];
    }

    #[DataProvider('invalidCreatePayloads')]
    public function test_create_validation_rejects_missing_required_fields(string $routeName, array $payload, string $field): void
    {
        $this->actingAs(User::factory()->create())
            ->from(route('dashboard'))
            ->post(route($routeName), $payload)
            ->assertRedirect(route('dashboard'))
            ->assertSessionHasErrors($field);
    }

    public function test_contact_validation_rejects_invalid_email(): void
    {
        $this->actingAs(User::factory()->create())
            ->from(route('dashboard'))
            ->post(route('contacts.store'), [
                'name' => 'Test contact',
                'email' => 'not-an-email',
            ])
            ->assertRedirect(route('dashboard'))
            ->assertSessionHasErrors('email');
    }
}
