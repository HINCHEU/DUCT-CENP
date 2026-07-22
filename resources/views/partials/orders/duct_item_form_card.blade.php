{{--
    Partial: partials/orders/duct_item_form_card.blade.php

    Variables expected from the parent view:
      $order            – the Order model
      $storeRoute       – URL for the form action (POST to store a new item)
      $editableStatuses – array of order statuses where editing is allowed
      $cardTitle        – string shown in the card header (e.g. "Add Duct Item")
      $submitBtnLabel   – HTML string for the submit button label
      $show3dExpand     – bool; whether to show the ⤢ expand-3D button
--}}
<div>
    <div class="card">
        <div class="card-header">
            <div class="card-header-left">
                <div class="card-icon">
                    <svg viewBox="0 0 24 24">
                        <path d="M19 3H5a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2V5a2 2 0 00-2-2zm-7 14H8v-2h4v2zm4-4H8v-2h8v2zm0-4H8V7h8v2z" />
                    </svg>
                </div>
                <span class="card-title">{{ $cardTitle }}</span>
            </div>
        </div>

        {{-- Disable form when order is not in an editable status --}}
        <div class="card-body" @if(!in_array($order->status, $editableStatuses)) style="opacity:0.6; pointer-events:none;" @endif>

            <form id="add-item-form" action="{{ $storeRoute }}" method="POST">
                @csrf
                <input type="hidden" name="duct_type_id" id="hidden-duct-type-id" value="">

                <div class="field-group">
                    <label class="field-label">Duct Type</label>
                    <select id="duct-type" class="field-input" onchange="onTypeChange()"></select>
                </div>

                <div id="duct-img-wrap" class="duct-diagram">
                    <div id="duct-3d-canvas-wrap"></div>
                    <img id="duct-static-img" class="y-duct-static" src="{{ asset('duct/y-duct.png') }}" alt="Y-Duct diagram">
                    <div id="duct-static-overlay" class="y-duct-overlay"></div>
                    @if($show3dExpand)
                        <button class="expand-3d-btn" type="button" onclick="open3DModal()" title="Expand 3D view" aria-label="Expand 3D view">⤢</button>
                    @endif
                    <span id="duct-type-tag" class="duct-type-tag">—</span>
                    <span class="dim-hint">🖱 Drag to rotate</span>
                </div>

                <div class="section-divider">
                    <div class="section-divider-line"></div>
                    <span class="section-divider-label">Dimensions (mm)</span>
                    <div class="section-divider-line"></div>
                </div>

                <div id="dynamic-fields" class="fields-grid"></div>

                <div style="display: flex; gap: 20px; align-items: center; margin-top: 10px;">
                    <div class="qty-row" style="margin-top: 0;">
                        <span class="qty-label">Thickness</span>
                        <select id="thickness-select" name="thickness" class="thickness-select">
                            <option value="0.6">0.6 mm</option>
                            <option value="0.8" selected>0.8 mm</option>
                            <option value="1.0">1.0 mm</option>
                            <option value="1.2">1.2 mm</option>
                        </select>
                    </div>

                    <div class="qty-row" style="margin-top: 0;">
                        <span class="qty-label">Quantity</span>
                        <input type="number" id="qty" name="quantity" class="qty-input" value="1" min="1" oninput="updatePreview()">
                        <span style="font-size:12px;color:var(--text-muted)">nos</span>
                    </div>
                </div>

                <div id="preview-area" class="preview-box">
                    <div class="preview-muted">Fill dimensions above to preview surface area</div>
                </div>

                <div class="field-group" style="margin-top: 10px;">
                    <label class="field-label" for="remarks-input">Remark <span style="color:#8a97b8;font-weight:400;">(optional)</span></label>
                    <textarea id="remarks-input" name="remarks" rows="2" class="field-input"
                              placeholder="e.g. Install near AHU-1, painted black..."
                              style="resize:vertical; min-height:52px; font-size:13px;"></textarea>
                </div>

                <div class="btn-row">
                    <button type="button" class="btn btn-primary" onclick="submitItemForm()">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="white">
                            <path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z" />
                        </svg>
                        {!! $submitBtnLabel !!}
                    </button>
                    <button type="button" class="btn btn-secondary" onclick="clearFields()">Clear</button>
                </div>
            </form>
        </div>
    </div>
</div>
