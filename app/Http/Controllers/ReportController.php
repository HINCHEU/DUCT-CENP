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
        
        $linearKeys = ['angle_bar', 'angle_bar_u'];

        $ducts = $order->items->filter(function($item) use ($linearKeys) {
            return !in_array($item->ductType->formula_key, $linearKeys);
        });
        
        $supports = $order->items->filter(function($item) use ($linearKeys) {
            return in_array($item->ductType->formula_key, $linearKeys);
        });
        
        $pdf = Pdf::loadView('reports.cutlist', compact('order', 'ducts', 'supports'));
        
        // Use a landscape orientation if needed, or A4 default
        $pdf->setPaper('a4', 'portrait');
        
        return $pdf->download("cutlist_{$order->order_number}.pdf");
    }
}
