<?php

namespace Tests\Feature\PurchaseOrder;

use App\Filament\Resources\PurchaseOrderResource;
use App\Models\Product;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class CreatePurchaseOrderTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @return void
     */
    public function test_can_access_create_purchase_order(): void
    {
        $this->login(['create_purchase::order', 'view_any_purchase::order']);

        $this->get(PurchaseOrderResource::getUrl('create'))
            ->assertSuccessful();
    }

    /**
     * @return void
     */
    public function test_cant_access_create_purchase_order_with_wrong_permission(): void
    {
        $this->login(['create_something']);

        $this->get(PurchaseOrderResource::getUrl('create'))
            ->assertForbidden();
    }

    /**
     * @return void
     */
    public function test_can_create_purchase_order(): void
    {
        $user = $this->login(['create_purchase::order', 'view_any_purchase::order']);

        $purchaseOrder = PurchaseOrder::factory()->make([
            'company_id' => $this->company->id,
            'user_id' => $user->id,
            'status' => 'pending',
        ])->toArray();
        $product = Product::factory()->create(['purchase_price' => 100]);

        $items = [
            [
                'product_id' => $product->id,
                'quantity' => 1,
            ],
        ];

        Livewire::test(PurchaseOrderResource\Pages\CreatePurchaseOrder::class)
            ->set('data.purchaseOrderItems', null)
            ->fillForm(array_merge($purchaseOrder, ['purchaseOrderItems' => $items, 'paid_amount' => 1]))
            ->call('create')
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas('purchase_orders', [
            'company_id' => $purchaseOrder['company_id'],
            'order_date' => (new Carbon($purchaseOrder['order_date']))->startOfDay()->toDateTimeString(),
            'total_amount' => $product->purchase_price,
            'paid_amount' => 1,
        ]);

        $purchaseOrder = PurchaseOrder::first();
        $this->assertDatabaseHas('purchase_order_items', [
            'purchase_order_id' => $purchaseOrder->id,
            'sku' => $product->sku,
            'product_id' => $product->id,
        ]);
    }

    /**
     * @return void
     */
    public function test_cant_create_purchase_order_with_wrong_permission(): void
    {
        $this->login(['create_something']);

        Livewire::test(PurchaseOrderResource\Pages\CreatePurchaseOrder::class)
            ->assertForbidden();
    }

    /**
     * @return void
     */
    public function test_can_create_goods_receipt_when_purchase_order_is_pending(): void
    {
        $user = $this->login(['create_purchase::order', 'view_any_purchase::order']);

        $purchaseOrder = PurchaseOrder::factory()->create([
            'company_id' => $this->company->id,
            'user_id' => $user->id,
            'status' => 'pending',
        ]);

        $product = Product::factory()->create(['purchase_price' => 100]);
        PurchaseOrderItem::factory()->create([
            'company_id' => $product->id,
            'purchase_order_id' => $purchaseOrder->id,
            'product_id' => $product->id,
            'quantity' => 1,
            'unit_cost' => 1,
            'sku' => $product->sku,
            'name' => $product->name,
        ]);

        Livewire::test(PurchaseOrderResource\RelationManagers\GoodsReceiptsRelationManager::class, [
            'ownerRecord' => $purchaseOrder,
            'pageClass' => PurchaseOrderResource\Pages\ViewPurchaseOrder::class,
        ])->callTableAction('create', null, [
            'received_date' => now(),
            'product_id' => $product->id,
            'quantity' => 1,
        ])
            ->assertHasNoTableActionErrors();

        $this->assertDatabaseHas('goods_receipts', [
            'purchase_order_id' => $purchaseOrder->id,
            'user_id' => $purchaseOrder->user_id,
            'product_id' => $product->id,
            'sku' => $product->sku,
            'name' => $product->name,
            'quantity' => 1,
            'unit_cost' => $product->purchase_price,
        ]);

        $this->assertDatabaseHas('purchase_orders', [
            'id' => $purchaseOrder->id,
            'status' => 'partially_received',
        ]);
    }
}
