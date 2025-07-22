<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Foreign Supplier {{ old('mode', $mode ?? 'login') === 'register' ? 'Register' : 'Login' }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;700&display=swap');
        *, *::before, *::after { box-sizing: border-box; }
        html, body { margin: 0; padding: 0; height: 100vh; width: 100vw; font-family: 'Inter', sans-serif; background: linear-gradient(135deg, #b8f7b0 0%, #64e8a7 100%);}
        .center-bg { display: flex; align-items: center; justify-content: center; width: 100vw; height: 100vh; background: radial-gradient(circle at 10% 20%, #c3f6c3 0%,rgb(57, 213, 91) 100%);}
        .main-box { backdrop-filter: blur(25px); background: rgba(255, 255, 255, 0.7); border-radius: 24px; box-shadow: 0 8px 40px rgba(0, 0, 0, 0.1); display: flex; flex-direction: row; max-width: 950px; width: 100%; overflow: hidden; transition: all 0.3s ease-in-out;}
        .login-section { flex: 1; padding: 70px 60px; display: flex; flex-direction: column; justify-content: center;}
        .login-section h1 { font-size: 1.8rem; font-weight: 800; color: #0f1c2e; margin-bottom: 35px;}
        .form-container { display: flex; flex-direction: column; gap: 20px; max-width: 500px; width: 100%;}
        .form-container input { padding: 14px 12px; font-size: 1rem; border: 1.5px solid #ccc; border-radius: 10px; outline: none; background-color: #ffffffcc; transition: 0.2s;}
        .form-container input:focus { border-color: #1f78ff; background-color: #ffffff; box-shadow: 0 0 0 3px rgba(31, 120, 255, 0.2);}
        .form-container button { background: linear-gradient(to right, #4576b8, #5a8dee); color: white; font-size: 1rem; font-weight: bold; padding: 14px 0; border: none; border-radius: 10px; cursor: pointer; transition: transform 0.3s ease, box-shadow 0.2s;}
        .form-container button:hover { transform: translateY(-2px); box-shadow: 0 6px 20px rgba(90, 141, 238, 0.3);}
        .switch-links { display: flex; flex-direction: column; align-items: center; margin-top: 16px; gap: 8px; font-size: 0.95rem; color: #333;}
        .switch-links span { font-weight: 500;}
        .switch-links button { background-color: #ffffff; color: #4576b8; border: 2px solid #4576b8; border-radius: 8px; padding: 8px 16px; font-size: 0.95rem; font-weight: 600; cursor: pointer; transition: background 0.2s, color 0.2s;}
        .switch-links button:hover { background-color: #4576b8; color: white;}
        .logo-section { flex: 1; background: transparent; display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 40px 20px; transition: all 0.3s ease; text-align: center;}
        .logo-section img { width: 250px; max-width: 100%; margin-bottom: 25px; opacity: 0; transform: scale(0.95); animation: fadeInScale 2s ease-out forwards;}
        .logo-text { display: flex; flex-direction: column; align-items: center; justify-content: center; opacity: 0; transform: translateY(20px); animation: fadeInText 3s ease-out forwards; animation-delay: 1s; text-align: center; margin-top: 10px;}
        .logo-text .ar { font-size: 2.3rem; font-weight: bold; color: #222; margin-bottom: 6px;}
        .logo-text .en { font-size: 1.6rem; font-weight: 500; color: #444;}
        .error-message { color: #e53935; font-size: 1rem; margin-top: -8px; margin-bottom: 8px; text-align: left;}
        .success-message { color: #43a047; font-size: 1rem; margin-top: -8px; margin-bottom: 8px; text-align: left;}
        .switch-links .forgot-password {background: none; color: #4576b8; border: none; font-size: 0.95rem; cursor: pointer; text-decoration: underline; margin-bottom: 8px;}
        @keyframes fadeInScale { to { opacity: 1; transform: scale(1);} }
        @keyframes fadeInText { to { opacity: 1; transform: translateY(0);} }
        @media (max-width: 900px) {
            .main-box { flex-direction: column-reverse; max-height: none; width: 95%; margin: auto;}
            .login-section, .logo-section { padding: 30px 20px;}
            .form-container { width: 100%;}
        }
    </style>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
<div class="center-bg">
    <div class="main-box">
        <!-- Login/Register Section -->
        <div class="login-section">
            <h1>Foreign Supplier {{ old('mode', $mode ?? 'login') === 'login' ? 'Login' : 'Register' }}</h1>
            @if(session('error'))
                <div class="error-message">{{ session('error') }}</div>
            @endif
            @if(session('success'))
                <div class="success-message">{{ session('success') }}</div>
            @endif
            @if ($errors->any())
                <div class="error-message">
                    @foreach ($errors->all() as $error)
                        {{ $error }}<br>
                    @endforeach
                </div>
            @endif

            <form class="form-container" method="POST" action="{{ route('auth.handle') }}">
                @csrf
                <input type="hidden" name="mode" id="mode" value="{{ old('mode', $mode ?? 'login') }}">
                @if (old('mode', $mode ?? 'login') === 'login')
                <input 
                type="text" 
                name="id" 
                placeholder="User ID" 
                required autofocus />
                    <input
                        type="password"
                        name="password"
                        placeholder="Password"
                        required
                    />
                    <button type="submit">Login</button>
                    
                    <div class="switch-links">
                        <button type="button" class="forgot-password" onclick="handleForgotPassword()">Forgot Password?</button>
                        <span>Don’t have an account?</span>
                        <button type="button" onclick="document.getElementById('mode').value='register'; this.form.submit();">Register here</button>
                    </div>


                @else
                    <input
                        type="text"
                        name="fullname"
                        placeholder="Full Name"
                        value="{{ old('fullname') }}"
                        required
                        autofocus
                    />
                    <input
                        type="email"
                        name="email"
                        placeholder="Email Address"
                        value="{{ old('email') }}"
                        required
                    />
                    <input
                        type="password"
                        name="password"
                        placeholder="Password"
                        required
                    />
                    <input
                        type="password"
                        name="password_confirmation"
                        placeholder="Confirm Password"
                        required
                    />
                    <button type="submit">Register</button>
                    <div class="switch-links">
                        <span>Already have an account?</span>
                        <button type="button" onclick="document.getElementById('mode').value='login'; this.form.submit();">Login here</button>
                    </div>
                @endif
            </form>
        </div>
        <!-- Logo Section -->
        <div class="logo-section">
            <img src="{{ asset('almulla-logo-png.png') }}" alt="Al Mulla Group Logo" />
            <div class="logo-text">
                <span class="ar">مجموعة الملا</span>
                <span class="en">AL MULLA GROUP</span>
            </div>
        </div>
    </div>
</div>
