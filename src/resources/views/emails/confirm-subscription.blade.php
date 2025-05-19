<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Confirm Your Weather Subscription</title>
    <link rel="stylesheet" href="{{ asset('resources/css/emails/confirm-subscription.css') }}">
</head>
<body>
<div class="email-container">
    <div class="header">
        Confirm Your Weather Subscription
    </div>
    <div class="content">
        <p>Hello,</p>
        <p>Thank you for subscribing to <strong>{{ $subscription->getFrequency()->getName() }}</strong> weather updates for <strong>{{ $subscription->getCity()->getName() }}</strong>.</p>
        <p>To complete your subscription, please click the button below:</p>
        <div class="btn-container">
            <a href="{{ $confirmUrl }}" class="btn" target="_blank" rel="noopener noreferrer">Confirm Subscription</a>
        </div>
        <p>If you did not request this subscription, you can safely ignore this email.</p>
        <p style="color: #888; font-size: 14px; margin-top: 30px;">The confirmation link will expire in 24 hours.</p>
        <p>Thank you,<br>The Weather Updates Team</p>
    </div>
</div>
<div class="footer">
    This email was sent to <strong>{{ $subscription->getEmail()->getValue() }}</strong>
</div>
</body>
</html>
