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
    <link rel="stylesheet" href="{{ asset('style.css') }}?v={{ filemtime(public_path('style.css')) }}">
    
    <!-- Three.js for viewer -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/three.js/r128/three.min.js"></script>
    
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        function confirmSubmit(event, message, confirmText = 'Yes, proceed!') {
            event.preventDefault();
            const form = event.target.closest('form');
            Swal.fire({
                title: 'Are you sure?',
                text: message,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#1B3F8B',
                cancelButtonColor: '#D72B2B',
                confirmButtonText: confirmText,
                customClass: {
                    popup: 'swal2-custom-popup'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        }
    </script>
    
    <style>
        .navbar {
            display: flex;
            background: #fff;
            padding: 12px 28px;
            border-bottom: 1px solid #e2e8f0;
            align-items: center;
        }
        .nav-links {
            display: flex;
            gap: 8px;
        }
        .nav-links a {
            color: #475569;
            text-decoration: none;
            font-weight: 500;
            font-size: 14px;
            padding: 8px 16px;
            border-radius: 999px;
            transition: all 0.2s ease;
        }
        .nav-links a:hover {
            background: #f1f5f9;
            color: #0f172a;
        }
        .nav-links a.active {
            background: #0f172a;
            color: #ffffff;
            font-weight: 600;
            box-shadow: 0 2px 4px rgba(15, 23, 42, 0.1);
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
      <a href="/" class="logo-area" style="text-decoration: none; color: inherit;">
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
      </a>
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

  @if(!request()->is('/'))
  <!-- NAVBAR -->
  <div class="navbar">
      <div class="nav-links">
          @if(Auth::check())
              @if(Auth::user()->hasRole('engineer') || Auth::user()->hasRole('super_admin'))
                <a href="{{ route('engineer.orders.index') }}" class="{{ request()->routeIs('engineer.orders.index', 'engineer.orders.show', 'engineer.orders.edit') ? 'active' : '' }}">My Orders</a>
                <a href="{{ route('engineer.orders.create') }}" class="{{ request()->routeIs('engineer.orders.create') ? 'active' : '' }}">New Order</a>
              @endif
              @if(Auth::user()->hasRole('manager') || Auth::user()->hasRole('super_admin'))
                <a href="{{ route('manager.orders.index') }}" class="{{ request()->routeIs('manager.orders.*') ? 'active' : '' }}">Site Orders</a>
              @endif
              @if(Auth::user()->hasRole('workshop') || Auth::user()->hasRole('super_admin'))
                <a href="{{ route('workshop.orders.index') }}" class="{{ request()->routeIs('workshop.orders.*') ? 'active' : '' }}">Workshop Queue</a>
              @endif
          @endif
      </div>
  </div>
  @endif

  @if(session('success'))
      <script>
          document.addEventListener('DOMContentLoaded', function() {
              Swal.fire({
                  toast: true,
                  position: 'top-end',
                  icon: 'success',
                  title: "{{ session('success') }}",
                  showConfirmButton: false,
                  timer: 3000,
                  timerProgressBar: true
              });
          });
      </script>
  @endif

  @if(session('error'))
      <script>
          document.addEventListener('DOMContentLoaded', function() {
              Swal.fire({
                  toast: true,
                  position: 'top-end',
                  icon: 'error',
                  title: "{{ session('error') }}",
                  showConfirmButton: false,
                  timer: 4000,
                  timerProgressBar: true
              });
          });
      </script>
  @endif

  <!-- MAIN CONTENT -->
  @yield('content')

  @stack('scripts')
</body>
</html>
