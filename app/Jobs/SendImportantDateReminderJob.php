<?php

namespace App\Jobs;

use App\Models\ImportantDate;
use App\Notifications\ImportantDateNotification;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\DB;

class SendImportantDateReminderJob implements ShouldBeUnique, ShouldQueue
{
    use Queueable;

    public int $uniqueFor = 86400;

    public function __construct(public int $importantDateId) {}

    public function uniqueId(): string
    {
        return "important-date:{$this->importantDateId}:".now()->year;
    }

    public function handle(): void
    {
        DB::transaction(function (): void {
            $importantDate = ImportantDate::query()
                ->with(['user', 'contact'])
                ->whereKey($this->importantDateId)
                ->lockForUpdate()
                ->first();

            if ($importantDate === null || $importantDate->last_notified_year === now()->year) {
                return;
            }

            $matchesToday = $importantDate->repeat_yearly
                ? $importantDate->date->format('m-d') === now()->format('m-d')
                : $importantDate->date->isSameDay(today());

            if (! $matchesToday) {
                return;
            }

            $importantDate->user->notify(new ImportantDateNotification($importantDate));
            $importantDate->forceFill(['last_notified_year' => now()->year])->save();
        });
    }
}
