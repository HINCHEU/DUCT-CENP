{{--
    Partial: partials/orders/order_item_styles.blade.php
    Shared styles for the Add Duct Item card and remark popover.
    Include inside @push('styles') in the parent view.
--}}
<style>
    .order-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 12px;
        padding: 8px 16px;
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
    /* Remark inline popover */
    .remark-popover {
        position: fixed;
        z-index: 9999;
        background: #fff;
        border: 1.5px solid #1B3F8B;
        border-radius: 10px;
        box-shadow: 0 8px 32px rgba(27,63,139,0.18);
        padding: 12px;
        width: 260px;
        animation: remarkFadeIn 0.15s ease;
    }
    @keyframes remarkFadeIn {
        from { opacity: 0; transform: translateY(-4px); }
        to   { opacity: 1; transform: translateY(0); }
    }
    .remark-popover textarea {
        width: 100%;
        box-sizing: border-box;
        border: 1px solid #dde3f0;
        border-radius: 6px;
        padding: 7px 9px;
        font-size: 12px;
        resize: vertical;
        min-height: 64px;
        outline: none;
        color: #0d1a3a;
        font-family: inherit;
        transition: border-color 0.15s;
    }
    .remark-popover textarea:focus { border-color: #1B3F8B; }
    .remark-popover-label {
        font-size: 11px;
        font-weight: 700;
        color: #1B3F8B;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 6px;
    }
    .remark-popover-btns {
        display: flex;
        gap: 6px;
        margin-top: 8px;
        justify-content: flex-end;
    }
    .remark-popover-btns button {
        padding: 4px 12px;
        border-radius: 5px;
        border: none;
        cursor: pointer;
        font-size: 12px;
        font-weight: 600;
    }
    .remark-btn-save   { background: #1B3F8B; color: #fff; }
    .remark-btn-save:hover { background: #14317a; }
    .remark-btn-cancel { background: #f0f3fa; color: #555; }
    .remark-btn-cancel:hover { background: #e0e5f0; }
    .item-remark-display:hover .remark-edit-icon { opacity: 1 !important; }
</style>
