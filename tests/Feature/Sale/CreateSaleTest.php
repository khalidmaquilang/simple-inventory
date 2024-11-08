<?php

namespace Tests\Feature\Sale;

use App\Enums\GoodsIssueTypeEnum;
use App\Events\SaleCreated;
use App\Filament\Resources\SaleResource;
use App\Models\Inventory;
use App\Models\Product;
use App\Models\Sale;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Livewire\Livewire;
use Tests\TestCase;

class CreateSaleTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @return void
     */
    public function test_can_access_create_sale(): void
    {
        $this->login(['create_sale', 'view_any_sale']);

        $this->get(SaleResource::getUrl('create'))
            ->assertSuccessful();
    }

    /**
     * @return void
     */
    public function test_cant_access_create_sale_with_wrong_permission(): void
    {
        $this->login(['create_something']);

        $this->get(SaleResource::getUrl('create'))
            ->assertForbidden();
    }

    /**
     * @return void
     */
    public function test_can_create_sale(): void
    {
        $user = $this->login(['create_sale', 'view_any_sale']);

        $sale = Sale::factory()->make([
            'user_id' => $user->id,
            'discount' => 0,
            'vat' => 12,
        ])->toArray();
        $product = Product::factory()->create(['selling_price' => 100]);
        Inventory::factory()->create(['product_id' => $product->id]);

        $items = [
            [
                'product_id' => $product->id,
                'quantity' => 1,
            ],
        ];

        Livewire::test(SaleResource\Pages\CreateSale::class)
            ->set('data.saleItems', null)
            ->fillForm(array_merge($sale, ['saleItems' => $items, 'paid_amount' => 1]))
            ->call('create')
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas('sales', [
            'company_id' => $sale['company_id'],
            'sale_date' => (new Carbon($sale['sale_date']))->startOfDay()->toDateTimeString(),
        ]);

        $sale = Sale::first();
        $this->assertDatabaseHas('sale_items', [
            'sale_id' => $sale->id,
            'product_id' => $product->id,
        ]);
    }

    /**
     * @return void
     */
    public function test_cant_create_sale_with_wrong_permission(): void
    {
        $this->login(['create_something']);

        Livewire::test(SaleResource\Pages\CreateSale::class)
            ->assertForbidden();
    }

    /**
     * @return void
     */
    public function test_can_fire_create_sale_event(): void
    {
        Event::fake(SaleCreated::class);

        $user = $this->login(['create_sale', 'view_any_sale']);

        $sale = Sale::factory()->make([
            'user_id' => $user->id,
            'discount' => 0,
            'vat' => 12,
        ])->toArray();
        $product = Product::factory()->create(['selling_price' => 100]);
        Inventory::factory()->create(['product_id' => $product->id]);

        $items = [
            [
                'product_id' => $product->id,
                'quantity' => 1,
            ],
        ];

        Livewire::test(SaleResource\Pages\CreateSale::class)
            ->set('data.saleItems', null)
            ->fillForm(array_merge($sale, ['saleItems' => $items, 'paid_amount' => 1]))
            ->call('create')
            ->assertHasNoFormErrors();

        Event::assertDispatched(SaleCreated::class);
    }

    /**
     * @return void
     */
    public function test_can_create_goods_issue_when_sales_is_created(): void
    {
        $user = $this->login(['create_sale', 'view_any_sale']);

        $sale = Sale::factory()->make([
            'user_id' => $user->id,
            'discount' => 0,
            'vat' => 12,
        ])->toArray();
        $product = Product::factory()->create(['selling_price' => 100]);
        Inventory::factory()->create(['product_id' => $product->id]);

        $items = [
            [
                'product_id' => $product->id,
                'quantity' => 1,
            ],
        ];

        Livewire::test(SaleResource\Pages\CreateSale::class)
            ->set('data.saleItems', null)
            ->fillForm(array_merge($sale, ['saleItems' => $items, 'paid_amount' => 1]))
            ->call('create')
            ->assertHasNoFormErrors();

        $sale = Sale::first();
        $this->assertDatabaseHas('goods_issues', [
            'sale_id' => $sale->id,
            'customer_id' => $sale->customer_id,
            'user_id' => $sale->user_id,
            'issue_date' => (new Carbon($sale->sale_date))->startOfDay()->toDateTimeString(),
            'product_id' => $product->id,
            'type' => GoodsIssueTypeEnum::SALE->value,
            'sku' => $product->sku,
            'name' => $product->name,
            'quantity' => 1,
            'notes' => 'Generated by the System.',
        ]);
    }

    /**
     * @return void
     */
    public function test_stock_movement_reference_number_is_invoice_when_sales_is_created(): void
    {
        $user = $this->login(['create_sale', 'view_any_sale']);

        $sale = Sale::factory()->make([
            'user_id' => $user->id,
            'discount' => 0,
            'vat' => 12,
        ])->toArray();
        $product = Product::factory()->create(['selling_price' => 100]);
        $inventory = Inventory::factory()->create(['product_id' => $product->id]);

        $items = [
            [
                'product_id' => $product->id,
                'quantity' => 1,
            ],
        ];

        Livewire::test(SaleResource\Pages\CreateSale::class)
            ->set('data.saleItems', null)
            ->fillForm(array_merge($sale, ['saleItems' => $items, 'paid_amount' => 1]))
            ->call('create')
            ->assertHasNoFormErrors();

        $sale = Sale::first();
        $this->assertDatabaseHas('stock_movements', [
            'user_id' => $sale->user_id,
            'inventory_id' => $inventory->id,
            'customer_id' => $sale->customer_id,
            'reference_number' => $sale->invoice_number,
            'type' => GoodsIssueTypeEnum::SALE->value,
            'quantity_before_adjustment' => $inventory->quantity_on_hand,
            'quantity' => -1,
        ]);
    }
}
