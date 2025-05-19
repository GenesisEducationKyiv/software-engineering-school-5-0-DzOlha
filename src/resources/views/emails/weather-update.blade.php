<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Weather Update for {{ $city }}</title>
    <link rel="stylesheet" href="{{ asset('resources/css/emails/weather-updates.css') }}">
</head>
<body>
<div class="email-container">
    <div class="header">
        Current Weather Update for {{ $city }}
    </div>
    <div class="content">
        <p>Hello,</p>
        <p>This is your <strong>{{ strtolower($frequency) }}</strong> weather update for <strong>{{ $city }}</strong>:</p>

        <div class="weather-box">
            <h2>Current Conditions</h2>
            <p class="temperature">{{ $temperature }}°C</p>
            <p class="weather-detail"><strong>Description:</strong> {{ $description }}</p>
            <p class="weather-detail"><strong>Humidity:</strong> {{ $humidity }}%</p>
        </div>

        <p>Thank you,<br>The Weather Updates Team</p>
    </div>
</div>
<div class="footer">
    Subscription frequency: <strong>{{ $frequency }}</strong>
</div>
<div class="unsubscribe">
    <a href="{{ $unsubscribeUrl }}" target="_blank" rel="noopener noreferrer">Unsubscribe from these emails</a>
</div>
</body>
</html>
