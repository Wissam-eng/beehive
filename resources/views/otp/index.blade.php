<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OTP Verification</title>
</head>

<body>
    <h1>Verify Your OTP</h1>
    <form method="POST" action="{{ route('otp.check') }}">
        @csrf
        <input type="hidden" name="email" value="{{ session('email') }}">
        <label for="otp">Enter OTP:</label>
        <input type="text" id="otp" name="otp" required>
        <button type="submit">Verify</button>
    </form>
</body>

</html>
