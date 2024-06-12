<?php

namespace App\Services;

use App\Models\Company;
use App\Models\Payment;
use Illuminate\Http\Response;
use LaravelDaily\Invoices\Classes\Buyer;
use LaravelDaily\Invoices\Classes\InvoiceItem;
use LaravelDaily\Invoices\Classes\Party;
use LaravelDaily\Invoices\Invoice;

class PaymentService
{
    public function generateInvoice(Payment $payment): Response
    {
        $customerCompany = $payment->subscription->company;

        $buyer = new Buyer([
            'name' => $customerCompany->name,
            'custom_fields' => [
                'email' => $customerCompany->email,
                'phone' => $customerCompany->phone,
                'address' => $customerCompany->address,
            ],
        ]);

        $company = Company::find(1);
        $seller = new Party([
            'name' => $company->name,
            'custom_fields' => [
                'email' => $company->email,
                'phone' => $company->phone,
                'address' => $company->address,
            ],
        ]);

        $vat = 12; // philippines 12%
        $plan = $payment->subscription->plan;

        $items = [];
        $items[] = InvoiceItem::make($plan->name)
            ->quantity(1)
            ->pricePerUnit($this->priceBeforeVat($plan->price, $vat));

        $extra_users = $payment->subscription->extra_users;
        if ($extra_users) {
            //100php per user
            $items[] = InvoiceItem::make('Additional User')
                ->quantity($payment->subscription->extra_users)
                ->pricePerUnit($this->priceBeforeVat(100, $vat));
        }

        $invoice = Invoice::make()
            ->date($payment->payment_date)
            ->buyer($buyer)
            ->seller($seller)
            ->taxRate($vat)
            ->series($payment->invoice_number)
            ->serialNumberFormat('{SERIES}')
            ->addItems($items)
            ->currencyCode('PHP')
            ->currencySymbol('₱')
            ->currencyFormat('{SYMBOL}{VALUE}')
            ->payUntilDays(0)
            ->logo($company->getCompanyLogo());

        return $invoice->stream();
    }

    /**
     * @param  float  $price
     * @param  int  $vat
     * @return float
     */
    protected function priceBeforeVat(float $price, int $vat): float
    {
        $taxRate = $vat / 100;

        return $price / (1 + $taxRate);
    }
}