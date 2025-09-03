<?php

namespace App\Listeners;

use App\Events\SaleFinalized;
use App\Models\Inventory;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class UpdateInventory implements ShouldQueue {
    use InteractsWithQueue;

    /**
     * Handle the event.
     */
    public function handle(SaleFinalized $event): void
    {
        foreach ($event->sale->items as $item) {
            $inventory = Inventory::where('product_id', $item->product_id)->first();
            if ($inventory) {
                $inventory->quantity -= $item->quantity;
                $inventory->save();
            }
        }
    }
}
