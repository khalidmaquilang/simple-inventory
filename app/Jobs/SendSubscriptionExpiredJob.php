<?php

namespace App\Jobs;

use App\Models\Company;
use App\Notifications\ExpiredNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendSubscriptionExpiredJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(protected Company $company)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $owner = $this->company->owner;

        $owner->notify(new ExpiredNotification(
            company: $this->company,
        ));
    }
}
