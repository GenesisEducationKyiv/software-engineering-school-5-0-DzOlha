@php use App\Application\Subscription\Emails\Mails\Update\WeatherUpdateMailData; @endphp
@php
    /** @var WeatherUpdateMailData $data */
@endphp

    <!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1"/>
    <title>Weather Update for {{ $data->getCity() }}</title>
    <link rel="stylesheet" href="{{ asset('resources/css/emails/weather-updates.css') }}">
</head>
<body>
<div class="email-container">
    <div class="header">
        Current Weather Update for {{ $data->getCity() }}
    </div>
    <div class="content">
        <p>Hello,</p>
        <p>This is your <strong>{{ $data->getFrequency() }}</strong> weather update for
            <strong>{{ $data->getCity() }}</strong>:
        </p>

        <div class="weather-box">
            <h2>Current Conditions</h2>
            <p class="temperature">{{ $data->getTemperature() }}°C</p>
            <p class="weather-detail"><strong>Description:</strong> {{ $data->getDescription() }}</p>
            <p class="weather-detail"><strong>Humidity:</strong> {{ $data->getHumidity() }}%</p>
        </div>

        <p>Thank you,<br>The Weather Updates Team</p>
    </div>
</div>
<div class="footer">
    Subscription frequency: <strong>{{ $data->getFrequency() }}</strong>
</div>
<div class="unsubscribe">
    <a href="{{ $data->getUnsubscribeUrl() }}" target="_blank" rel="noopener noreferrer">Unsubscribe from these
        emails</a>
</div>
</body>
</html>
