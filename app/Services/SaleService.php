<?php

namespace App\Services;

use App\Models\Company;
use App\Models\Sale;
use Spatie\Browsershot\Browsershot;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class SaleService
{
    public function generateInvoice(Sale $sale): BinaryFileResponse
    {
        $customer = $sale->customer;
        $company = $sale->company;
        $items = $sale->saleItems;
        $subTotal = $items->sum(function ($item) {
            return $item->quantity * $item->unit_cost;
        });
        $currency = $company->getCurrency();

        $html = view('filament.invoice.invoice', [
            'companyName' => $company->name,
            'logo' => $company->getCompanyLogo(),
            'invoiceNumber' => $sale->invoice_number,
            'email' => $company->email,
            'address' => $company->address,
            'phoneNumber' => $company->phone,
            'buyerName' => $customer->name,
            'buyerEmail' => $customer->email,
            'buyerPhoneNumber' => $customer->phone,
            'buyerAddress' => $customer->address,
            'invoiceDate' => $sale->sale_date->format('M d, Y'),
            'dueDate' => $sale->sale_date->addDays($sale->pay_until)->format('M d, Y'),
            'items' => $items,
            'subTotal' => $this->format($subTotal, $currency),
            'shippingFee' => $this->format($sale->shipping_fee, $currency),
            'discount' => $sale->formatted_discount,
            'vat' => $sale->vat,
            'total' => $this->format($sale->total_amount, $currency),
            'paidAmount' => $this->format($sale->paid_amount, $currency),
            'remainingAmount' => $this->format($sale->remaining_amount, $currency),
            'superCompany' => Company::first(),
        ])->render();

        Browsershot::html($html)
            ->noSandbox()
            ->waitUntilNetworkIdle()
            ->format('A4')
            ->save($sale->invoice_number.'.pdf');

        return response()
            ->download($sale->invoice_number.'.pdf')
            ->deleteFileAfterSend();

        $invoice = Invoice::make()
            ->date($sale->sale_date)
            ->buyer($buyer)
            ->seller($seller)
            ->shipping($sale->shipping_fee * (1 + ($sale->vat / 100)))
            ->taxRate($sale->vat)
            ->totalDiscount($sale->discount, $sale->discount_type === DiscountTypeEnum::PERCENTAGE)
            ->series($sale->invoice_number)
            ->serialNumberFormat('{SERIES}')
            ->addItems($items)
            ->currencyCode($company->getCurrency())
            ->currencySymbol('')
            ->payUntilDays($sale->pay_until)
            ->logo($company->getCompanyLogo());

        return $invoice->stream();
    }

    /**
     * @param  int|float  $amount
     * @param  string  $currency
     * @return string
     */
    protected function format(int|float $amount, string $currency): string
    {
        return number_format($amount, 2).' '.$currency;
    }
}
