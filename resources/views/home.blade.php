@extends('layouts.app')

@push('styles')
<style>
    body {
        background-color: #f8fafc;
    }
    
    .dashboard-wrapper {
        padding: 60px 20px;
        max-width: 1200px;
        margin: 0 auto;
    }

    .home-hero {
        text-align: center;
        margin-bottom: 60px;
    }

    .home-title {
        font-family: 'Barlow', sans-serif;
        font-size: 42px;
        font-weight: 800;
        color: #0f172a;
        margin-bottom: 12px;
    }

    .home-subtitle {
        font-size: 16px;
        color: #64748b;
        margin: 0 auto;
    }

    .dashboard-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 24px;
    }

    @media (max-width: 1024px) {
        .dashboard-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }
    
    @media (max-width: 640px) {
        .dashboard-grid {
            grid-template-columns: 1fr;
        }
    }

    .dash-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        padding: 24px;
        display: flex;
        flex-direction: column;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }

    .dash-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05), 0 8px 10px -6px rgba(0, 0, 0, 0.01);
    }

    .card-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 16px;
    }

    .card-icon {
        width: 44px;
        height: 44px;
        border-radius: 10px;
        background: #f1f5f9;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #0f172a;
    }

    /* Subtle tinted backgrounds for icons */
    .icon-duct { background: #e0f2fe; color: #0369a1; }
    .icon-site { background: #dcfce7; color: #166534; }
    .icon-workshop { background: #fef3c7; color: #b45309; }
    .icon-admin { background: #f3f4f6; color: #374151; }

    .card-badge {
        font-size: 12px;
        font-weight: 600;
        padding: 4px 12px;
        border-radius: 9999px;
    }

    /* Badge variants */
    .badge-red { background: #fee2e2; color: #991b1b; }
    .badge-orange { background: #ffedd5; color: #9a3412; }
    .badge-green { background: #d1fae5; color: #065f46; }
    .badge-gray { background: #f1f5f9; color: #475569; }

    .card-title {
        font-family: 'Barlow', sans-serif;
        font-size: 20px;
        font-weight: 700;
        color: #0f172a;
        margin-bottom: 8px;
    }

    .card-desc {
        font-size: 14px;
        color: #64748b;
        line-height: 1.5;
        flex-grow: 1;
        margin-bottom: 24px;
        min-height: 42px;
    }

    .card-button {
        display: inline-flex;
        align-items: center;
        background: #0f172a;
        color: #ffffff;
        font-size: 13px;
        font-weight: 600;
        padding: 10px 16px;
        border-radius: 8px;
        text-decoration: none;
        transition: background 0.2s ease;
        align-self: flex-start;
    }

    .card-button:hover {
        background: #1e293b;
        color: #ffffff;
    }
</style>
@endpush

@section('content')
<div class="dashboard-wrapper">
    <div class="home-hero">
        <h1 class="home-title">Welcome to CE&P Hub</h1>
        <p class="home-subtitle">Manage your duct fabrication lifecycle — pick up right where you left off.</p>
    </div>

    <div class="dashboard-grid">
        
        @if(Auth::user()->hasRole('engineer') || Auth::user()->hasRole('super_admin'))
        <!-- Engineer / Duct Orders -->
        <div class="dash-card">
            <div class="card-header">
                <div class="card-icon icon-duct">
                    📦
                </div>
                @if(isset($stats['rejected_orders']) && $stats['rejected_orders'] > 0)
                    <span class="card-badge badge-red">{{ $stats['rejected_orders'] }} rejected</span>
                @else
                    <span class="card-badge badge-gray">All good</span>
                @endif
            </div>
            <h2 class="card-title">Duct Orders</h2>
            <p class="card-desc">Create requests, track approval status, and manage your site's fabrication list.</p>
            <a href="{{ route('engineer.orders.index') }}" class="card-button">
                Go to Orders &rarr;
            </a>
        </div>
        @endif

        @if(Auth::user()->hasRole('manager') || Auth::user()->hasRole('super_admin'))
        <!-- Manager / Manage Sites -->
        <div class="dash-card">
            <div class="card-header">
                <div class="card-icon icon-site">
                    🛡️
                </div>
                @if(isset($stats['awaiting_approval']) && $stats['awaiting_approval'] > 0)
                    <span class="card-badge badge-orange">{{ $stats['awaiting_approval'] }} awaiting you</span>
                @else
                    <span class="card-badge badge-gray">0 awaiting</span>
                @endif
            </div>
            <h2 class="card-title">Manage Sites</h2>
            <p class="card-desc">Review incoming orders, approve or reject fabrication requests by site.</p>
            <a href="{{ route('manager.orders.index') }}" class="card-button">
                Go to Approvals &rarr;
            </a>
        </div>
        @endif

        @if(Auth::user()->hasRole('workshop') || Auth::user()->hasRole('super_admin'))
        <!-- Workshop Card -->
        <div class="dash-card">
            <div class="card-header">
                <div class="card-icon icon-workshop">
                    ⚙️
                </div>
                <span class="card-badge badge-green">{{ $stats['workshop_queued'] ?? 0 }} queued</span>
            </div>
            <h2 class="card-title">Workshop</h2>
            <p class="card-desc">Access approved orders, generate cut lists, and update fabrication status.</p>
            <a href="{{ route('workshop.orders.index') }}" class="card-button">
                Go to Workshop &rarr;
            </a>
        </div>
        @endif

        @if(Auth::user()->hasRole('admin') || Auth::user()->hasRole('super_admin'))
        <!-- Admin Panel Card -->
        <div class="dash-card">
            <div class="card-header">
                <div class="card-icon icon-admin">
                    ⋮⋮
                </div>
                <span class="card-badge badge-gray">{{ $stats['total_users'] ?? 0 }} users</span>
            </div>
            <h2 class="card-title">Admin Panel</h2>
            <p class="card-desc">User roles, site assignments, and duct type catalog — master data control.</p>
            <a href="/admin" class="card-button">
                Go to Admin &rarr;
            </a>
        </div>
        @endif

    </div>
</div>
@endsection
