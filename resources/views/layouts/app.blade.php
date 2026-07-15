<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CE&P — Duct Fabrication</title>
    
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Barlow+Condensed:wght@400;600;700;800&family=Barlow:wght@300;400;500;600&display=swap" rel="stylesheet">
    
    <!-- Our main CSS -->
    <link rel="stylesheet" href="{{ asset('style.css') }}">
    
    <!-- Three.js for viewer -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/three.js/r128/three.min.js"></script>
    
    <style>
        .navbar {
            display: flex;
            background: #fff;
            padding: 10px 28px;
            border-bottom: 1px solid #dde3f0;
            justify-content: space-between;
            align-items: center;
        }
        .nav-links a {
            margin-right: 20px;
            color: #0d1a3a;
            text-decoration: none;
            font-weight: 500;
        }
        .nav-links a:hover {
            color: var(--navy);
        }
        .alert {
            padding: 15px 28px;
            margin-bottom: 0;
            color: #fff;
            font-weight: 500;
        }
        .alert-success { background-color: var(--accent); }
        .alert-error { background-color: var(--red); }
        
        .container {
            padding: 24px 28px;
        }
        
        /* Table Styles for general list pages */
        .data-table {
            width: 100%;
            border-collapse: collapse;
            background: #fff;
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
            border-radius: 8px;
            overflow: hidden;
        }
        .data-table th, .data-table td {
            padding: 12px 16px;
            text-align: left;
            border-bottom: 1px solid #dde3f0;
        }
        .data-table th {
            background: var(--navy);
            color: #fff;
            font-weight: 600;
        }
        .data-table tr:last-child td {
            border-bottom: none;
        }
        .badge {
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
        }
        .badge-draft { background: #e2e8f0; color: #475569; }
        .badge-submitted { background: #dbeafe; color: #1e40af; }
        .badge-approved { background: #dcfce7; color: #166534; }
        .badge-rejected { background: #fee2e2; color: #991b1b; }
        .badge-fabricating { background: #fef3c7; color: #92400e; }
        .badge-ready { background: #e0e7ff; color: #3730a3; }
        .badge-delivered { background: #ccfbf1; color: #115e59; }
    </style>
    
    @stack('styles')
</head>
<body>

  <!-- HEADER -->
  <div class="header">
    <div class="header-inner">
      <div class="logo-area">
        <div class="logo-badge">
          <span class="logo-ce">CE</span>
          <span class="logo-amp">&amp;</span>
          <span class="logo-p">P</span>
        </div>
        <div class="logo-divider"></div>
        <div class="logo-text">
          <h1>Corporation</h1>
          <p>optimize your investment</p>
        </div>
      </div>
      <div class="header-actions" style="display:flex; align-items:center; gap:16px;">
        @auth
            <span style="color:white; font-size:14px;">Welcome, {{ Auth::user()->name }} ({{ Auth::user()->roles->first()->name ?? 'No Role' }})</span>
            <form action="{{ route('logout') }}" method="POST" style="display:inline;">
                @csrf
                <button type="submit" class="btn-hdr btn-hdr-outline">Logout</button>
            </form>
        @endauth
      </div>
    </div>
  </div>

  <!-- NAVBAR -->
  <div class="navbar">
      <div class="nav-links">
          @if(Auth::user()->hasRole('engineer'))
            <a href="{{ route('engineer.orders.index') }}">My Orders</a>
            <a href="{{ route('engineer.orders.create') }}">New Order</a>
          @endif
          @if(Auth::user()->hasRole('manager'))
            <a href="{{ route('manager.orders.index') }}">Site Orders</a>
          @endif
          @if(Auth::user()->hasRole('workshop'))
            <a href="{{ route('workshop.orders.index') }}">Workshop Queue</a>
          @endif
      </div>
  </div>

  @if(session('success'))
      <div class="alert alert-success">
          {{ session('success') }}
      </div>
  @endif

  @if(session('error'))
      <div class="alert alert-error">
          {{ session('error') }}
      </div>
  @endif

  <!-- MAIN CONTENT -->
  @yield('content')

  @stack('scripts')
</body>
</html>
