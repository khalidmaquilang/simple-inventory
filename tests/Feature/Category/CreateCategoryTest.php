<?php

namespace Tests\Feature\Category;

use App\Filament\Resources\CategoryResource;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class CreateCategoryTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @return void
     */
    public function test_can_access_create_category(): void
    {
        $this->login(['create_category', 'view_any_category']);

        $this->get(CategoryResource::getUrl('create'))
            ->assertSuccessful();
    }

    /**
     * @return void
     */
    public function test_cant_access_create_category_with_wrong_permission(): void
    {
        $this->login(['create_something']);

        $this->get(CategoryResource::getUrl('create'))
            ->assertForbidden();
    }

    /**
     * @return void
     */
    public function test_can_create_category(): void
    {
        $this->login(['create_category', 'view_any_category']);

        Livewire::test(CategoryResource\Pages\CreateCategory::class)
            ->fillForm([
                'category_id' => null,
                'name' => 'name',
                'description' => 'description',
            ])
            ->call('create')
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
    public function test_cant_create_category_with_wrong_permission(): void
    {
        $this->login(['create_something']);

        Livewire::test(CategoryResource\Pages\CreateCategory::class)
            ->assertForbidden();
    }
}
