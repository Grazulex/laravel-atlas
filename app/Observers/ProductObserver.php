<?php

namespace App\Observers;

use App\Models\Product;
use App\Events\ProductCreated;
use App\Events\ProductUpdated;

class ProductObserver
{
    /**
     * Handle the Product "creating" event.
     */
    public function creating(Product $product): void
    {
        // Logic before creating a product
    }

    /**
     * Handle the Product "created" event.
     */
    public function created(Product $product): void
    {
        // Dispatch event after product is created
        event(new ProductCreated($product));
    }

    /**
     * Handle the Product "updating" event.
     */
    public function updating(Product $product): void
    {
        // Logic before updating a product
    }

    /**
     * Handle the Product "updated" event.
     */
    public function updated(Product $product): void
    {
        // Dispatch event after product is updated
        event(new ProductUpdated($product));
    }

    /**
     * Handle the Product "deleted" event.
     */
    public function deleted(Product $product): void
    {
        // Logic after product is deleted
    }

    /**
     * Handle the Product "restored" event.
     */
    public function restored(Product $product): void
    {
        // Logic after product is restored
    }
}
