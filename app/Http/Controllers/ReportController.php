<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function downloadCutList(Order $order)
    {
        $this->authorize('view', $order);
        
        $order->load(['site', 'user', 'items.ductType']);
        
        $ducts = $order->items->filter(function($item) {
            return $item->ductType->unit !== 'm';
        });
        
        $supports = $order->items->filter(function($item) {
            return $item->ductType->unit === 'm';
        });
        
        $pdf = Pdf::loadView('reports.cutlist', compact('order', 'ducts', 'supports'));
        
        // Use a landscape orientation if needed, or A4 default
        $pdf->setPaper('a4', 'portrait');
        
        return $pdf->download("cutlist_{$order->order_number}.pdf");
    }
}
