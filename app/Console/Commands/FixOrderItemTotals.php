<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\OrderItem;

class FixOrderItemTotals extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:fix-order-item-totals';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fix total area for existing order items where it is currently 0.00';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $items = OrderItem::all();
        $count = 0;
        
        foreach ($items as $item) {
            if ($item->surface_area > 0) {
                $item->update(['total_area' => $item->surface_area * $item->quantity]);
                $count++;
            }
        }
        
        $this->info("Successfully updated {$count} order items.");
    }
}
