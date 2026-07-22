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
                <span class="meta-label">Requested Delivery</span>
                <span class="meta-value">{{ $order->requested_delivery_date ? $order->requested_delivery_date->format('M d, Y') : 'TBD' }}</span>
            </div>
        </div>
        <div>
            @if($order->status !== 'draft')
                <a href="{{ route('orders.report', $order) }}" target="_blank" class="btn btn-secondary" style="margin-right:10px;">Download Cut List PDF</a>
            @endif
            @if($order->status === 'draft' || $order->status === 'rejected')
                <form action="{{ route('engineer.orders.submit', $order) }}" method="POST" style="display:inline;">
                    @csrf
                    <button type="submit" class="btn btn-primary" onclick="confirmSubmit(event, 'Are you sure you want to submit this order?', 'Yes, submit it!')">
                        Submit Order
                    </button>
                </form>
            @endif
            @if($order->status === 'submitted')
                <form action="{{ route('engineer.orders.revert', $order) }}" method="POST" style="display:inline;">
                    @csrf
                    <button type="submit" class="btn btn-secondary" onclick="confirmSubmit(event, 'Are you sure you want to revert this order back to draft?', 'Yes, revert it')">
                        Set to Draft
                    </button>
                </form>
            @endif
            @if($order->notes && $order->status === 'rejected')
                <div style="margin-top:10px; color:var(--red); font-size:14px;"><strong>Rejection Note:</strong><br>{{ $order->notes }}</div>
            @endif
        </div>
    </div>

    <!-- MAIN DUCT CALCULATOR LAYOUT -->
    <div class="main" style="padding:0; margin-top:20px;">

        <!-- LEFT: FORM -->
        @include('partials.orders.duct_item_form_card', [
            'storeRoute'       => route('engineer.orders.items.store', $order),
            'editableStatuses' => ['draft', 'rejected'],
            'cardTitle'        => 'Add Duct Item',
            'submitBtnLabel'   => 'Add to List',
            'show3dExpand'     => true,
        ])

        <!-- RIGHT: LIST -->
        @include('partials.orders.fabrication_list', [
            'editableStatuses'  => ['draft', 'rejected'],
            'remarkRoutePrefix' => 'engineer',
            'qtyRoutePrefix'    => 'engineer',
            'showDeleteBtn'     => true,
        ])

    </div>

    <!-- 3D Modal -->
    <div id="duct-3d-modal" class="duct-3d-modal" onclick="on3DModalBackdrop(event)">
        <div class="duct-3d-modal-card">
            <div class="duct-3d-modal-header">
                <div class="duct-3d-modal-title-wrap">
                    <span class="duct-3d-modal-title">3D Duct Preview</span>
                    <span id="duct-3d-modal-type" class="duct-3d-modal-type">-</span>
                </div>
                <button class="duct-3d-modal-close" type="button" onclick="close3DModal()" aria-label="Close 3D preview">✕</button>
            </div>
            <div class="duct-3d-modal-body" id="duct-modal-body">
                <div id="duct-3d-canvas-wrap-modal"></div>
                <img id="duct-static-img-modal" class="y-duct-static" src="{{ asset('duct/y-duct.png') }}" alt="Y-Duct diagram">
                <div id="duct-static-overlay-modal" class="y-duct-overlay"></div>
            </div>
        </div>
    </div>
    
    @include('partials.comments')
</div>
@endsection

@push('scripts')
    @include('partials.orders.order_item_scripts', [
        'editItemUrlPrefix' => '/engineer/orders/' . $order->id . '/items',
        'storeRoute'        => route('engineer.orders.items.store', $order),
        'cancelBtnLabel'    => 'Add to List',
        'ductTypes'         => $ductTypes,
    ])
@endpush
