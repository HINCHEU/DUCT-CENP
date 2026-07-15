@extends('layouts.app')

@section('content')
<div class="container">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom: 20px;">
        <h2 style="font-family:'Barlow Condensed', sans-serif; font-size:24px; color:#1B3F8B;">My Orders</h2>
        <a href="{{ route('engineer.orders.create') }}" class="btn btn-primary" style="text-decoration:none; flex: none;">
            + Create New Order
        </a>
    </div>

    <table class="data-table">
        <thead>
            <tr>
                <th>Order No</th>
                <th>Site</th>
                <th>Date</th>
                <th>Status</th>
                <th>Priority</th>
                <th>Items</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($orders as $order)
                <tr>
                    <td>{{ $order->order_number }}</td>
                    <td>{{ $order->site->name }}</td>
                    <td>{{ $order->created_at->format('M d, Y') }}</td>
                    <td>
                        <span class="badge badge-{{ $order->status }}">{{ ucfirst($order->status) }}</span>
                    </td>
                    <td>
                        @if($order->priority === 'urgent')
                            <span style="color:var(--red); font-weight:600;">URGENT</span>
                        @else
                            Normal
                        @endif
                    </td>
                    <td>{{ $order->items->count() }}</td>
                    <td>
                        <a href="{{ route('engineer.orders.show', $order) }}" style="color:var(--accent); font-weight:500;">
                            View / Manage
                        </a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" style="text-align:center; color:#8a97b8; padding: 30px;">
                        No orders found. Click "Create New Order" to get started.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
    
    <div style="margin-top: 20px;">
        {{ $orders->links() }}
    </div>
</div>
@endsection
