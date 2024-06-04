<?php

namespace App\Services;

use App\Models\Sale;
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

        $seller = new Party([
            'name' => $customer->name,
            'custom_fields' => [
                'email' => $customer->email,
                'phone' => $customer->phone,
                'address' => $customer->address,
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
            ->series($sale->invoice_number)
            ->serialNumberFormat('{SERIES}')
            ->addItems($items);

        return $invoice->stream();
    }
}
