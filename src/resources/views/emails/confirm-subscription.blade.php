<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Confirm Your Weather Subscription</title>
    <style>
        body {
            background-color: #f4f6f8;
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            margin: 0;
            padding: 40px 10px;
            color: #333;
        }
        .email-container {
            max-width: 560px;
            background: #ffffff;
            margin: 0 auto;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            overflow: hidden;
            border: 1px solid #e2e8f0;
        }
        .header {
            background: #0077cc;
            padding: 30px 40px;
            color: white;
            text-align: center;
            font-size: 28px;
            font-weight: 700;
            letter-spacing: 0.04em;
        }
        .content {
            padding: 30px 40px 40px;
            font-size: 16px;
            line-height: 1.5;
            color: #444444;
        }
        .content p {
            margin-bottom: 22px;
        }
        .content p strong {
            color: #0077cc;
        }
        .btn-container {
            text-align: center;
            margin: 35px 0;
        }
        .btn {
            background-color: #0077cc;
            color: #fff !important;
            text-decoration: none;
            font-weight: 600;
            padding: 14px 40px;
            border-radius: 8px;
            font-size: 17px;
            display: inline-block;
            box-shadow: 0 5px 20px rgba(0, 119, 204, 0.3);
            transition: background-color 0.25s ease;
        }
        .btn:hover {
            background-color: #005fa3;
        }
        .footer {
            font-size: 12px;
            text-align: center;
            padding: 20px 40px 30px;
            color: #999999;
            border-top: 1px solid #e2e8f0;
            font-style: italic;
        }
        .footer strong {
            color: #666666;
        }
        @media (max-width: 600px) {
            .email-container {
                width: 100% !important;
                border-radius: 0;
                box-shadow: none;
            }
            .header, .content, .footer {
                padding-left: 20px;
                padding-right: 20px;
            }
        }
    </style>
</head>
<body>
<div class="email-container">
    <div class="header">
        Confirm Your Weather Subscription
    </div>
    <div class="content">
        <p>Hello,</p>
        <p>Thank you for subscribing to {{ $subscription->getFrequency()->getName() }} weather updates for <strong>{{ $subscription->getCity()->getName() }}</strong>.</p>
        <p>To complete your subscription, please click the button below:</p>
        <div class="btn-container">
            <a href="{{ $confirmUrl }}" class="btn" target="_blank" rel="noopener noreferrer">Confirm Subscription</a>
        </div>
        <p>If you did not request this subscription, you can safely ignore this email.</p>
        <p style="color: #888; font-size: 14px; margin-top: 30px;">The confirmation link will expire in 7 days.</p>
        <p>Thank you,<br>The Weather Updates Team</p>
    </div>
</div>
<div class="footer">
    This email was sent to <strong>{{ $subscription->getEmail()->getValue() }}</strong>
</div>
</body>
</html>
