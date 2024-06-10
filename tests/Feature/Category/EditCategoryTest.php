<?php

namespace Tests\Feature\Category;

use App\Filament\Resources\CategoryResource;
use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class EditCategoryTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @return void
     */
    public function test_can_access_edit_category(): void
    {
        $this->login(['update_category', 'view_any_category']);

        $this->get(CategoryResource::getUrl('edit', [
            'record' => Category::factory()->create()
        ]))
            ->assertSuccessful();
    }

    /**
     * @return void
     */
    public function test_cant_access_edit_category_with_wrong_permission(): void
    {
        $this->login(['edit_something']);

        $this->get(CategoryResource::getUrl('edit'))
            ->assertForbidden();
    }

    /**
     * @return void
     */
    public function test_can_edit_category(): void
    {
        $this->login(['update_category', 'view_any_category']);

        Livewire::test(CategoryResource\Pages\CreateCategory::class)
            ->fillForm([
                'category_id' => null,
                'name' => 'name',
                'description' => 'description',
            ])
            ->call('edit')
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas('categories', [
            'category_id' => null,
            'name' => 'name',
            'description' => 'description',
        ]);
    }

    /**
     * @return void
     */
    public function test_cant_edit_category_with_wrong_permission(): void
    {
        $this->login(['edit_something']);

        Livewire::test(CategoryResource\Pages\CreateCategory::class)
            ->assertForbidden();
    }
}
