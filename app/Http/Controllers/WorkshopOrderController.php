<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;

class WorkshopOrderController extends Controller
{
    public function index()
    {
        // Workshop sees all approved and fabricating orders
        $orders = Order::whereIn('status', ['approved', 'fabricating', 'ready', 'delivered'])
            ->with(['site', 'user'])
            ->oldest('requested_delivery_date')
            ->paginate(15);
            
        return view('workshop.orders.index', compact('orders'));
    }
    
    public function show(Order $order)
    {
        $this->authorize('view', $order);
        $order->load(['site', 'user', 'items.ductType']);
        
        return view('workshop.orders.show', compact('order'));
    }
    
    public function updateStatus(Request $request, Order $order)
    {
        $this->authorize('view', $order);
        
        $request->validate([
            'status' => 'required|in:fabricating,ready,delivered'
        ]);
        
        // Ensure logical flow: approved -> fabricating -> ready -> delivered
        $validTransitions = [
            'approved' => ['fabricating'],
            'fabricating' => ['ready'],
            'ready' => ['delivered']
        ];
        
        if (!isset($validTransitions[$order->status]) || !in_array($request->status, $validTransitions[$order->status])) {
            return back()->with('error', 'Invalid status transition.');
        }
        
        $order->update(['status' => $request->status]);
        
        return back()->with('success', 'Order status updated to ' . ucfirst($request->status));
    }
}
