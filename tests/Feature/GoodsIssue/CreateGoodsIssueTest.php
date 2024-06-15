<?php

namespace Tests\Feature\GoodsIssue;

use App\Enums\GoodsIssueTypeEnum;
use App\Events\GoodsIssueCreated;
use App\Filament\Resources\GoodsIssueResource;
use App\Models\Customer;
use App\Models\GoodsIssue;
use App\Models\Inventory;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Livewire\Livewire;
use Tests\TestCase;

class CreateGoodsIssueTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @return void
     */
    public function test_can_access_create_goods_issue(): void
    {
        $this->login(['create_goods::issue', 'view_any_goods::issue']);

        $this->get(GoodsIssueResource::getUrl('create'))
            ->assertSuccessful();
    }

    /**
     * @return void
     */
    public function test_cant_access_create_goods_issue_with_wrong_permission(): void
    {
        $this->login(['create_something']);

        $this->get(GoodsIssueResource::getUrl('create'))
            ->assertForbidden();
    }

    /**
     * @return void
     */
    public function test_can_create_goods_issue(): void
    {
        $user = $this->login(['create_goods::issue', 'view_any_goods::issue']);

        $product = Product::factory()->create(['selling_price' => 100]);

        Inventory::factory()->create(['product_id' => $product->id]);
        $dateNow = now();

        Livewire::test(GoodsIssueResource\Pages\CreateGoodsIssue::class)
            ->fillForm([
                'issue_date' => $dateNow,
                'type' => 'sale',
                'product_id' => $product->id,
                'quantity' => 1,
                'notes' => 'test notes',
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas('goods_issues', [
            'issue_date' => $dateNow->toDateString(),
            'type' => 'sale',
            'product_id' => $product->id,
            'sku' => $product->sku,
            'name' => $product->name,
            'quantity' => 1,
            'notes' => 'test notes',
        ]);
    }

    /**
     * @return void
     */
    public function test_cant_create_goods_issue_with_wrong_permission(): void
    {
        $this->login(['create_something']);

        Livewire::test(GoodsIssueResource\Pages\CreateGoodsIssue::class)
            ->assertForbidden();
    }

    /**
     * @return void
     */
    public function test_can_fire_create_goods_issue_event(): void
    {
        Event::fake(GoodsIssueCreated::class);

        $user = $this->login(['create_goods::issue', 'view_any_goods::issue']);

        $product = Product::factory()->create(['selling_price' => 100]);
        Inventory::factory()->create(['product_id' => $product->id]);

        Livewire::test(GoodsIssueResource\Pages\CreateGoodsIssue::class)
            ->fillForm([
                'issue_date' => now(),
                'type' => 'sale',
                'product_id' => $product->id,
                'quantity' => 1,
                'notes' => 'test notes',
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        Event::assertDispatched(GoodsIssueCreated::class);
    }

    /**
     * @return void
     */
    public function test_can_create_stock_movement_when_goods_issues_is_created(): void
    {
        $user = $this->login(['create_goods::issue', 'view_any_goods::issue']);

        $customer = Customer::factory()->create();
        $product = Product::factory()->create(['selling_price' => 100]);
        $inventory = Inventory::factory()->create(['product_id' => $product->id]);

        Livewire::test(GoodsIssueResource\Pages\CreateGoodsIssue::class)
            ->fillForm([
                'issue_date' => now(),
                'type' => GoodsIssueTypeEnum::WRITE_OFF->value,
                'product_id' => $product->id,
                'quantity' => 1,
                'notes' => 'test notes',
                'customer_id' => $customer->id,
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $goodsIssue = GoodsIssue::first();

        $this->assertDatabaseHas('stock_movements', [
            'user_id' => $goodsIssue->user_id,
            'inventory_id' => $inventory->id,
            'customer_id' => $customer->id,
            'reference_number' => $goodsIssue->gin_code,
            'type' => GoodsIssueTypeEnum::WRITE_OFF->value,
            'quantity_before_adjustment' => $inventory->quantity_on_hand,
            'quantity' => -1,
        ]);
    }
}
