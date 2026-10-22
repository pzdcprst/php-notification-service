<?php

namespace Tests\Feature;

use App\Jobs\SendImportantDateReminderJob;
use App\Jobs\SendReminderJob;
use App\Jobs\SendTelegramMessageJob;
use App\Models\Reminder;
use App\Models\User;
use App\Services\TelegramBot;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class TelegramIntegrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_webhook_rejects_requests_without_telegram_secret(): void
    {
        config(['services.telegram.webhook_secret' => str_repeat('x', 40)]);

        $this->postJson(route('telegram.webhook'), [])->assertForbidden();
    }

    public function test_start_token_links_one_private_chat_and_dispatches_confirmation(): void
    {
        $secret = str_repeat('s', 40);
        $plainToken = str_repeat('a', 48);
        $user = User::factory()->create();
        $user->forceFill([
            'telegram_link_token' => hash('sha256', $plainToken),
            'telegram_link_expires_at' => now()->addMinutes(10),
        ])->save();
        config(['services.telegram.webhook_secret' => $secret]);
        Queue::fake([SendTelegramMessageJob::class]);

        $this->postJson(route('telegram.webhook'), [
            'message' => [
                'text' => '/start '.$plainToken,
                'chat' => ['id' => 987654, 'type' => 'private'],
            ],
        ], ['X-Telegram-Bot-Api-Secret-Token' => $secret])->assertStatus(204);

        $this->assertSame('987654', $user->fresh()->telegram_chat_id);
        $this->assertNull($user->fresh()->telegram_link_token);
        Queue::assertPushed(SendTelegramMessageJob::class, fn (SendTelegramMessageJob $job): bool => $job->chatId === '987654');
    }

    public function test_expired_start_token_does_not_link_chat(): void
    {
        $secret = str_repeat('s', 40);
        $plainToken = str_repeat('b', 48);
        $user = User::factory()->create();
        $user->forceFill([
            'telegram_link_token' => hash('sha256', $plainToken),
            'telegram_link_expires_at' => now()->subSecond(),
        ])->save();
        config(['services.telegram.webhook_secret' => $secret]);
        Queue::fake([SendTelegramMessageJob::class]);

        $this->postJson(route('telegram.webhook'), [
            'message' => ['text' => '/start '.$plainToken, 'chat' => ['id' => 123, 'type' => 'private']],
        ], ['X-Telegram-Bot-Api-Secret-Token' => $secret])->assertStatus(204);

        $this->assertNull($user->fresh()->telegram_chat_id);
        Queue::assertNotPushed(SendTelegramMessageJob::class);
    }

    public function test_due_reminder_is_queued_but_future_reminder_is_not(): void
    {
        $this->freezeTime();
        $user = User::factory()->create();
        $event = $user->events()->create([
            'title' => 'Planning session',
            'starts_at' => now()->addHour(),
        ]);
        $dueReminder = $user->reminders()->create([
            'event_id' => $event->id,
            'remind_at' => now()->subMinute(),
            'channel' => Reminder::CHANNEL_DATABASE,
        ]);
        $futureReminder = $user->reminders()->create([
            'event_id' => $event->id,
            'remind_at' => now()->addHour(),
            'channel' => Reminder::CHANNEL_DATABASE,
        ]);
        Queue::fake([SendReminderJob::class]);

        $this->artisan('reminders:dispatch')->assertSuccessful();

        Queue::assertPushed(SendReminderJob::class, fn (SendReminderJob $job): bool => $job->reminderId === $dueReminder->id);
        Queue::assertNotPushed(SendReminderJob::class, fn (SendReminderJob $job): bool => $job->reminderId === $futureReminder->id);
    }

    public function test_telegram_reminder_job_sends_message_and_marks_reminder_sent(): void
    {
        config(['services.telegram.bot_token' => 'test-token']);
        Http::preventStrayRequests();
        Http::fake(['https://api.telegram.org/*' => Http::response(['ok' => true], 200)]);

        $user = User::factory()->create();
        $user->forceFill(['telegram_chat_id' => '987654'])->save();
        $event = $user->events()->create([
            'title' => 'Dentist',
            'starts_at' => now()->addDay(),
        ]);
        $reminder = $user->reminders()->create([
            'event_id' => $event->id,
            'remind_at' => now()->subMinute(),
            'channel' => Reminder::CHANNEL_TELEGRAM,
        ]);

        (new SendReminderJob($reminder->id))->handle(app(TelegramBot::class));

        $this->assertDatabaseHas('reminders', [
            'id' => $reminder->id,
            'status' => Reminder::STATUS_SENT,
        ]);
        Http::assertSent(fn (Request $request): bool => str_ends_with($request->url(), '/sendMessage')
            && $request['chat_id'] === '987654'
            && str_contains($request['text'], 'Dentist'));
    }

    public function test_yearly_important_date_notifies_once_for_the_year(): void
    {
        $this->travelTo(Carbon::parse('2026-05-06 08:00:00'));
        $user = User::factory()->create();
        $contact = $user->contacts()->create(['name' => 'Grace Hopper']);
        $importantDate = $user->importantDates()->create([
            'contact_id' => $contact->id,
            'title' => 'Birthday',
            'date' => '2022-05-06',
            'repeat_yearly' => true,
        ]);
        Queue::fake([SendImportantDateReminderJob::class]);

        $this->artisan('important-dates:dispatch')->assertSuccessful();
        Queue::assertPushed(SendImportantDateReminderJob::class, fn (SendImportantDateReminderJob $job): bool => $job->importantDateId === $importantDate->id);

        (new SendImportantDateReminderJob($importantDate->id))->handle();
        (new SendImportantDateReminderJob($importantDate->id))->handle();

        $this->assertDatabaseHas('important_dates', [
            'id' => $importantDate->id,
            'last_notified_year' => 2026,
        ]);
        $this->assertDatabaseCount('notifications', 1);
    }
}
