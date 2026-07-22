{{--
    Partial: partials/orders/order_item_scripts.blade.php

    Shared JS for the duct item form (editItem, cancelEdit, submitItemForm,
    getStaticImageSrc, inline remark popover).

    Variables expected from the parent view:
      $order                 – the Order model
      $editItemUrlPrefix     – URL path prefix for editing items,
                               e.g. '/engineer/orders' or '/manager/orders'
      $storeRoute            – URL to restore form action on cancel (same as in the card)
      $cancelBtnLabel        – HTML label to restore on the submit button after cancel
                               e.g. 'Add to List' or '<svg ...> Add / Update Item'
      $ductTypes             – collection of DuctType models (for @json injection)
--}}
<script>
    const ductTypesDb = @json($ductTypes);
</script>
<script src="{{ asset('ducts.js') }}"></script>
<script src="{{ asset('viewer.js') }}"></script>
<script src="{{ asset('app.js') }}"></script>
<script>
    const orderItems = @json($order->items->map(function($i) { $i->duct_type_key = $i->ductType->formula_key; return $i; }));

    function editItem(id) {
        const item = orderItems.find(i => i.id === id);
        if (!item) return;

        // Set duct type
        const typeSelect = document.getElementById('duct-type');
        typeSelect.value = item.duct_type_key;
        onTypeChange();

        // Fill dimension inputs
        if (item.dimensions) {
            Object.keys(item.dimensions).forEach(k => {
                const input = document.getElementById('f_' + k);
                if (input) input.value = item.dimensions[k];
            });
        }

        // Fill other fields
        const thicknessSelect = document.getElementById('thickness-select');
        if (thicknessSelect) thicknessSelect.value = item.thickness;

        const qtyInput = document.getElementById('qty');
        if (qtyInput) qtyInput.value = item.quantity;

        const canvasCheckbox = document.querySelector('input[name="canvas_flange"]');
        if (canvasCheckbox) canvasCheckbox.checked = item.canvas_flange == 1;

        const strutCheckbox = document.querySelector('input[name="inner_strut"]');
        if (strutCheckbox) strutCheckbox.checked = item.inner_strut == 1;

        const remarksInput = document.getElementById('remarks-input');
        if (remarksInput) remarksInput.value = item.remarks || '';

        updatePreview();

        // Switch form to PUT on the item's URL
        const form = document.getElementById('add-item-form');
        form.action = `{{ $editItemUrlPrefix }}/${item.id}`;

        let methodField = form.querySelector('input[name="_method"]');
        if (!methodField) {
            methodField = document.createElement('input');
            methodField.type  = 'hidden';
            methodField.name  = '_method';
            form.appendChild(methodField);
        }
        methodField.value = 'PUT';

        // Update submit button text
        const submitBtn = form.querySelector('button[type="button"][onclick="submitItemForm()"]')
                       || form.querySelector('button[type="submit"]');
        if (submitBtn) submitBtn.innerHTML = 'Update Item';

        // Add / show Cancel button
        let cancelBtn = document.getElementById('cancel-edit-btn');
        if (!cancelBtn) {
            cancelBtn = document.createElement('button');
            cancelBtn.type      = 'button';
            cancelBtn.id        = 'cancel-edit-btn';
            cancelBtn.className = 'btn btn-ghost';
            cancelBtn.textContent = 'Cancel';
            cancelBtn.style.marginLeft = '10px';
            cancelBtn.onclick = cancelEdit;
            if (submitBtn && submitBtn.parentNode) {
                submitBtn.parentNode.insertBefore(cancelBtn, submitBtn.nextSibling);
            }
        }
        if (cancelBtn) cancelBtn.style.display = 'inline-block';

        // Scroll to form
        form.scrollIntoView({ behavior: 'smooth' });
    }

    function cancelEdit() {
        const form = document.getElementById('add-item-form');
        form.action = "{{ $storeRoute }}";

        const methodField = form.querySelector('input[name="_method"]');
        if (methodField) methodField.remove();

        const submitBtn = form.querySelector('button[type="button"][onclick="submitItemForm()"]')
                       || form.querySelector('button[type="submit"]');
        if (submitBtn) submitBtn.innerHTML = @json($cancelBtnLabel);

        const cancelBtn = document.getElementById('cancel-edit-btn');
        if (cancelBtn) cancelBtn.style.display = 'none';

        form.reset();
        const typeSelect = document.getElementById('duct-type');
        if (typeSelect) typeSelect.selectedIndex = 0;
        onTypeChange();
    }

    function submitItemForm() {
        const key = document.getElementById('duct-type').value;
        const t   = DUCTS[key];
        const f   = getVals();

        const required = t.fields.filter(x => !x.optional);
        if (!required.every(x => +f[x.id] > 0)) {
            alert('Please fill in all required dimensions.');
            return;
        }

        const dbType = ductTypesDb.find(d => d.formula_key === key);
        if (!dbType) {
            alert('Duct type mapping error.');
            return;
        }

        document.getElementById('hidden-duct-type-id').value = dbType.id;

        const form = document.getElementById('add-item-form');
        form.querySelectorAll('.dyn-dim').forEach(el => el.remove());

        Object.keys(f).forEach(k => {
            const input   = document.createElement('input');
            input.type    = 'hidden';
            input.name    = `dimensions[${k}]`;
            input.value   = f[k];
            input.className = 'dyn-dim';
            form.appendChild(input);
        });

        form.submit();
    }

    // Override getStaticImageSrc to use Laravel asset paths
    function getStaticImageSrc(key) {
        if (key === 'r_type')               return '{{ asset('duct/R-TYPE%20DUCT.png') }}';
        if (key === 'r_type_round_two')     return '{{ asset('duct/R-Type-duct-round-two-side.png') }}';
        if (key === '4ways')                return '{{ asset('duct/4-way-duct.png') }}';
        if (key === 'fan_conn')             return '{{ asset('duct/FAN_CONN.png') }}';
        if (key === 'butterfly_round')      return '{{ asset('duct/butterfly-duct-round-out-one-side.png') }}';
        if (key === 'butterfly_round_two')  return '{{ asset('duct/Butterfly-duct-round-two-side.png') }}';
        if (key === 'butterfly_rect')       return '{{ asset('duct/BUTTERFLY%20DUCT.png') }}';
        return '{{ asset('duct/y-duct.png') }}';
    }
