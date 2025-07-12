<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Housesync</title>
    <link rel="stylesheet" href="{{ asset('css/auth.css') }}">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <!-- Additional styles for tenant login button -->
    <style>
        .demo-section {
            margin: 20px 0;
        }
        
        .divider {
            text-align: center;
            margin: 20px 0;
            position: relative;
        }
        
        .divider::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 0;
            right: 0;
            height: 1px;
            background: #e2e8f0;
        }
        
        .divider span {
            background: white;
            padding: 0 15px;
            color: #64748b;
            font-size: 14px;
            font-weight: 500;
        }
        
        .tenant-login-btn {
            width: 100%;
            padding: 14px 20px;
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
        }
        
        .tenant-login-btn:hover {
            background: linear-gradient(135deg, #059669 0%, #047857 100%);
            box-shadow: 0 6px 20px rgba(16, 185, 129, 0.4);
            transform: translateY(-2px);
        }
        
        .tenant-login-btn:active {
            transform: translateY(0);
            box-shadow: 0 2px 8px rgba(16, 185, 129, 0.3);
        }
        
        .tenant-login-btn i {
            font-size: 18px;
        }
    </style>
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
                    
                    <form class="auth-form" onsubmit="redirectToDashboard(event)">
                        <div class="form-group">
                            <label for="email">Email*</label>
                            <input type="email" id="email" name="email" placeholder="Enter your email">
                        </div>
                        
                        <div class="form-group">
                            <label for="password">Password*</label>
                            <input type="password" id="password" name="password" placeholder="minimum 8 characters">
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
                    
                    <!-- Demo/Testing Button for Tenant Login -->
                    <div class="demo-section">
                        <div class="divider">
                            <span>Demo Access</span>
                        </div>
                        <button type="button" class="tenant-login-btn" onclick="redirectToTenantDashboard()">
                            <i class="fas fa-user"></i>
                            Login as a Tenant
                        </button>
                    </div>
                    
                    <p class="auth-switch">Not registered yet? <a href="{{ route('register') }}">Create a new account</a></p>
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
        function redirectToDashboard(event) {
            event.preventDefault(); // Prevent form submission
            // Add a small delay to show the button press effect
            setTimeout(function() {
                window.location.href = "{{ route('dashboard') }}";
            }, 200);
        }

        function redirectToTenantDashboard() {
            window.location.href = "{{ route('tenant.dashboard') }}";
        }
    </script>
</body>
</html> 