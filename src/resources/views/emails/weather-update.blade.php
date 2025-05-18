<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Weather Update for {{ $city }}</title>
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
        .weather-box {
            background-color: #eaf6ff;
            border-radius: 8px;
            padding: 20px 25px;
            margin: 25px 0;
            border: 1px solid #c3dbf7;
            color: #2c3e50;
        }
        .weather-box h2 {
            margin-top: 0;
            font-weight: 600;
            font-size: 22px;
            margin-bottom: 15px;
        }
        .temperature {
            font-size: 36px;
            font-weight: 700;
            color: #e67e22;
            margin: 10px 0 15px;
        }
        .weather-detail {
            font-size: 16px;
            margin: 6px 0;
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
        .unsubscribe {
            font-style: normal;
            font-size: 12px;
            color: #999;
            text-align: center;
            margin-top: 10px;
        }
        .unsubscribe a {
            color: #888;
            text-decoration: underline;
        }
        .unsubscribe a:hover {
            color: #555;
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
            .weather-box {
                padding: 15px 20px;
                margin: 20px 0;
            }
        }
    </style>
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
