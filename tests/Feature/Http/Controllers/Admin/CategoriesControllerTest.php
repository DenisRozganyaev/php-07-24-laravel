<?php

namespace Tests\Feature\Http\Controllers\Admin;

use App\Enums\RolesEnum;
use App\Models\Category;
use Illuminate\Support\Str;
use Tests\Feature\Traits\SetupTrait;
use Tests\TestCase;

class CategoriesControllerTest extends TestCase
{
    use SetupTrait;

    /*
     *  Action: index
     */
    public function test_allow_to_see_categories_for_admin_role(): void
    {
        $categories = Category::factory(3)->create();

        $response = $this->actingAs($this->user())
            ->get(route('admin.categories.index'));

        $response->assertSuccessful();
        $response->assertViewIs('admin.categories.index');
        $response->assertSeeInOrder($categories->pluck('name')->toArray());
    }

    public function test_allow_to_see_categories_for_moderator_role(): void
    {
        $categories = Category::factory(3)->create();

        $response = $this->actingAs($this->user(RolesEnum::MODERATOR))
            ->get(route('admin.categories.index'));

        $response->assertSuccessful();
        $response->assertViewIs('admin.categories.index');
        $response->assertSeeInOrder($categories->pluck('name')->toArray());
    }

    public function test_allow_to_see_categories_for_customer_role(): void
    {
        $response = $this->actingAs($this->user(RolesEnum::CUSTOMER))
            ->get(route('admin.categories.index'));

        $response->assertForbidden();
    }

    public function test_it_creates_category_with_valid_data(): void
    {
        $category = Category::factory()->makeOne()->toArray();

        $this->assertDatabaseEmpty('categories');

        $response = $this->actingAs($this->user())
            ->post(route('admin.categories.store'), $category);

        $response->assertStatus(302);
        $response->assertRedirectToRoute('admin.categories.index');

        $this->assertDatabaseHas('categories', $category);

        $response->assertSessionHas('toasts');
        $response->assertSessionHas(
            'toasts',
            fn ($collection) => $collection->first()['message'] === "Category [$category[name]] was created"
        );
    }

    public function test_it_creates_category_with_parent(): void
    {
        $parent = Category::factory()->createOne();
        $category = Category::factory()->makeOne([
            'parent_id' => $parent->id,
        ])->toArray();

        $this->assertDatabaseMissing('categories', [
            'name' => $category['name'],
            'parent_id' => $category['parent_id'],
        ]);

        $this->actingAs($this->user())
            ->post(route('admin.categories.store'), $category);

        $this->assertDatabaseHas(Category::class, [
            'name' => $category['name'],
            'parent_id' => $category['parent_id'],
        ]);
    }

    public function test_it_fails_when_category_data_is_wrong(): void
    {
        $data = ['name' => 'a'];

        $this->assertDatabaseMissing('categories', [
            'name' => $data['name'],
        ]);

        $response = $this->actingAs($this->user())
            ->post(route('admin.categories.store'), $data);

        $response->assertStatus(302);
        $response->assertRedirectToRoute('admin.categories.create');
        $response->assertSessionHasErrors(['name']);
        $this->assertDatabaseMissing('categories', [
            'name' => $data['name'],
        ]);
    }

    public function test_it_fails_when_parent_id_is_wrong(): void
    {
        $data = Category::factory()->makeOne([
            'parent_id' => 989898989,
        ])->toArray();

        $this->assertDatabaseMissing('categories', [
            'name' => $data['name'],
            'parent_id' => $data['parent_id'],
        ]);

        $response = $this->actingAs($this->user())
            ->post(route('admin.categories.store'), $data);

        $response->assertStatus(302);
        $response->assertRedirectToRoute('admin.categories.create');
        $response->assertSessionHasErrors(['parent_id']);
        $this->assertDatabaseMissing('categories', [
            'name' => $data['name'],
            'parent_id' => $data['parent_id'],
        ]);
    }

    public function test_it_updates_with_valid_data(): void
    {
        $newName = 'updated';

        $category = Category::factory()->createOne();
        $newData = ['name' => $newName];

        $this->assertDatabaseHas('categories', $category->toArray());
        $this->assertDatabaseMissing('categories', $newData);

        $response = $this->actingAs($this->user())
            ->put(route('admin.categories.update', $category), $newData);

        $this->assertDatabaseHas('categories', [
            'name' => $newName,
            'slug' => Str::slug($newName),
        ]);
        $this->assertDatabaseMissing('categories', [
            'name' => $category->name,
            'slug' => $category->slug,
        ]);
    }

    public function test_it_removes_category_for_admin_role()
    {
        $category = Category::factory()->create();

        $this->assertDatabaseHas(Category::class, [
            'id' => $category->id,
        ]);

        $this->actingAs($this->user())
            ->delete(route('admin.categories.destroy', $category));

        $this->assertDatabaseMissing(Category::class, [
            'id' => $category->id,
        ]);
    }

    public function test_it_removes_category_and_set_null_to_child()
    {
        $category = Category::factory()->createOne();
        $child = Category::factory()->createOne(['parent_id' => $category->id]);

        $this->assertDatabaseHas(Category::class, [
            'id' => $category->id,
        ]);
        $this->assertEquals($category->id, $child->parent_id);

        $this->actingAs($this->user())
            ->delete(route('admin.categories.destroy', $category));

        $this->assertDatabaseMissing(Category::class, [
            'id' => $category->id,
        ]);

        $child->refresh();

        $this->assertNull($child->parent_id);
    }
}
