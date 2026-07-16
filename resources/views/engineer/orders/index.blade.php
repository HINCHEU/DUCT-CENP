@extends('layouts.app')

@section('content')
<div class="container">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom: 20px;">
        <h2 style="font-family:'Barlow Condensed', sans-serif; font-size:24px; color:#1B3F8B;">My Orders</h2>
        <a href="{{ route('engineer.orders.create') }}" class="btn btn-primary" style="text-decoration:none; flex: none;">
            + Create New Order
        </a>
    </div>

    <div class="odoo-search-wrapper">
        <!-- Top Search Bar -->
        <div class="odoo-search-container">
            @if(request('status'))
                <div class="odoo-chip">
                    <span class="chip-label">Status:</span> {{ ucfirst(request('status')) }}
                    <a href="{{ request()->fullUrlWithQuery(['status' => null, 'page' => 1]) }}">×</a>
                </div>
            @endif
            
            @if(request('priority'))
                <div class="odoo-chip">
                    <span class="chip-label">Priority:</span> {{ ucfirst(request('priority')) }}
                    <a href="{{ request()->fullUrlWithQuery(['priority' => null, 'page' => 1]) }}">×</a>
                </div>
            @endif

            <form action="{{ route('engineer.orders.index') }}" method="GET">
                @if(request('status')) <input type="hidden" name="status" value="{{ request('status') }}"> @endif
                @if(request('priority')) <input type="hidden" name="priority" value="{{ request('priority') }}"> @endif
                
                <input type="text" name="search" class="odoo-search-input" placeholder="Search order # or site..." value="{{ request('search') }}">
                <button type="submit" style="background:none; border:none; cursor:pointer;" class="odoo-search-icon">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                </button>
            </form>
            
            @if(request('search'))
                <a href="{{ request()->fullUrlWithQuery(['search' => null, 'page' => 1]) }}" style="font-size: 12px; color: #888; text-decoration: none; margin-left: 8px;">Clear Search</a>
            @endif
        </div>

        <!-- Bottom Action Bar -->
        <div class="odoo-action-bar">
            <div class="odoo-action-left">
                <div class="odoo-dropdown">
                    <button type="button" class="odoo-dropdown-btn" onclick="toggleDropdown('filterDropdown')">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"></polygon></svg>
                        Filters
                    </button>
                    <div id="filterDropdown" class="odoo-dropdown-menu">
                        <div style="padding: 4px 12px; font-size: 11px; color: #888; text-transform: uppercase; font-weight: bold;">By Status</div>
                        <a href="{{ request()->fullUrlWithQuery(['status' => 'draft', 'page' => 1]) }}" class="odoo-dropdown-item">Draft</a>
                        <a href="{{ request()->fullUrlWithQuery(['status' => 'submitted', 'page' => 1]) }}" class="odoo-dropdown-item">Submitted</a>
                        <a href="{{ request()->fullUrlWithQuery(['status' => 'approved', 'page' => 1]) }}" class="odoo-dropdown-item">Approved</a>
                        <a href="{{ request()->fullUrlWithQuery(['status' => 'rejected', 'page' => 1]) }}" class="odoo-dropdown-item">Rejected</a>
                        <a href="{{ request()->fullUrlWithQuery(['status' => 'fabricating', 'page' => 1]) }}" class="odoo-dropdown-item">Fabricating</a>
                        <a href="{{ request()->fullUrlWithQuery(['status' => 'ready', 'page' => 1]) }}" class="odoo-dropdown-item">Ready</a>
                        <a href="{{ request()->fullUrlWithQuery(['status' => 'delivered', 'page' => 1]) }}" class="odoo-dropdown-item">Delivered</a>
                        
                        <div class="odoo-dropdown-divider"></div>
                        <div style="padding: 4px 12px; font-size: 11px; color: #888; text-transform: uppercase; font-weight: bold;">By Priority</div>
                        <a href="{{ request()->fullUrlWithQuery(['priority' => 'urgent', 'page' => 1]) }}" class="odoo-dropdown-item">Urgent</a>
                        <a href="{{ request()->fullUrlWithQuery(['priority' => 'normal', 'page' => 1]) }}" class="odoo-dropdown-item">Normal</a>
                    </div>
                </div>
                
                <div class="odoo-dropdown">
                    <button type="button" class="odoo-dropdown-btn">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="8" y1="6" x2="21" y2="6"></line><line x1="8" y1="12" x2="21" y2="12"></line><line x1="8" y1="18" x2="21" y2="18"></line><line x1="3" y1="6" x2="3.01" y2="6"></line><line x1="3" y1="12" x2="3.01" y2="12"></line><line x1="3" y1="18" x2="3.01" y2="18"></line></svg>
                        Group By
                    </button>
                </div>
                
                <div class="odoo-dropdown">
                    <button type="button" class="odoo-dropdown-btn">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon></svg>
                        Favorites
                    </button>
                </div>
            </div>
            
            <div class="odoo-action-right">
                <div class="odoo-pager">
                    @if($orders->total() > 0)
                        <span>{{ $orders->firstItem() }}-{{ $orders->lastItem() }} / {{ $orders->total() }}</span>
                        @if(!$orders->onFirstPage())
                            <a href="{{ $orders->previousPageUrl() }}"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"></polyline></svg></a>
                        @endif
                        @if($orders->hasMorePages())
                            <a href="{{ $orders->nextPageUrl() }}"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"></polyline></svg></a>
                        @endif
                    @endif
                </div>
                <div style="display: flex; gap: 4px;">
                    <button type="button" class="odoo-dropdown-btn" style="background:#e9ecef;"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="8" y1="6" x2="21" y2="6"></line><line x1="8" y1="12" x2="21" y2="12"></line><line x1="8" y1="18" x2="21" y2="18"></line><line x1="3" y1="6" x2="3.01" y2="6"></line><line x1="3" y1="12" x2="3.01" y2="12"></line><line x1="3" y1="18" x2="3.01" y2="18"></line></svg></button>
                    <button type="button" class="odoo-dropdown-btn"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="7"></rect><rect x="14" y="3" width="7" height="7"></rect><rect x="14" y="14" width="7" height="7"></rect><rect x="3" y="14" width="7" height="7"></rect></svg></button>
                </div>
            </div>
        </div>
    </div>

    <script>
        function toggleDropdown(id) {
            document.getElementById(id).classList.toggle("show");
        }
        
        window.onclick = function(event) {
            if (!event.target.closest('.odoo-dropdown-btn')) {
                var dropdowns = document.getElementsByClassName("odoo-dropdown-menu");
                for (var i = 0; i < dropdowns.length; i++) {
                    var openDropdown = dropdowns[i];
                    if (openDropdown.classList.contains('show')) {
                        openDropdown.classList.remove('show');
                    }
                }
            }
        }
    </script>

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
