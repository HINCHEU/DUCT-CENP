@extends('layouts.app')

@push('styles')
    @include('partials.orders.order_item_styles')
@endpush

@section('content')
<div class="container" style="padding-top:0;">
    
    <div class="order-header">
        <div>
            <h2 style="margin:0; font-family:'Barlow Condensed', sans-serif; font-size:24px; color:#1B3F8B;">
                Order {{ $order->order_number }}
            </h2>
            <div style="font-size: 14px; color:#8a97b8;">Site: {{ $order->site->name }} | Engineer: {{ $order->user->name }}</div>
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
        <div style="display:flex; gap: 10px;">
            @if($order->status !== 'draft')
                <a href="{{ route('orders.report', $order) }}" target="_blank" class="btn btn-secondary">Download Cut List PDF</a>
            @endif
            @if($order->status === 'draft')
                <form action="{{ route('manager.orders.submit', $order) }}" method="POST" style="display:inline;">
                    @csrf
                    <button type="submit" class="btn btn-primary" style="background-color: var(--navy);" onclick="confirmSubmit(event, 'Submit this order for approval?', 'Yes, submit it!')">
                        Submit Order
                    </button>
                </form>
            @endif
            @if($order->status === 'submitted')
                <form action="{{ route('manager.orders.approve', $order) }}" method="POST" style="display:inline;">
                    @csrf
                    <button type="submit" class="btn btn-primary" style="background-color: var(--navy);" onclick="confirmSubmit(event, 'Approve this order for workshop fabrication?', 'Yes, approve it!')">
                        Approve
                    </button>
                </form>
                
                <button type="button" class="btn btn-secondary" onclick="document.getElementById('reject-form-container').style.display = 'block';">
                    Reject
                </button>
            @endif
        </div>
    </div>
    
    <div id="reject-form-container" style="display:none; background:#fff; padding:16px; border-radius:8px; margin-bottom:20px; box-shadow:0 2px 8px rgba(0,0,0,0.05); border-left:4px solid var(--red);">
        <h4 style="margin-top:0; color:var(--red);">Reject Order</h4>
        <form action="{{ route('manager.orders.reject', $order) }}" method="POST">
            @csrf
            <textarea name="notes" class="field-input" rows="3" placeholder="Provide a reason for rejection..." required style="width:100%; margin-bottom:10px;"></textarea>
            <button type="submit" class="btn" style="background-color:var(--red); color:#fff; border:none; padding:8px 16px; border-radius:4px; cursor:pointer;">Confirm Rejection</button>
            <button type="button" class="btn-ghost" onclick="document.getElementById('reject-form-container').style.display = 'none';">Cancel</button>
        </form>
    </div>

    <!-- MAIN DUCT CALCULATOR LAYOUT -->
    <div class="main" style="padding:0;">

        <!-- LEFT: FORM -->
        @include('partials.orders.duct_item_form_card', [
            'storeRoute'       => route('manager.orders.items.store', $order->id),
            'editableStatuses' => ['draft', 'submitted'],
            'cardTitle'        => 'Edit / Add Duct Item',
            'submitBtnLabel'   => '<svg width="14" height="14" viewBox="0 0 24 24" fill="white"><path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z" /></svg> Add / Update Item',
            'show3dExpand'     => true,
        ])

        <!-- RIGHT: LIST -->
        @include('partials.orders.fabrication_list', [
            'editableStatuses'  => ['draft', 'submitted'],
            'remarkRoutePrefix' => 'manager',
            'qtyRoutePrefix'    => 'manager',
            'showDeleteBtn'     => true,
        ])

    </div>
    
    @include('partials.comments')
</div>
@endsection

@push('scripts')
    @include('partials.orders.order_item_scripts', [
        'editItemUrlPrefix' => '/manager/orders/' . $order->id . '/items',
        'storeRoute'        => route('manager.orders.items.store', $order->id),
        'cancelBtnLabel'    => '<svg width="14" height="14" viewBox="0 0 24 24" fill="white"><path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z" /></svg> Add / Update Item',
        'ductTypes'         => $ductTypes,
    ])
@endpush
