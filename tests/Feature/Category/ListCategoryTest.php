<?php

namespace Tests\Feature\Category;

use App\Filament\Resources\CategoryResource;
use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class ListCategoryTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @return void
     */
    public function test_can_access_list_category(): void
    {
        $this->login(['view_any_category']);

        $this->get(CategoryResource::getUrl())
            ->assertSuccessful();
    }

    /**
     * @return void
     */
    public function test_cant_access_list_category_with_wrong_permission(): void
    {
        $this->login(['list_something']);

        $this->get(CategoryResource::getUrl())
            ->assertForbidden();
    }

    /**
     * @return void
     */
    public function test_can_see_lists_of_categories(): void
    {
        $this->login(['view_any_category']);
        $categories = Category::factory(10)->create();

        Livewire::test(CategoryResource\Pages\ListCategories::class)
            ->assertCanSeeTableRecords($categories);
    }
}
