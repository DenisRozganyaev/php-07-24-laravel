<?php

namespace App\Observers;

use App\Models\Category;

class CategoryObserver
{
    /**
     * Handle the Category "deleted" event.
     */
    public function deleted(Category $category): void
    {
        if ($category->children()->exists()) {
            $category->children()->update(['parent_id' => null]);
        }
    }
}
