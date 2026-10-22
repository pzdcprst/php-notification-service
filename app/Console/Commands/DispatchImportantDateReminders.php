<?php

namespace App\Console\Commands;

use App\Jobs\SendImportantDateReminderJob;
use App\Models\ImportantDate;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

#[Signature('important-dates:dispatch')]
#[Description('Queue notifications for important dates occurring today')]
class DispatchImportantDateReminders extends Command
{
    public function handle(): int
    {
        $dispatched = 0;

        ImportantDate::query()
            ->where(function (Builder $query): void {
                $query->whereNull('last_notified_year')
                    ->orWhere('last_notified_year', '<>', now()->year);
            })
            ->where(function (Builder $query): void {
                $query->where(function (Builder $yearlyQuery): void {
                    $yearlyQuery->where('repeat_yearly', true)
                        ->whereMonth('date', now()->month)
                        ->whereDay('date', now()->day);
                })->orWhere(function (Builder $onceQuery): void {
                    $onceQuery->where('repeat_yearly', false)
                        ->whereDate('date', today());
                });
            })
            ->orderBy('id')
            ->chunkById(100, function (Collection $importantDates) use (&$dispatched): void {
                foreach ($importantDates as $importantDate) {
                    SendImportantDateReminderJob::dispatch($importantDate->id)->afterCommit();
                    $dispatched++;
                }
            });

        $this->info("Dispatched {$dispatched} important date reminder(s).");

        return self::SUCCESS;
    }
}
