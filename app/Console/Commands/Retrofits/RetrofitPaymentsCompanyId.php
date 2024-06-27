<?php

namespace App\Console\Commands\Retrofits;

use App\Models\Payment;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class RetrofitPaymentsCompanyId extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'retrofit:fill-payments-company-id';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command will fill all payments that does not have any company id.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->line('Searching for payments that does not have company_id');
        $payments = Payment::with('subscription')->where('company_id', null)->get();

        $this->line($payments->count().' payment/s found.');

        $this->line('Retrofitting...');
        $bar = $this->output->createProgressBar($payments->count());

        $errorCount = 0;
        $bar->start();

        foreach ($payments as $payment) {
            try {
                $this->addCompanyId($payment);

            } catch (\Exception $exception) {
                Log::error('There was something wrong while retrofitting payments.', [
                    'payment_id' => $payment->id,
                    'exception' => $exception,
                ]);
                $errorCount++;
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine();

        if ($errorCount) {
            $this->error($errorCount.' errors found. Please check logs.');
        }

        $this->line('Retrofit Finished!');
    }

    /**
     * @param  Payment  $payment
     * @return void
     */
    protected function addCompanyId(Payment $payment)
    {
        $subscription = $payment->subscription;

        $payment->company_id = $subscription->company_id;
        $payment->save();
    }
}
