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
    </style>
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
            @if($order->status === 'submitted')
                <form action="{{ route('manager.orders.approve', $order) }}" method="POST" style="display:inline;">
                    @csrf
                    <button type="submit" class="btn btn-primary" style="background-color: var(--navy);" onclick="return confirm('Approve this order for workshop fabrication?')">
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
        <div>
            <div class="card">
                <div class="card-header">
                    <div class="card-header-left">
                        <div class="card-icon">
                            <svg viewBox="0 0 24 24">
                                <path d="M19 3H5a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2V5a2 2 0 00-2-2zm-7 14H8v-2h4v2zm4-4H8v-2h8v2zm0-4H8V7h8v2z" />
                            </svg>
                        </div>
                        <span class="card-title">Edit / Add Duct Item</span>
                    </div>
                </div>
                
                <!-- Disable form if not submitted -->
                <div class="card-body" @if($order->status !== 'submitted') style="opacity:0.6; pointer-events:none;" @endif>
                    
                    <form id="add-item-form" action="{{ route('manager.orders.items.update', ['order' => $order->id, 'item' => 0]) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <!-- We will use a script to change action to POST for new items, or PUT to an existing item -->
                        <!-- Actually, if a manager adds a new item, they need a POST to items.store -->
                        <input type="hidden" name="duct_type_id" id="hidden-duct-type-id" value="">
                        
                        <div class="field-group">
                            <label class="field-label">Duct Type</label>
                            <select id="duct-type" class="field-input" onchange="onTypeChange()"></select>
                        </div>

                        <div id="duct-img-wrap" class="duct-diagram">
                            <div id="duct-3d-canvas-wrap"></div>
                            <img id="duct-static-img" class="y-duct-static" src="{{ asset('duct/y-duct.png') }}" alt="Y-Duct diagram">
                            <div id="duct-static-overlay" class="y-duct-overlay"></div>
                            <span id="duct-type-tag" class="duct-type-tag">—</span>
                            <span class="dim-hint">🖱 Drag to rotate</span>
                        </div>

                        <div class="section-divider">
                            <div class="section-divider-line"></div>
                            <span class="section-divider-label">Dimensions (mm)</span>
                            <div class="section-divider-line"></div>
                        </div>

                        <div id="dynamic-fields" class="fields-grid"></div>

                        <div class="qty-row">
                            <span class="qty-label">Thickness</span>
                            <select id="thickness-select" name="thickness" class="thickness-select">
                                <option value="0.6">0.6 mm</option>
                                <option value="0.8" selected>0.8 mm</option>
                                <option value="1.0">1.0 mm</option>
                                <option value="1.2">1.2 mm</option>
                            </select>
                        </div>

                        <div class="qty-row">
                            <span class="qty-label">Quantity</span>
                            <input type="number" id="qty" name="quantity" class="qty-input" value="1" min="1" oninput="updatePreview()">
                            <span style="font-size:12px;color:var(--text-muted)">nos</span>
                        </div>
                        
                        <div class="qty-row" style="margin-top: 10px;">
                            <label style="display:flex; align-items:center; gap:8px; font-size:14px;">
                                <input type="checkbox" name="canvas_flange" value="1"> Canvas Flange
                            </label>
                            <label style="display:flex; align-items:center; gap:8px; font-size:14px;">
                                <input type="checkbox" name="inner_strut" value="1"> Inner Strut
                            </label>
                        </div>

                        <div id="preview-area" class="preview-box">
                            <div class="preview-muted">Fill dimensions above to preview surface area</div>
                        </div>

                        <div class="btn-row">
                            <button type="button" class="btn btn-primary" onclick="submitItemForm()">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="white">
                                    <path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z" />
                                </svg>
                                Add / Update Item
                            </button>
                            <button type="button" class="btn btn-secondary" onclick="clearFields()">Clear</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- RIGHT: LIST -->
        <div class="list-panel">
            @php
                $totalQty = $order->items->sum('quantity');
                $totalArea = $order->items->where('ductType.unit', 'm²')->sum('total_area');
                $totalLength = $order->items->where('ductType.unit', 'm')->sum('total_area');
            @endphp
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

                <div class="card-body" style="flex:1;overflow-y:auto;padding-bottom:0">
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
                            @foreach($order->items as $index => $item)
                                <div class="item-row">
                                    <div class="item-num">{{ $index + 1 }}</div>
                                    <div class="item-info">
                                        <div class="item-name">{{ $item->ductType->name }}</div>
                                        <div class="item-dim">
                                            @foreach($item->dimensions as $k => $v)
                                                {{ $k }}:{{ $v }}
                                            @endforeach
                                            | thickness: {{ $item->thickness }}mm
                                        </div>
                                        <span class="item-qty">{{ $item->quantity }} nos</span>
                                    </div>
                                    <div class="item-area">{{ number_format($item->total_area, 2) }}<div class="item-area-unit">{{ $item->ductType->unit }}</div></div>
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
    </div>
</div>
@endsection

@push('scripts')
<script>
    const ductTypesDb = @json($ductTypes);
</script>
<script src="{{ asset('ducts.js') }}"></script>
<script src="{{ asset('viewer.js') }}"></script>
<script src="{{ asset('app.js') }}"></script>
<script>
    // Managers use the AddItem flow but it posts to the engineer route conceptually, or we can just post to engineer items route since it checks permissions
    function submitItemForm() {
        const key = document.getElementById('duct-type').value;
        const t = DUCTS[key];
        const f = getVals();
        
        const required = t.fields.filter(x => !x.optional);
        if (!required.every(x => +f[x.id] > 0)) { 
            alert('Please fill in all required dimensions.'); 
            return; 
        }

        const dbType = ductTypesDb.find(d => d.formula_key === key);
        if(!dbType) {
            alert('Duct type mapping error.');
            return;
        }

        document.getElementById('hidden-duct-type-id').value = dbType.id;
        
        const form = document.getElementById('add-item-form');
        
        // We actually want to POST to store (create new item) 
        // to simplify, since edit is complex
        form.action = "{{ route('engineer.orders.items.store', $order) }}";
        form.querySelector('input[name="_method"]').value = "POST";
        
        form.querySelectorAll('.dyn-dim').forEach(el => el.remove());

        Object.keys(f).forEach(k => {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = `dimensions[${k}]`;
            input.value = f[k];
            input.className = 'dyn-dim';
            form.appendChild(input);
        });
        
        form.submit();
    }
    
    // Override getStaticImageSrc to use asset path
    function getStaticImageSrc(key) {
        if (key === 'r_type') return '{{ asset('duct/R-TYPE%20DUCT.png') }}';
        if (key === 'r_type_round_two') return '{{ asset('duct/R-Type-duct-round-two-side.png') }}';
        if (key === '4ways') return '{{ asset('duct/4-way-duct.png') }}';
        if (key === 'fan_conn') return '{{ asset('duct/FAN_CONN.png') }}';
        if (key === 'butterfly_round') return '{{ asset('duct/butterfly-duct-round-out-one-side.png') }}';
        if (key === 'butterfly_round_two') return '{{ asset('duct/Butterfly-duct-round-two-side.png') }}';
        if (key === 'butterfly_rect') return '{{ asset('duct/BUTTERFLY%20DUCT.png') }}';
        return '{{ asset('duct/y-duct.png') }}';
    }
</script>
@endpush
