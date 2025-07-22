<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Foreign Supplier {{ old('mode', $mode ?? 'login') === 'register' ? 'Register' : 'Login' }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;700&display=swap');
        *, *::before, *::after { box-sizing: border-box; }
        html, body {
            margin: 0;
            padding: 0;
            height: 100vh;
            width: 100vw;
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #318c38 2%,rgb(13, 115, 43) 100%);
            min-height: 100vh;
            min-width: 100vw;
        }
        body::before {
            content: "";
            position: absolute;
            left: 0; top: 0; width: 100vw; height: 100vh;
            background: url('data:image/svg+xml;utf8,<svg width="100%" height="100%" viewBox="0 0 1440 900" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M0 800 Q720 1000 1440 800 L1440 900 L0 900 Z" fill="%2335b36e" fill-opacity="0.12"/><path d="M0 600 Q720 850 1440 600 L1440 900 L0 900 Z" fill="%23fff" fill-opacity="0.07"/></svg>');
            background-repeat: no-repeat;
            background-size: cover;
            z-index: 0;
        }
        .center-bg {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 100vw;
            height: 100vh;
            position: relative;
            z-index: 1;
        }
        .main-box {
            background: #fff;
            border-radius: 18px;
            box-shadow: 0 6px 32px rgba(44, 62, 80, 0.13);
            padding: 44px 36px 32px 36px;
            width: 100%;
            max-width: 400px;
            display: flex;
            flex-direction: column;
            align-items: center;
            animation: fadeInScale 0.8s cubic-bezier(.4,0,.2,1);
        }
        .logo-section {
            width: 100%;
            display: flex;
            flex-direction: row;
            align-items: center;
            justify-content: center;
            margin-bottom: 24px;
            gap: 18px;
        }
        .logo-section img {
            width: 300px;
            min-width: 62px;
            margin-bottom: 0;
            filter: drop-shadow(0 2px 8px rgba(52, 199, 89, 0.13));
        }
        .logo-text {
            display: flex;
            flex-direction: column;
            justify-content: center;
            gap: 2px;
            text-align: left;
        }
        .logo-text .ar {
            font-size: 1.22rem;
            font-weight: 700;
            color:rgb(0, 0, 0);
            line-height: 1.1;
            letter-spacing: 0.5px;
        }
        .logo-text .en {
            font-size: 1rem;
            font-weight: 500;
            color:rgb(0, 0, 0);
            letter-spacing: 0.0001px;
            line-height: 1.1;
        }
        .login-section {
            width: 100%;
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        .login-section h1 {
            font-size: 1.2rem;
            font-weight: 700;
            color: #222;
            margin-bottom: 18px;
            text-align: center;
            letter-spacing: 0.5px;
        }
        .form-container {
            width: 100%;
            display: flex;
            flex-direction: column;
            gap: 14px;
        }
        .form-container input {
            padding: 12px 12px;
            font-size: 1rem;
            border: 1.5px solid #d2e8d8;
            border-radius: 8px;
            outline: none;
            background: #f7fef9;
            transition: border 0.2s, box-shadow 0.2s;
        }
        .form-container input:focus {
            border-color: #1fae4b;
            box-shadow: 0 0 0 2px rgba(31, 174, 75, 0.13);
            background: #fff;
        }
        .form-container button[type="submit"] {
            background: #40c463;
            color: #fff;
            font-size: 1.08rem;
            font-weight: 700;
            padding: 12px 0;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            margin-top: 6px;
            transition: background 0.2s, transform 0.2s, box-shadow 0.2s;
            box-shadow: 0 2px 8px rgba(52, 199, 89, 0.13);
        }
        .form-container button[type="submit"]:hover {
            background: #1fae4b;
            transform: translateY(-1px) scale(1.01);
        }
        .switch-links {
            display: flex;
            flex-direction: column;
            align-items: center;
            margin-top: 6px;
            gap: 5px;
            font-size: 0.95rem;
            color: #555;
        }
        .switch-links span {
            font-weight: 500;
        }
        .switch-links button {
            background: #fff;
            color: #1fae4b;
            border: 1.5px solid #1fae4b;
            border-radius: 7px;
            padding: 7px 16px;
            font-size: 0.97rem;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.2s, color 0.2s, border 0.2s;
        }
        .switch-links button:hover {
            background: #1fae4b;
            color: #fff;
            border-color: #17983c;
        }
        .error-message {
            color: #e53935;
            font-size: 1rem;
            margin-bottom: 8px;
            text-align: left;
            width: 100%;
        }
        .success-message {
            color: #43a047;
            font-size: 1rem;
            margin-bottom: 8px;
            text-align: left;
            width: 100%;
        }
        @keyframes fadeInScale { from { opacity: 0; transform: scale(0.96);} to { opacity: 1; transform: scale(1);} }
        @media (max-width: 500px) {
            .main-box {
                padding: 18px 5vw 18px 5vw;
                max-width: 98vw;
            }
            .logo-section img { width: 48px; }
            .logo-text .ar { font-size: 1rem; }
            .logo-text .en { font-size: 0.95rem; }
        }
         /*changes made by niveditha*/
    .swal2-popup {
      background-color: #fff !important;
      border-radius: 16px !important;
      border: 1.5px solid #d2e8d8 !important;
      font-family: 'Inter', sans-serif !important;
      box-shadow: 0 6px 32px rgba(44, 62, 80, 0.13) !important;
    }
    .swal2-title {
      font-size: 1.2rem !important;
      font-weight: 700 !important;
      color: #222 !important;
    }
    .swal2-html-container {
      font-size: 0.98rem !important;
      color: #444 !important;
    }
    .swal2-input {
      border: 1.5px solid #d2e8d8 !important;
      border-radius: 8px !important;
      background: #f7fef9 !important;
      font-size: 1rem !important;
      padding: 10px !important;
    }
    .swal2-input:focus {
      border-color: #1fae4b !important;
      box-shadow: 0 0 0 2px rgba(31, 174, 75, 0.13) !important;
      background: #fff !important;
    }
    .swal2-confirm {
      background-color: #40c463 !important;
      color: #fff !important;
      font-weight: 700 !important;
      border-radius: 8px !important;
      padding: 10px 20px !important;
      box-shadow: 0 2px 8px rgba(52, 199, 89, 0.13) !important;
    }
    .swal2-confirm:hover {
      background-color: #1fae4b !important;
    }
    .swal2-cancel {
      background-color: #fff !important;
      color: #1fae4b !important;
      border: 1.5px solid #1fae4b !important;
      border-radius: 8px !important;
      font-weight: 600 !important;
      padding: 8px 18px !important;
    }
    .swal2-cancel:hover {
      background-color: #1fae4b !important;
      color: #fff !important;
    }
    .swal2-validation-message {
      color: #e53935 !important;
      font-size: 0.95rem !important;
    }
    /*changes end*/
    </style>
</head>
<body>
<div class="center-bg">
    <div class="main-box">
        <!-- Logo and Text Side by Side -->
        <div class="logo-section">
            <!--changes made by niveditha-->
            <img style="width: 100px; height: auto;" src="{{ asset('almulla-logo-small.png') }}" alt="Al Mulla Group Logo" />
            <!--changes end-->
        </div>
        <!-- Login/Register Section -->
        <div class="login-section">
            <h1>Foreign Supplier {{ old('mode', $mode ?? 'login') === 'login' ? 'Login' : 'Register' }}</h1>
            @if(session('error'))
                <div class="error-message">{{ session('error') }}</div>
            @endif
            @if ($errors->any())
                <div class="error-message">
                    @foreach ($errors->all() as $error)
                        {{ $error }}<br>
                    @endforeach
                </div>
            @endif
<!-- CHANGES MADE BY NIVEDITHA-->
            <form class="form-container" method="POST" action="{{ route('auth.handle') }}">
                @csrf
                <input type="hidden" name="mode" id="mode" value="{{ old('mode', $mode ?? 'login') }}">
                @if (old('mode', $mode ?? 'login') === 'login')
                    <input
                        type="text"
                        name="id"
                        maxlength="20"
                        placeholder="User ID"
                        value="{{ old('id') }}"
                        required
                        autofocus
                    />
                    <input
                        type="password"
                        name="password"
                        placeholder="Password"
                        required
                    />
                    <button type="submit">
                        Login
                    </button>
                    <div class="switch-links">
                        <span>Don't have an account?</span>
                        <button type="button" onclick="document.getElementById('mode').value='register'; this.form.submit();">Register here</button>
                        <button type="button" onclick="handleForgotPassword()">Forgot Password?</button>
                    </div>
                    <!-- CHANGES END-->
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
                    <button type="submit">
                        Register
                    </button>
                    <div class="switch-links">
                        <span>Already have an account?</span>
                        <button type="button" onclick="document.getElementById('mode').value='login'; this.form.submit();">Login here</button>
                    </div>
                @endif
            </form>
        </div>
    </div>
</div>

<!-- CHANGES MADE BY NIVEDITHA-->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script>
function handleForgotPassword() {
    Swal.fire({
        title: "Forgot Password",
        input: "text",
        inputLabel: "Enter your User ID",
        inputPlaceholder: "e.g. 0000900010",
        showCancelButton: true,
        confirmButtonText: "Send OTP",
        preConfirm: (vendorId) => {
            if (!vendorId) {
                Swal.showValidationMessage("⚠️ Please enter your User ID");
                return false;
            }
            // Call the backend to get user email and send OTP
            return fetch("{{ route('password.sendOtp') }}", {
                method: "POST",
                headers: { 
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": "{{ csrf_token() }}" 
                },
                body: JSON.stringify({ user_id: vendorId })
            })
            .then(response => response.json())
            .then(data => {
                if (!data.success) throw new Error(data.message || "User ID not found");
                return data.email;  // use the email returned by backend
            })
            .catch(error => {
                Swal.showValidationMessage(`🚫 ${error.message}`);
                return false;
            });
        }
    }).then(result => {
        if (result.isConfirmed && result.value) {
            // result.value is the email fetched from user ID
            askForOtp(result.value);
        }
    });
}


function askForOtp(email) {
    let timerInterval;
    let secondsLeft = 180; // 3 minutes

    Swal.fire({
        title: "Verify OTP",
        html: `
            <div style="margin-bottom:6px;">Enter the OTP sent to your email</div>
            <input type="text" id="otpInput" class="swal2-input" placeholder="e.g. 123456">
            <div id="timer" style="font-size:0.9rem;color:#e53935;margin-top:6px;">Expires in 3:00</div>
        `,
        showCancelButton: true,
        confirmButtonText: "Verify OTP",
        focusConfirm: false,
        didOpen: () => {
            const timerElem = Swal.getPopup().querySelector("#timer");
            timerInterval = setInterval(() => {
                secondsLeft--;
                const m = Math.floor(secondsLeft / 60);
                const s = secondsLeft % 60;
                timerElem.textContent = `Expires in ${m}:${s < 10 ? '0' : ''}${s}`;
                if (secondsLeft <= 0) {
                    clearInterval(timerInterval);
                    timerElem.textContent = "OTP expired. Please try again.";
                    Swal.getConfirmButton().disabled = true;
                }
            }, 1000);
        },
        preConfirm: () => {
            const otp = Swal.getPopup().querySelector("#otpInput").value;
            if (!otp) {
                Swal.showValidationMessage("⚠️ Please enter the OTP");
                return false;
            }
            return fetch("{{ route('password.verifyOtp') }}", {
                method: "POST",
                headers: { 
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": "{{ csrf_token() }}" 
                },
                body: JSON.stringify({ email: email, otp: otp })
            })
            .then(response => response.json())
            .then(data => {
                if (!data.success) throw new Error(data.message || "Invalid OTP");
                return true;
            })
            .catch(error => {
                Swal.showValidationMessage(`🚫 ${error.message}`);
                return false;
            });
        },
        willClose: () => {
            clearInterval(timerInterval);
        }
    }).then(result => {
        if (result.isConfirmed) {
            askForNewPassword(email);
        }
    });
}

function askForNewPassword(email) {
    Swal.fire({
        title: "Set New Password",
        html: `
            <input type="password" id="newPassword" class="swal2-input" placeholder="New Password">
            <input type="password" id="confirmPassword" class="swal2-input" placeholder="Confirm Password">
        `,
        focusConfirm: false,
        showCancelButton: true,
        confirmButtonText: "Change Password",
        preConfirm: () => {
            const newPassword = Swal.getPopup().querySelector("#newPassword").value;
            const confirmPassword = Swal.getPopup().querySelector("#confirmPassword").value;
            if (!newPassword || !confirmPassword) {
                Swal.showValidationMessage("⚠️ Both fields are required");
                return false;
            }
            if (newPassword !== confirmPassword) {
                Swal.showValidationMessage("🚫 Passwords do not match");
                return false;
            }
            const regex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?#&])[A-Za-z\d@$!%*?#&]{8,}$/;
            if (!regex.test(newPassword)) {
                Swal.showValidationMessage("⚠️ Password must be 8+ chars, upper, lower, number, symbol");
                return false;
            }
            return fetch("{{ route('password.reset') }}", {
                method: "POST",
                headers: { "Content-Type": "application/json", "X-CSRF-TOKEN": "{{ csrf_token() }}" },
                body: JSON.stringify({ email: email, password: newPassword })
            })
            .then(response => response.json())
            .then(data => {
                if (!data.success) throw new Error(data.message || "Error resetting password");
                return true;
            })
            .catch(error => {
                Swal.showValidationMessage(`🚫 ${error.message}`);
                return false;
            });
        }
    }).then(result => {
        if (result.isConfirmed) {
            Swal.fire({
                icon: "success",
                title: "Password Changed",
                text: "You can now log in with your new password",
                confirmButtonColor: "#16a34a"
            });
        }
    });
}

</script>
<!-- CHANGES END-->
</body>
</html>