</script>
<script>
    // ── Inline remark popover ──────────────────────────────────────────
    (function() {
        let activePopover = null;

        function closePopover() {
            if (activePopover) {
                activePopover.remove();
                activePopover = null;
            }
        }

        function openRemarkPopover(trigger) {
            closePopover();

            const url      = trigger.dataset.remarkUrl;
            const current  = trigger.querySelector('.remark-text');
            const isEmpty  = trigger.classList.contains('item-remark-empty');
            const currentVal = isEmpty ? '' : (current ? current.textContent.trim() : '');

            const pop = document.createElement('div');
            pop.className = 'remark-popover';
            pop.innerHTML = `
                <div class="remark-popover-label">&#128221; Remark</div>
                <textarea id="remark-ta" placeholder="e.g. Install near AHU-1, painted black..." maxlength="500">${currentVal}</textarea>
                <div class="remark-popover-btns">
                    <button class="remark-btn-cancel" type="button">Cancel</button>
                    <button class="remark-btn-save"   type="button">Save</button>
                </div>`;

            document.body.appendChild(pop);
            activePopover = pop;

            // Position below the trigger element
            const rect = trigger.getBoundingClientRect();
            let top  = rect.bottom + 6 + window.scrollY;
            let left = rect.left   + window.scrollX;
            if (left + 260 > window.innerWidth - 8) left = window.innerWidth - 268;
            pop.style.top  = top  + 'px';
            pop.style.left = left + 'px';

            const ta = pop.querySelector('#remark-ta');
            ta.focus();
            ta.setSelectionRange(ta.value.length, ta.value.length);

            pop.querySelector('.remark-btn-cancel').onclick = closePopover;

            pop.querySelector('.remark-btn-save').onclick = function() {
                const newVal  = ta.value.trim();
                const saveBtn = this;
                saveBtn.disabled    = true;
                saveBtn.textContent = 'Saving…';

                fetch(url, {
                    method: 'PATCH',
                    headers: {
                        'Content-Type':  'application/json',
                        'X-CSRF-TOKEN':  document.querySelector('meta[name="csrf-token"]').content,
                        'Accept':        'application/json',
                    },
                    body: JSON.stringify({ remarks: newVal })
                })
                .then(r => r.json())
                .then(data => {
                    if (data.success) {
                        if (newVal) {
                            trigger.classList.remove('item-remark-empty');
                            trigger.style.color     = '#1B3F8B';
                            trigger.style.fontStyle = 'italic';
                            trigger.title           = 'Click to edit remark';
                            trigger.innerHTML = `<span>&#128221;</span><span class="remark-text">${newVal}</span><svg class="remark-edit-icon" width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="#1B3F8B" stroke-width="2.5" style="opacity:0.4;flex-shrink:0;"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>`;
                        } else {
                            trigger.classList.add('item-remark-empty');
                            trigger.style.color     = '#b0bad4';
                            trigger.style.fontStyle = 'normal';
                            trigger.title           = 'Click to add remark';
                            trigger.innerHTML = `<svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="#b0bad4" stroke-width="2.5"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg><span class="remark-text">Add remark…</span>`;
                        }
                        closePopover();
                    }
                })
                .catch(() => {
                    saveBtn.disabled    = false;
                    saveBtn.textContent = 'Save';
                    alert('Failed to save remark.');
                });
            };

            // Keyboard: Ctrl+Enter saves, Escape closes
            ta.addEventListener('keydown', function(e) {
                if (e.key === 'Escape') { closePopover(); }
                if ((e.ctrlKey || e.metaKey) && e.key === 'Enter') {
                    pop.querySelector('.remark-btn-save').click();
                }
            });
        }

        // Delegate click on all remark display elements
        document.addEventListener('click', function(e) {
            if (activePopover && !activePopover.contains(e.target)) {
                closePopover();
                return;
            }
            const trigger = e.target.closest('.item-remark-display');
            if (trigger) openRemarkPopover(trigger);
        });
    })();
</script>
