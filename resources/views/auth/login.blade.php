<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CE&P — Login</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Barlow+Condensed:wght@400;600;700;800&family=Barlow:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('style.css') }}">
    <style>
        body {
            background-color: #f4f6fb;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
            margin: 0;
        }
        .login-card {
            background: #fff;
            padding: 40px;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            width: 100%;
            max-width: 400px;
        }
        .logo-area {
            justify-content: center;
            margin-bottom: 30px;
        }
        .alert-error {
            background-color: #fee2e2;
            color: #991b1b;
            padding: 10px;
            border-radius: 4px;
            margin-bottom: 20px;
            font-size: 14px;
        }
    </style>
</head>
<body>

    <div class="login-card">
        <div class="logo-area" style="display:flex; align-items:center;">
            <div class="logo-badge">
                <span class="logo-ce">CE</span>
                <span class="logo-amp">&amp;</span>
                <span class="logo-p">P</span>
            </div>
            <div class="logo-divider"></div>
            <div class="logo-text">
                <h1 style="color:#0d2050">Corporation</h1>
                <p style="color:#8a97b8">optimize your investment</p>
            </div>
        </div>

        <h2 style="font-family:'Barlow Condensed', sans-serif; font-size:24px; color:#1B3F8B; text-align:center; margin-bottom: 20px;">Duct Fabrication Portal</h2>

        @if($errors->any())
            <div class="alert-error">
                <ul style="margin:0; padding-left:20px;">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('login') }}" method="POST">
            @csrf
            
            <div class="field-group full" style="margin-bottom: 20px;">
                <label class="field-label">Email Address</label>
                <input type="email" name="email" class="field-input" required value="{{ old('email') }}" autofocus>
            </div>
            
            <div class="field-group full" style="margin-bottom: 20px;">
                <label class="field-label">Password</label>
                <input type="password" name="password" class="field-input" required>
            </div>
            
            <div style="margin-bottom: 20px;">
                <label style="display:flex; align-items:center; gap:8px; font-size:14px; color:#475569;">
                    <input type="checkbox" name="remember"> Remember me
                </label>
            </div>
            
            <button type="submit" class="btn btn-primary" style="width:100%; justify-content:center;">Login</button>
        </form>
    </div>

</body>
</html>
