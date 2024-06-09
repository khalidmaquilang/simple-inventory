<?php

namespace App\Services;

use App\Enums\DiscountTypeEnum;
use App\Models\Sale;
use App\Models\Setting;
use Filament\Facades\Filament;
use Illuminate\Http\Response;
use LaravelDaily\Invoices\Classes\Buyer;
use LaravelDaily\Invoices\Classes\InvoiceItem;
use LaravelDaily\Invoices\Classes\Party;
use LaravelDaily\Invoices\Invoice;

class SaleService
{
    public function generateInvoice(Sale $sale): Response
    {
        $customer = $sale->customer;

        $buyer = new Buyer([
            'name' => $customer->name,
            'custom_fields' => [
                'email' => $customer->email,
                'phone' => $customer->phone,
                'address' => $customer->address,
            ],
        ]);

        $company = Filament::getTenant();
        $seller = new Party([
            'name' => $company->name,
            'custom_fields' => [
                'email' => $company->email,
                'phone' => $company->phone,
                'address' => $company->address,
            ],
        ]);

        $items = [];
        $saleItems = $sale->saleItems;
        foreach ($saleItems as $saleItem) {
            $items[] = InvoiceItem::make($saleItem->name)
                ->description($saleItem->sku)
                ->quantity($saleItem->quantity)
                ->pricePerUnit($saleItem->unit_cost);
        }

        $invoice = Invoice::make()
            ->date($sale->sale_date)
            ->buyer($buyer)
            ->seller($seller)
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
}
