<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ManagerOrderController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        // Manager can view submitted and approved orders for their sites
        $siteIds = $user->sites()->pluck('site_id');
        
        $orders = Order::whereIn('site_id', $siteIds)
            ->whereIn('status', ['submitted', 'approved', 'fabricating', 'ready', 'delivered', 'rejected'])
            ->with(['site', 'user'])
            ->latest()
            ->paginate(15);
            
        return view('manager.orders.index', compact('orders'));
    }
    
    public function show(Order $order)
    {
        $this->authorize('view', $order);
        $order->load(['site', 'user', 'items.ductType']);
        $ductTypes = \App\Models\DuctType::all();
        
        return view('manager.orders.show', compact('order', 'ductTypes'));
    }
    
    public function approve(Order $order)
    {
        $this->authorize('update', $order);
        
        if ($order->status !== 'submitted') {
            return back()->with('error', 'Only submitted orders can be approved.');
        }
        
        $order->update(['status' => 'approved']);
        
        return redirect()->route('manager.orders.index')->with('success', 'Order approved successfully.');
    }
    
    public function reject(Request $request, Order $order)
    {
        $this->authorize('update', $order);
        
        if ($order->status !== 'submitted') {
            return back()->with('error', 'Only submitted orders can be rejected.');
        }
        
        $request->validate([
            'notes' => 'required|string|max:1000'
        ]);
        
        // Append rejection reason to notes
        $order->update([
            'status' => 'rejected',
            'notes' => $order->notes . "\n\nRejection Reason: " . $request->notes
        ]);
        
        return redirect()->route('manager.orders.index')->with('success', 'Order rejected.');
    }
}
