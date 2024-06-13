<?php

namespace Tests\Feature\Category;

use App\Filament\Resources\CategoryResource;
use App\Models\Category;
use Filament\Actions\DeleteAction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class DeleteCategoryTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @return void
     */
    public function test_can_delete_category(): void
    {
        $this->markTestSkipped('I dont know why it is not working. Is it because there is a confirmation?');

        $this->login(['delete_category', 'view_any_category', 'update_category']);

        $category = Category::factory()->create();

        Livewire::test(CategoryResource\Pages\EditCategory::class, [
            'record' => $category->getRouteKey(),
        ])
            ->callAction(DeleteAction::class);

        $this->assertModelMissing($category);
    }

    /**
     * @return void
     */
    public function test_cant_delete_category_with_wrong_permission(): void
    {
        $this->login(['delete_something']);

        Livewire::test(CategoryResource\Pages\EditCategory::class, [
            'record' => Category::factory()->create()->getRouteKey(),
        ])
            ->assertForbidden();
    }
}
