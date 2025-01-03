<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Password Reset</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f9f9f9;
            margin: 0;
            padding: 0;
        }
        .email-container {
            max-width: 600px;
            margin: 20px auto;
            background-color: #ffffff;
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        .header {
            text-align: center;
            padding: 10px 0;
            border-bottom: 1px solid #e0e0e0;
        }
        .header h1 {
            color: #56409f;
            margin: 0;
            font-size: 24px;
        }
        .content {
            margin: 20px 0;
            line-height: 1.6;
        }
        .content p {
            margin: 10px 0;
        }
        .content .password {
            display: block;
            margin: 20px 0;
            font-size: 24px;
            color: #56409f;
            font-weight: bold;
            text-align: center;
        }
        .footer {
            text-align: center;
            margin-top: 20px;
            font-size: 12px;
            color: #6c757d;
            border-top: 1px solid #e0e0e0;
            padding-top: 10px;
        }
        .btn {
            display: inline-block;
            margin: 20px 0;
            padding: 10px 20px;
            background-color: #56409f;
            color: #ffffff;
            text-decoration: none;
            border-radius: 4px;
            font-weight: bold;
            text-align: center;
        }
        .btn:hover {
            background-color: #46358a;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="header">
            <h1>Password Reset</h1>
        </div>
        <div class="content">
            <p>Hi {{ $data['name'] }},</p>
            <p>Your password has been successfully reset. Here is your new password:</p>
            <h3 class="password">{{ $data['password'] }}</h3>
            <p>Please log in to your account and update your password to something more secure at your earliest convenience.</p>
            <a href="{{ route('user.index') }}" class="btn">Log In Now</a>
        </div>
        <div class="footer">
            <p>Thank you,<br>Admin i-Track</p>
        </div>
    </div>
</body>
</html>
