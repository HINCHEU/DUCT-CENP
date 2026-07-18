<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;

class WorkshopOrderController extends Controller
{
    public function index(Request $request)
    {
        // Workshop sees all approved and fabricating orders
        $query = Order::whereIn('status', ['approved', 'fabricating', 'ready', 'delivered'])
            ->with(['site', 'user']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('order_number', 'like', "%{$search}%")
                  ->orWhereHas('site', function($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }

        $orders = $query->oldest('requested_delivery_date')->paginate(15)->withQueryString();
            
        return view('workshop.orders.index', compact('orders'));
    }
    
    public function show(Order $order)
    {
        $this->authorize('view', $order);
        $order->load(['site', 'user', 'items.ductType', 'comments.user']);
        
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
        
        $updateData = ['status' => $request->status];

        // Record who confirmed (started fabrication) — this is the workshop acceptance step
        if ($request->status === 'fabricating') {
            $updateData['confirmed_by'] = auth()->id();
            $updateData['confirmed_at'] = now();
        }

        $order->update($updateData);
        
        return back()->with('success', 'Order status updated to ' . ucfirst($request->status));
    }
}
