{{--
    Partial: partials/orders/fabrication_list.blade.php

    Variables expected from the parent view:
      $order               – the Order model
      $editableStatuses    – array of statuses where qty/delete actions are allowed
      $remarkRoutePrefix   – route name prefix, e.g. 'engineer' or 'manager'
                             Used for: {prefix}.orders.items.updateRemark
      $qtyRoutePrefix      – route name prefix for: {prefix}.orders.items.updateQuantity
      $showDeleteBtn       – bool; show the delete button on each item row
--}}
@php
    $totalQty    = $order->items->sum('quantity');
    $ducts       = $order->items->filter(fn($i) => !in_array($i->ductType->formula_key, ['angle_bar', 'angle_bar_u']));
    $supports    = $order->items->filter(fn($i) =>  in_array($i->ductType->formula_key, ['angle_bar', 'angle_bar_u']));
    $totalArea   = $ducts->sum('total_area');
    $totalLength = $supports->sum('total_area');
@endphp

<div class="list-panel">
    {{-- Stats row --}}
    <div class="stats-row">
        <div class="stat-card navy-accent">
            <div class="stat-label">Items</div>
            <div class="stat-value">{{ $order->items->count() }}</div>
        </div>
        <div class="stat-card navy-accent">
            <div class="stat-label">Total Qty</div>
            <div class="stat-value">{{ $totalQty }} <span class="stat-unit">nos</span></div>
        </div>
        <div class="stat-card accent">
            <div class="stat-label">Total Area</div>
            <div class="stat-value">{{ number_format($totalArea, 4) }} <span class="stat-unit">m²</span></div>
        </div>
        @if($totalLength > 0)
        <div class="stat-card accent">
            <div class="stat-label">Total Length</div>
            <div class="stat-value">{{ number_format($totalLength, 2) }} <span class="stat-unit">m</span></div>
        </div>
        @endif
    </div>

    <div class="card" style="flex:1;display:flex;flex-direction:column">
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

        <div class="card-body" style="flex:1;overflow-y:auto;padding-bottom:0;max-height:550px;">
            <div id="item-list">
                @if($order->items->count() === 0)
                    <div class="empty-state">
                        <div class="empty-icon">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="#8a97b8">
                                <path d="M19 3H5a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2V5a2 2 0 00-2-2zm-5 14H7v-2h7v2zm3-4H7v-2h10v2zm0-4H7V7h10v2z" />
                            </svg>
                        </div>
                        <p>No items yet.<br>Select a duct type and add to list.</p>
                    </div>
                @else
                    @php
                        $groupedItems = $order->items->groupBy(fn($item) => $item->ductType->name);
                    @endphp

                    @foreach($groupedItems as $typeName => $itemsGroup)
                        @php
                            $firstItem      = $itemsGroup->first();
                            $isLinear       = in_array($firstItem->ductType->formula_key, ['angle_bar', 'angle_bar_u']);
                            $groupTotalArea = $itemsGroup->sum('total_area');
                        @endphp
                        <div class="list-group">
                            <div class="list-group-header {{ $isLinear ? 'linear-group' : '' }}">
                                <div>{{ $typeName }}</div>
                                <div class="group-total">{{ number_format($groupTotalArea, 2) }} {{ $isLinear ? 'm (linear)' : 'm²' }}</div>
                            </div>

                            @foreach($itemsGroup as $item)
                                <div class="list-item-row">
                                    <div class="item-main-details">
                                        <div class="item-dimensions" style="font-family: monospace;">
                                            <span style="color:#8a97b8; font-size:11px; margin-right:4px;">{{ $loop->iteration }}.</span>
                                            {{ $item->formatted_dimensions }}
                                        </div>
                                        <div class="item-thickness">
                                            @if($isLinear)
                                                length only — not m²
                                            @else
                                                {{ $item->thickness }}mm thickness
                                            @endif
                                        </div>

                                        {{-- Remark display / edit trigger --}}
                                        @if($item->remarks)
                                            <div class="item-remark-display"
                                                 data-item-id="{{ $item->id }}"
                                                 data-remark-url="{{ route($remarkRoutePrefix . '.orders.items.updateRemark', [$order, $item]) }}"
                                                 title="Click to edit remark"
                                                 style="font-size:11px; color:#1B3F8B; margin-top:3px; font-style:italic; cursor:pointer; display:inline-flex; align-items:center; gap:4px;">
                                                <span>&#128221;</span>
                                                <span class="remark-text">{{ $item->remarks }}</span>
                                                <svg class="remark-edit-icon" width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="#1B3F8B" stroke-width="2.5" style="opacity:0.4; flex-shrink:0;"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
                                            </div>
                                        @else
                                            <div class="item-remark-display item-remark-empty"
                                                 data-item-id="{{ $item->id }}"
                                                 data-remark-url="{{ route($remarkRoutePrefix . '.orders.items.updateRemark', [$order, $item]) }}"
                                                 title="Click to add remark"
                                                 style="font-size:11px; color:#b0bad4; margin-top:3px; cursor:pointer; display:inline-flex; align-items:center; gap:4px;">
                                                <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="#b0bad4" stroke-width="2.5"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
                                                <span class="remark-text">Add remark&hellip;</span>
                                            </div>
                                        @endif
                                    </div>

                                    {{-- Quantity (editable or read-only) --}}
                                    <div class="item-qty-multiplier">
                                        @if(in_array($order->status, $editableStatuses))
                                            <form action="{{ route($qtyRoutePrefix . '.orders.items.updateQuantity', [$order, $item]) }}" method="POST" style="display:inline; margin:0;">
                                                @csrf
                                                @method('PUT')
                                                <div style="display:flex; align-items:center;">
                                                    <span style="color:#8a97b8; margin-right:4px;">×</span>
                                                    <input type="number" name="quantity" value="{{ $item->quantity }}" min="1"
                                                           style="width: 50px; border: 1px solid #dde3f0; border-radius: 4px; padding: 4px 6px; font-size: 14px; text-align: center; color: #0d1a3a; font-weight: 600; outline: none;"
                                                           onfocus="this.style.borderColor='#1B3F8B'"
                                                           onblur="this.style.borderColor='#dde3f0'"
                                                           onchange="this.form.submit()">
                                                </div>
                                            </form>
                                        @else
                                            ×{{ $item->quantity }}
                                        @endif
                                    </div>

                                    <div class="item-final-area">
                                        {{ number_format($item->total_area, 2) }} {{ $isLinear ? 'm' : 'm²' }}
                                    </div>

                                    <div class="item-actions">
                                        {{-- Edit button --}}
                                        @if(in_array($order->status, $editableStatuses))
                                            <button type="button" class="btn-icon btn-edit" title="Edit" onclick="editItem({{ $item->id }})">
                                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
                                            </button>
                                        @endif

                                        {{-- Delete button (role-specific) --}}
                                        @if($showDeleteBtn && in_array($order->status, $editableStatuses))
                                            <form action="{{ route($remarkRoutePrefix . '.orders.items.destroy', [$order, $item]) }}" method="POST" style="display:inline; margin:0;">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn-icon btn-delete" title="Delete">
                                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endforeach
                @endif
            </div>
        </div>

        @if($order->items->count() > 0)
        <div class="total-bar">
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
