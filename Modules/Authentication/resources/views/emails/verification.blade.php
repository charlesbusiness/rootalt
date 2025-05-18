<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Email Verification</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f8f9fa;
            margin: 0;
            padding: 0;
        }
        .email-container {
            max-width: 600px;
            margin: 20px auto;
            background: #ffffff;
            border: 1px solid #e3e3e3;
            border-radius: 5px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }
        .email-header {
            background-color: #007bff;
            color: #ffffff;
            padding: 20px;
            text-align: center;
        }
        .email-body {
            padding: 20px;
            color: #333333;
            line-height: 1.6;
        }
        .email-body h1 {
            font-size: 24px;
        }
        .email-body p {
            margin: 10px 0;
        }
        .email-button {
            display: inline-block;
            padding: 10px 20px;
            color: #ffffff;
            background-color: #007bff;
            text-decoration: none;
            border-radius: 5px;
            margin: 20px 0;
        }
        .email-footer {
            background-color: #f1f1f1;
            color: #555555;
            text-align: center;
            padding: 10px;
            font-size: 12px;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <!-- Header Section -->
        <div class="email-header">
            <h1>Email Verification</h1>
        </div>
        
        <!-- Body Section -->
        <div class="email-body">
            <h1>Welcome to {{ config('app.name') }}!</h1>
            <p>Hi {{$data['name']}} ,</p>
            <p>Thank you for registering with us. Please verify your email address to complete your registration and access all features.</p>
            <p>
                Your verification code is <b>{{$data['verificationCode']}}</b>
            </p>
            <p>If you didn’t create an account, please ignore this email.</p>
        </div>
        
        <!-- Footer Section -->
        <div class="email-footer">
            <p>© {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
