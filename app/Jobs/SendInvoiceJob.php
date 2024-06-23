<?php

namespace App\Jobs;

use App\Models\Payment;
use App\Notifications\InvoiceNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendInvoiceJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(protected Payment $payment)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $owner = $this->payment->company->owner;

        $owner->notify(new InvoiceNotification(
            owner: $owner,
            company: $this->payment->company,
            subscription: $this->payment->subscription,
            payment: $this->payment
        ));
    }
}
