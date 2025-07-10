<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Housesync - Demo</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .container {
            text-align: center;
            background: white;
            padding: 60px 40px;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.1);
            max-width: 500px;
            width: 90%;
        }
        
        .brand {
            margin-bottom: 30px;
        }
        
        .brand-icon {
            width: 60px;
            height: 60px;
            background: linear-gradient(135deg, #4ecdc4 0%, #44a08d 100%);
            border-radius: 12px;
            margin: 0 auto 20px;
        }
        
        h1 {
            font-size: 48px;
            font-weight: 700;
            color: #2c3e50;
            margin-bottom: 16px;
        }
        
        .subtitle {
            font-size: 18px;
            color: #7f8c8d;
            margin-bottom: 40px;
            line-height: 1.6;
        }
        
        .nav-buttons {
            display: flex;
            gap: 15px;
            justify-content: center;
            flex-wrap: wrap;
        }
        
        .nav-btn {
            display: inline-block;
            padding: 16px 32px;
            border-radius: 12px;
            text-decoration: none;
            font-weight: 600;
            font-size: 16px;
            transition: all 0.3s ease;
            min-width: 140px;
        }
        
        .login-btn {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }
        
        .register-btn {
            background: white;
            color: #667eea;
            border: 2px solid #667eea;
        }
        
        .dashboard-btn {
            background: linear-gradient(135deg, #4ecdc4 0%, #44a08d 100%);
            color: white;
        }
        
        .units-btn {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            color: white;
        }
        
        .nav-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
        }
        
        .login-btn:hover {
            box-shadow: 0 8px 25px rgba(102, 126, 234, 0.4);
        }
        
        @media (max-width: 680px) {
            .nav-buttons {
                flex-direction: column;
                align-items: center;
            }
            
            .nav-btn {
                width: 100%;
                max-width: 200px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="brand">
            <div class="brand-icon"></div>
        </div>
        
        <h1>Housesync</h1>
        <p class="subtitle">Welcome to the demo. Choose an option below to view the static authentication pages.</p>
        
        <div class="nav-buttons">
            <a href="{{ route('login') }}" class="nav-btn login-btn">Login Page</a>
            <a href="{{ route('register') }}" class="nav-btn register-btn">Register Page</a>
            <a href="{{ route('dashboard') }}" class="nav-btn dashboard-btn">Dashboard Demo</a>
            <a href="{{ route('units') }}" class="nav-btn units-btn">Units Management</a>
        </div>
    </div>
</body>
</html> 