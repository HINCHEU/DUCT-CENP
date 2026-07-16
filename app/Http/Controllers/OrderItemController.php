<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\DuctType;
use App\Services\DuctAreaCalculator;
use Illuminate\Http\Request;

class OrderItemController extends Controller
{
    public function store(Request $request, Order $order, DuctAreaCalculator $calculator)
    {
        $this->authorize('update', $order);
        
        $request->validate([
            'duct_type_id' => 'required|exists:duct_types,id',
            'dimensions' => 'required|array',
            'quantity' => 'required|integer|min:1',
            'thickness' => 'required|string',
            'canvas_flange' => 'boolean',
            'inner_strut' => 'boolean',
            'remarks' => 'nullable|string',
        ]);
        
        $ductType = DuctType::find($request->duct_type_id);
        
        // Calculate the surface area based on dimensions and formula key
        $surfaceArea = $calculator->calculate($ductType->formula_key, $request->dimensions);
        $totalArea = $surfaceArea * $request->quantity;

        $order->items()->create([
            'duct_type_id' => $ductType->id,
            'dimensions' => $request->dimensions,
            'quantity' => $request->quantity,
            'surface_area' => $surfaceArea,
            'total_area' => $totalArea,
            'thickness' => $request->thickness,
            'canvas_flange' => $request->boolean('canvas_flange'),
            'inner_strut' => $request->boolean('inner_strut'),
            'remarks' => $request->remarks,
        ]);

        return redirect()->back()->with('success', 'Item added to order.');
    }

    public function edit(Order $order, OrderItem $item)
    {
        $this->authorize('update', $order);
        // We'll handle edits via a modal, but if we need a dedicated view:
        return view('engineer.items.edit', compact('order', 'item'));
    }

    public function update(Request $request, Order $order, OrderItem $item, DuctAreaCalculator $calculator)
    {
        $this->authorize('update', $order);
        
        $request->validate([
            'dimensions' => 'required|array',
            'quantity' => 'required|integer|min:1',
            'thickness' => 'required|string',
            'canvas_flange' => 'boolean',
            'inner_strut' => 'boolean',
            'remarks' => 'nullable|string',
        ]);
        
        $ductType = $item->ductType;
        $surfaceArea = $calculator->calculate($ductType->formula_key, $request->dimensions);
        $totalArea = $surfaceArea * $request->quantity;

        $item->update([
            'dimensions' => $request->dimensions,
            'quantity' => $request->quantity,
            'surface_area' => $surfaceArea,
            'total_area' => $totalArea,
            'thickness' => $request->thickness,
            'canvas_flange' => $request->boolean('canvas_flange'),
            'inner_strut' => $request->boolean('inner_strut'),
            'remarks' => $request->remarks,
        ]);

        return redirect()->back()->with('success', 'Item updated.');
    }

    public function destroy(Order $order, OrderItem $item)
    {
        $this->authorize('update', $order);
        $item->delete();
        return redirect()->back()->with('success', 'Item removed.');
    }
    
    // Manager specific update method (if needed separately)
    public function updateManager(Request $request, Order $order, OrderItem $item, DuctAreaCalculator $calculator)
    {
        // Managers can also update using authorize('update', $order)
        return $this->update($request, $order, $item, $calculator);
    }
    
    public function updateQuantity(Request $request, Order $order, OrderItem $item)
    {
        $this->authorize('update', $order);
        
        $request->validate([
            'quantity' => 'required|integer|min:1',
        ]);
        
        $totalArea = $item->surface_area * $request->quantity;
        
        $item->update([
            'quantity' => $request->quantity,
            'total_area' => $totalArea,
        ]);
        
        return redirect()->back()->with('success', 'Quantity updated.');
    }
}
