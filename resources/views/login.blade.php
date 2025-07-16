<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Housesync</title>
    <link rel="stylesheet" href="{{ asset('css/auth.css') }}">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    

</head>
<body>
    <div class="container">
        <div class="auth-wrapper">
            <!-- Left side - Form -->
            <div class="form-section">
                <div class="form-container">
                    <div class="brand">
                        <div class="brand-icon"></div>
                    </div>
                    
                    <h1 class="title">Login</h1>
                    <p class="subtitle">See your growth and get support!</p>
                    
                    <button class="google-btn">
                        <svg width="18" height="18" viewBox="0 0 18 18">
                            <path fill="#4285F4" d="M16.51 8H8.98v3h4.3c-.18 1-.74 1.48-1.6 2.04v2.01h2.6a7.8 7.8 0 0 0 2.38-5.88c0-.57-.05-.66-.15-1.18z"/>
                            <path fill="#34A853" d="M8.98 16c2.16 0 3.97-.72 5.3-1.94l-2.6-2.04a4.8 4.8 0 0 1-7.18-2.53H1.83v2.07A8 8 0 0 0 8.98 16z"/>
                            <path fill="#FBBC05" d="M4.5 9.49a4.8 4.8 0 0 1 0-3.07V4.35H1.83a8 8 0 0 0 0 7.17l2.67-2.03z"/>
                            <path fill="#EA4335" d="M8.98 3.2c1.17 0 2.23.4 3.06 1.2l2.3-2.3A8 8 0 0 0 1.83 4.35L4.5 6.42a4.77 4.77 0 0 1 4.48-3.22z"/>
                        </svg>
                        Sign in with google
                    </button>
                    
                    @if($errors->any())
                        <div class="alert alert-error">
                            <ul>
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form class="auth-form" method="POST" action="{{ route('login.post') }}">
                        @csrf
                        <div class="form-group">
                            <label for="email">Email*</label>
                            <input type="email" id="email" name="email" value="{{ old('email') }}" placeholder="Enter your email" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="password">Password*</label>
                            <input type="password" id="password" name="password" placeholder="minimum 8 characters" required>
                        </div>
                        
                        <div class="form-options">
                            <div class="remember-me">
                                <input type="checkbox" id="remember" name="remember">
                                <label for="remember">Remember me</label>
                            </div>
                            <a href="#" class="forgot-password">Forgot password?</a>
                        </div>
                        
                        <button type="submit" class="submit-btn">Login</button>
                    </form>
                    
                    <p class="auth-switch">Not registered yet? <a href="{{ route('register') }}">Create a new account</a></p>
                    <p class="auth-switch">Are you a property owner? <a href="{{ route('landlord.register') }}">Register as Landlord</a></p>
                </div>
            </div>
            
            <!-- Right side - Illustration -->
            <div class="illustration-section">
                <div class="illustration-container">
                    <!-- Isometric illustration elements -->
                    <div class="iso-platform platform-1">
                        <div class="person person-1"></div>
                        <div class="chart chart-1"></div>
                    </div>
                    <div class="iso-platform platform-2">
                        <div class="person person-2"></div>
                        <div class="chart chart-2"></div>
                    </div>
                    <div class="iso-platform platform-3">
                        <div class="person person-3"></div>
                        <div class="device device-1"></div>
                    </div>
                    <div class="iso-platform platform-4">
                        <div class="person person-4"></div>
                        <div class="chart chart-3"></div>
                    </div>
                    <div class="iso-platform platform-5">
                        <div class="person person-5"></div>
                        <div class="chart chart-4"></div>
                    </div>
                    <div class="floating-elements">
                        <div class="cube cube-1"></div>
                        <div class="cube cube-2"></div>
                        <div class="cube cube-3"></div>
                        <div class="cube cube-4"></div>
                        <div class="cube cube-5"></div>
                        <div class="cube cube-6"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Login form is now handled by Laravel backend
    </script>
    
    <style>
        .alert {
            padding: 12px;
            margin-bottom: 20px;
            border-radius: 8px;
            font-size: 14px;
        }
        
        .alert-error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        
        .alert ul {
            margin: 0;
            padding-left: 20px;
        }
    </style>
    
    <!-- Firebase App Check Scripts -->
    @include('partials.firebase-scripts')
</body>
</html> 