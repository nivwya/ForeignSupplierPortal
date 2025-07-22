<!DOCTYPE html>
<html>
<head>
    <title>Your OTP Code</title>
</head>
<body>
    <p>Hello,</p>
    <p>Your OTP code is: <strong>{{ $otp }}</strong></p>
    <p>Please use this code to proceed with your password reset.</p>
    <br>
    <p>Thanks,<br>{{ config('app.name') }}</p>
</body>
</html>
