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
        <div style="display:flex; gap: 10px; align-items: center;">
            <a href="{{ route('orders.report', $order) }}" target="_blank" class="btn btn-secondary">Download Cut List PDF</a>
            @if($order->status !== 'delivered')
                <form action="{{ route('workshop.orders.status', $order) }}" method="POST" style="display:inline;">
                    @csrf
                    @if($order->status === 'approved')
                        <input type="hidden" name="status" value="fabricating">
                        <button type="submit" class="btn btn-primary" style="background-color: var(--navy); border:none;">Start Fabrication</button>
                    @elseif($order->status === 'fabricating')
                        <input type="hidden" name="status" value="ready">
                        <button type="submit" class="btn btn-primary" style="background-color: #3730a3; border:none;">Mark as Ready</button>
                    @elseif($order->status === 'ready')
                        <input type="hidden" name="status" value="delivered">
                        <button type="submit" class="btn btn-primary" style="background-color: #115e59; border:none;">Mark as Delivered</button>
                    @endif
                </form>
            @endif
        </div>
    </div>

    @php
        $totalQty = $order->items->sum('quantity');
        $ductItems = $order->items->filter(fn($i) => !in_array($i->ductType->formula_key, ['angle_bar', 'angle_bar_u']));
        $supportItems = $order->items->filter(fn($i) => in_array($i->ductType->formula_key, ['angle_bar', 'angle_bar_u']));
        $totalArea   = $ductItems->sum('total_area');
        $totalLength = $supportItems->sum('total_area');
    @endphp

    {{-- Stat cards --}}
    <div class="stats-row">
        <div class="stat-card navy-accent">
            <div class="stat-label">Items</div>
            <div class="stat-value">{{ $order->items->count() }}</div>
        </div>
        <div class="stat-card navy-accent">
            <div class="stat-label">Total Qty</div>
            <div class="stat-value">{{ $totalQty }} <span class="stat-unit">nos</span></div>
        </div>
        @if($totalArea > 0)
        <div class="stat-card accent">
            <div class="stat-label">Total Area</div>
            <div class="stat-value">{{ number_format($totalArea, 4) }} <span class="stat-unit">m²</span></div>
        </div>
        @endif
        @if($totalLength > 0)
        <div class="stat-card accent">
            <div class="stat-label">Total Length</div>
            <div class="stat-value">{{ number_format($totalLength, 2) }} <span class="stat-unit">m</span></div>
        </div>
        @endif
    </div>

    {{-- Duct Fabrication List --}}
    @if($ductItems->count() > 0)
    <div class="card" style="display:flex;flex-direction:column; margin-bottom:20px;">
        <div class="card-header">
            <div class="card-header-left">
                <div class="card-icon" style="background:var(--red)">
                    <svg viewBox="0 0 24 24"><path d="M9 17H7v-7h2v7zm4 0h-2V7h2v10zm4 0h-2v-4h2v4zm2 2H5V5h14v14zm0-16H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2z" /></svg>
                </div>
                <span class="card-title">Duct Fabrication List</span>
            </div>
        </div>
        <div class="card-body" style="flex:1;overflow-y:auto;padding-bottom:0;">
            <div id="item-list">
                @php $ductGroups = $ductItems->groupBy(fn($i) => $i->ductType->name); @endphp
                @foreach($ductGroups as $typeName => $group)
                    @php
                        $groupTotal = $group->sum('total_area');
                    @endphp
                    <div class="list-group">
                        <div class="list-group-header">
                            <div>{{ $typeName }}</div>
                            <div class="group-total">{{ number_format($groupTotal, 2) }} m²</div>
                        </div>
                        @foreach($group as $item)
                            <div class="list-item-row">
                                <div class="item-main-details">
                                    <div class="item-dimensions" style="font-family:monospace;">
                                        <span style="color:#8a97b8; font-size:11px; margin-right:4px;">{{ $loop->iteration }}.</span>
                                        {{ $item->formatted_dimensions }}
                                    </div>
                                    <div class="item-thickness">{{ $item->thickness }}mm thickness</div>
                                    @if($item->remarks)
                                        <div style="font-size:11px; color:#1B3F8B; margin-top:2px; font-style:italic;">&#128221; {{ $item->remarks }}</div>
                                    @endif
                                </div>
                                <div class="item-qty-multiplier">×{{ $item->quantity }}</div>
                                <div class="item-final-area">{{ number_format($item->total_area, 2) }} m²</div>
                                <div class="item-actions"></div>
                            </div>
                        @endforeach
                    </div>
                @endforeach
            </div>
        </div>
        <div class="total-bar">
            <div>
                <div class="total-label">Duct Total</div>
                <div class="total-items">{{ $ductItems->count() }} items · {{ $ductItems->sum('quantity') }} nos</div>
            </div>
            <div class="total-value">{{ number_format($totalArea, 4) }} <span class="total-m2">m²</span></div>
        </div>
    </div>
    @endif

    {{-- Support Materials List --}}
    @if($supportItems->count() > 0)
    <div class="card" style="display:flex;flex-direction:column; margin-bottom:20px;">
        <div class="card-header">
            <div class="card-header-left">
                <div class="card-icon" style="background:#3730a3">
                    <svg viewBox="0 0 24 24"><path d="M19 3H5a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2V5a2 2 0 00-2-2zm-7 14H8v-2h4v2zm4-4H8v-2h8v2zm0-4H8V7h8v2z" /></svg>
                </div>
                <span class="card-title">Support Materials List</span>
            </div>
        </div>
        <div class="card-body" style="flex:1;overflow-y:auto;padding-bottom:0;">
            <div>
                @php $supportGroups = $supportItems->groupBy(fn($i) => $i->ductType->name); @endphp
                @foreach($supportGroups as $typeName => $group)
                    @php $groupTotal = $group->sum('total_area'); @endphp
                    <div class="list-group">
                        <div class="list-group-header linear-group">
                            <div>{{ $typeName }}</div>
                            <div class="group-total">{{ number_format($groupTotal, 2) }} m (linear)</div>
                        </div>
                        @foreach($group as $item)
                            <div class="list-item-row">
                                <div class="item-main-details">
                                    <div class="item-dimensions" style="font-family:monospace;">
                                        <span style="color:#8a97b8; font-size:11px; margin-right:4px;">{{ $loop->iteration }}.</span>
                                        {{ $item->formatted_dimensions }}
                                    </div>
                                    <div class="item-thickness">length only — not m²</div>
                                    @if($item->remarks)
                                        <div style="font-size:11px; color:#1B3F8B; margin-top:2px; font-style:italic;">&#128221; {{ $item->remarks }}</div>
                                    @endif
                                </div>
                                <div class="item-qty-multiplier">×{{ $item->quantity }}</div>
                                <div class="item-final-area">{{ number_format($item->total_area, 2) }} m</div>
                                <div class="item-actions"></div>
                            </div>
                        @endforeach
                    </div>
                @endforeach
            </div>
        </div>
        <div class="total-bar">
            <div>
                <div class="total-label">Support Total</div>
                <div class="total-items">{{ $supportItems->count() }} items · {{ $supportItems->sum('quantity') }} nos</div>
            </div>
            <div class="total-value">{{ number_format($totalLength, 2) }} <span class="total-m2">m</span></div>
        </div>
    </div>
    @endif
    
    @include('partials.comments')
</div>
@endsection
