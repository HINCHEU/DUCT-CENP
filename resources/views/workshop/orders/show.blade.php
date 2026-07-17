@extends('layouts.app')

@push('styles')
    <style>
        .order-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding: 16px;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        }
        .order-meta {
            display: flex;
            gap: 20px;
        }
        .meta-item {
            display: flex;
            flex-direction: column;
        }
        .meta-label {
            font-size: 12px;
            color: #8a97b8;
            text-transform: uppercase;
            font-weight: 600;
        }
        .meta-value {
            font-size: 16px;
            color: #0d1a3a;
            font-weight: 500;
        }
        .status-actions {
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
            display: flex;
            align-items: center;
            gap: 15px;
        }
    </style>
@endpush

@section('content')
<div class="container" style="padding-top:0;">
    
    <div class="order-header">
        <div>
            <h2 style="margin:0; font-family:'Barlow Condensed', sans-serif; font-size:24px; color:#1B3F8B;">
                Order {{ $order->order_number }}
            </h2>
            <div style="font-size: 14px; color:#8a97b8;">Site: {{ $order->site->name }}</div>
        </div>
        <div class="order-meta">
            <div class="meta-item">
                <span class="meta-label">Status</span>
                <span class="badge badge-{{ $order->status }}">{{ ucfirst($order->status) }}</span>
            </div>
            <div class="meta-item">
                <span class="meta-label">Priority</span>
                <span class="meta-value">{{ ucfirst($order->priority) }}</span>
            </div>
            <div class="meta-item">
                <span class="meta-label">Delivery Date</span>
                <span class="meta-value">{{ $order->requested_delivery_date ? $order->requested_delivery_date->format('M d, Y') : 'TBD' }}</span>
            </div>
        </div>
        <div>
            <a href="{{ route('orders.report', $order) }}" target="_blank" class="btn btn-secondary">Download Cut List PDF</a>
        </div>
    </div>

    @if($order->status !== 'delivered')
    <div class="status-actions">
        <h4 style="margin:0;">Update Status:</h4>
        <form action="{{ route('workshop.orders.status', $order) }}" method="POST" style="display:flex; gap:10px; align-items:center;">
            @csrf
            @if($order->status === 'approved')
                <input type="hidden" name="status" value="fabricating">
                <button type="submit" class="btn btn-primary" style="background-color:#92400e;">Start Fabrication</button>
            @elseif($order->status === 'fabricating')
                <input type="hidden" name="status" value="ready">
                <button type="submit" class="btn btn-primary" style="background-color:#3730a3;">Mark as Ready</button>
            @elseif($order->status === 'ready')
                <input type="hidden" name="status" value="delivered">
                <button type="submit" class="btn btn-primary" style="background-color:#115e59;">Mark as Delivered</button>
            @endif
        </form>
    </div>
    @endif

    <div class="card" style="display:flex;flex-direction:column">
        <div class="card-header">
            <div class="card-header-left">
                <div class="card-icon" style="background:var(--red)">
                    <svg viewBox="0 0 24 24">
                        <path d="M9 17H7v-7h2v7zm4 0h-2V7h2v10zm4 0h-2v-4h2v4zm2 2H5V5h14v14zm0-16H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2z" />
                    </svg>
                </div>
                <span class="card-title">Fabrication List</span>
            </div>
        </div>

        <div class="card-body">
            @php
                $totalQty = $order->items->sum('quantity');
                $totalArea = $order->items->where('ductType.unit', 'm²')->sum('total_area');
                $totalLength = $order->items->where('ductType.unit', 'm')->sum('total_area');
            @endphp
            
            <table class="data-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Type</th>
                        <th>Dimensions</th>
                        <th>Thickness</th>
                        <th>Qty</th>
                        <th>Area/Length</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($order->items as $index => $item)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $item->ductType->name }}</td>
                            <td style="font-family:monospace;">
                                @foreach($item->dimensions as $k => $v)
                                    {{ $k }}:{{ $v }}
                                @endforeach
                            </td>
                            <td>{{ $item->thickness }} mm</td>
                            <td>{{ $item->quantity }} nos</td>
                            <td>{{ number_format($item->total_area, 2) }} {{ $item->ductType->unit }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            
            @if($order->items->count() > 0)
            <div class="total-bar" style="margin-top:20px; border-radius:8px;">
                <div>
                    <div class="total-label">Grand Total</div>
                    <div class="total-items">{{ $order->items->count() }} items · {{ $totalQty }} nos</div>
                </div>
                <div class="total-value">
                    @if($totalArea > 0) {{ number_format($totalArea, 4) }} <span class="total-m2">m²</span> @endif
                    @if($totalArea > 0 && $totalLength > 0) <span style="font-size:16px;color:rgba(255,255,255,0.5);margin:0 10px;">|</span> @endif
                    @if($totalLength > 0) {{ number_format($totalLength, 2) }} <span class="total-m2">m</span> @endif
                </div>
            </div>
            @endif
        </div>
    </div>
    
    @include('partials.comments')
</div>
@endsection
