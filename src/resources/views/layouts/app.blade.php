<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Weather App')</title>
    <link rel="stylesheet" href="{{ asset('resources/css/app.css') }}">
    <link rel="icon" href="{{ asset('favicon.png') }}" type="image/png">
</head>
<body>

@yield('content')

</body>
</html>
