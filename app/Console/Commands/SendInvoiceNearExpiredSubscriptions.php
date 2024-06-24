<?php

namespace App\Console\Commands;

use App\Enums\PaymentStatusEnum;
use App\Jobs\SendInvoiceJob;
use App\Models\Subscription;
use Illuminate\Console\Command;

class SendInvoiceNearExpiredSubscriptions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:send-invoice-near-expired-subscriptions';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command checks for active subscriptions expiring within a week and generates invoices for them.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $expiringSubscriptions = Subscription::where('status', 'active')
            ->where('total_amount', '>', 0)
            ->whereDate('end_date', '=', now()->addWeek()->toDateString())
            ->get();

        foreach ($expiringSubscriptions as $subscription) {
            $payment = $subscription->createPayment(PaymentStatusEnum::PENDING);

            SendInvoiceJob::dispatch($payment)
                ->onQueue('short-running-queue');
        }
    }
}
