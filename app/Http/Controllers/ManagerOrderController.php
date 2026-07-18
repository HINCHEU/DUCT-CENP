<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ManagerOrderController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        
        $query = Order::whereIn('status', ['draft', 'submitted', 'approved', 'fabricating', 'ready', 'delivered', 'rejected'])
            ->with(['site', 'user']);

        if (!$user->hasRole('super_admin')) {
            $assignedSiteIds = $user->sites()->pluck('site_id');
            $managedSiteIds = $user->managedSites()->pluck('id');
            $siteIds = $assignedSiteIds->merge($managedSiteIds)->unique();
            $query->whereIn('site_id', $siteIds);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('order_number', 'like', "%{$search}%")
                  ->orWhereHas('site', function($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%");
                  })
                  ->orWhereHas('user', function($q) use ($search) {
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

        $orders = $query->oldest()->paginate(15)->withQueryString();
            
        return view('manager.orders.index', compact('orders'));
    }
    
    public function show(Order $order)
    {
        $this->authorize('view', $order);
        $order->load(['site', 'user', 'items.ductType', 'comments.user']);
        $ductTypes = \App\Models\DuctType::all();
        
        return view('manager.orders.show', compact('order', 'ductTypes'));
    }
    
    public function approve(Order $order)
    {
        $this->authorize('update', $order);
        
        if ($order->status !== 'submitted') {
            return back()->with('error', 'Only submitted orders can be approved.');
        }
        
        $order->update([
            'status' => 'approved',
            'approved_by' => Auth::id(),
            'approved_at' => now(),
        ]);
        
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
    
    public function submit(Order $order)
    {
        $this->authorize('update', $order);
        
        if ($order->status !== 'draft') {
            return back()->with('error', 'Only draft orders can be submitted.');
        }
        
        if ($order->items()->count() === 0) {
            return back()->with('error', 'Cannot submit an empty order.');
        }
        
        $order->update(['status' => 'submitted']);
        
        return redirect()->route('manager.orders.index')->with('success', 'Order submitted successfully.');
    }
}
