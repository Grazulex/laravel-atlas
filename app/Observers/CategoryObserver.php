<?php

namespace App\Observers;

use App\Events\CategoryCreated;
use App\Models\Category;

class CategoryObserver
{
    /**
     * Handle the Category "creating" event.
     */
    public function creating(Category $category): void
    {
        // Logic before creating a category
    }

    /**
     * Handle the Category "created" event.
     */
    public function created(Category $category): void
    {
        // Dispatch event after category is created
        event(new CategoryCreated($category));
    }

    /**
     * Handle the Category "updating" event.
     */
    public function updating(Category $category): void
    {
        // Logic before updating a category
    }

    /**
     * Handle the Category "updated" event.
     */
    public function updated(Category $category): void
    {
        // Logic after category is updated
    }

    /**
     * Handle the Category "deleted" event.
     */
    public function deleted(Category $category): void
    {
        // Logic after category is deleted
    }
}
