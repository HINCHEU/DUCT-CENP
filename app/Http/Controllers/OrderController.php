<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Site;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $query = Order::with(['site', 'items', 'user']);
        
        if (!$user->hasRole('super_admin')) {
            $query->where('created_by', $user->id);
        }

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

        $orders = $query->oldest()->paginate(15)->withQueryString();
            
        return view('engineer.orders.index', compact('orders'));
    }

    public function create()
    {
        $user = Auth::user();
        $sites = $user->sites;
        return view('engineer.orders.create', compact('sites'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'site_id' => 'required|exists:sites,id',
            'requested_delivery_date' => 'nullable|date',
            'priority' => 'required|in:normal,urgent',
            'notes' => 'nullable|string',
        ]);
        
        $user = Auth::user();
        if (!$user->sites()->where('site_id', $request->site_id)->exists()) {
            abort(403, 'You do not have access to this site.');
        }

        $site = Site::findOrFail($request->site_id);
        
        $projectCode = strtoupper($site->project_code ?: 'P000');
        $projectName = strtoupper(Str::slug($site->name));
        
        $count = Order::where('site_id', $site->id)->count();
        $sequence = str_pad($count + 1, 4, '0', STR_PAD_LEFT);
        
        $orderNumber = "{$projectCode}-{$projectName}-{$sequence}";

        $order = Order::create([
            'order_number' => $orderNumber,
            'site_id' => $request->site_id,
            'created_by' => $user->id,
            'status' => 'draft',
            'requested_delivery_date' => $request->requested_delivery_date,
            'priority' => $request->priority,
            'notes' => $request->notes,
        ]);

        return redirect()->route('engineer.orders.show', $order)->with('success', 'Order draft created successfully.');
    }

    public function show(Order $order)
    {
        $this->authorize('view', $order);
        $order->load(['site', 'items.ductType', 'comments.user']);
        $ductTypes = \App\Models\DuctType::all();
        
        return view('engineer.orders.show', compact('order', 'ductTypes'));
    }

    public function edit(Order $order)
    {
        $this->authorize('update', $order);
        $user = Auth::user();
        $sites = $user->sites;
        return view('engineer.orders.edit', compact('order', 'sites'));
    }

    public function update(Request $request, Order $order)
    {
        $this->authorize('update', $order);
        
        $request->validate([
            'requested_delivery_date' => 'nullable|date',
            'priority' => 'required|in:normal,urgent',
            'notes' => 'nullable|string',
        ]);

        $order->update($request->only(['requested_delivery_date', 'priority', 'notes']));

        return redirect()->route('engineer.orders.show', $order)->with('success', 'Order updated successfully.');
    }

    public function destroy(Order $order)
    {
        $this->authorize('delete', $order);
        $order->delete();
        return redirect()->route('engineer.orders.index')->with('success', 'Order deleted successfully.');
    }
    
    public function submit(Order $order)
    {
        $this->authorize('update', $order);
        
        if ($order->items()->count() === 0) {
            return back()->with('error', 'Cannot submit an empty order.');
        }
        
        $order->update(['status' => 'submitted']);
        
        return redirect()->route('engineer.orders.index')->with('success', 'Order submitted successfully.');
    }

    public function revertToDraft(Order $order)
    {
        $this->authorize('revert', $order);
        
        if ($order->status !== 'submitted') {
            return back()->with('error', 'Only submitted orders can be reverted to draft.');
        }
        
        $order->update(['status' => 'draft']);
        
        return back()->with('success', 'Order reverted to draft.');
    }
}
