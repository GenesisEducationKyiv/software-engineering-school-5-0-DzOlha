@extends('layouts.app')

@section('title', 'Weather Subscription')

@section('content')
    <div class="hero">
        <div class="form-container">
            <h1>Subscribe to Weather Updates</h1>

            <div id="message" class="message" style="display: none;"></div>

            <form id="getWeatherForm">
                <div id="currentWeatherBox" class="weather-box" style="display: none;">
                    <h2>
                        Current weather in
                        <span id="current-city"></span>
                    </h2>

                    <div class="weather-detail-wrapper">
                        <p class="temperature">
                            <span id="current-temperature"></span>°C
                        </p>
                        <div class="weather-details">
                            <p class="weather-detail">
                                <strong>Description: </strong>
                                <span id="current-description"></span>
                            </p>
                            <p class="weather-detail">
                                <strong>Humidity: </strong>
                                <span id="current-humidity"></span>%
                            </p>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="city">City</label>
                    <div class="city-input-wrapper">
                        <input type="text" id="city" name="city" autocomplete="off" required placeholder="e.g. New York">
                        <button type="submit" id="city-submit">
                            Get Weather
                        </button>
                    </div>
                </div>

                <button type="button" id="showSubscribeFormBtn">Subscribe</button>
            </form>

            <form id="subscriptionForm" style="display: none;">
                <div class="form-group">
                    <label for="email">Email Address</label>
                    <input type="email" id="email" name="email" autocomplete="off" required placeholder="e.g. john@example.com">
                </div>

                <div class="form-group">
                    <label for="frequency">Update Frequency</label>
                    <select id="frequency" name="frequency" required>
                        <option value="hourly">hourly</option>
                        <option value="daily" selected>daily</option>
                    </select>
                </div>

                <div class="subscription-buttons">
                    <button type="submit" id="subscribe-submit" class="confirm">Subscribe</button>
                    <button type="submit" id="subscribe-cancel" class="cancel">Cancel</button>
                </div>
            </form>
        </div>

        <div class="image-section" id="weatherImage">
            <img src="{{ asset('resources/img/hero.png') }}" alt="Weather Hero Image" />
        </div>
    </div>

    <script type="module" src="{{ asset('resources/js/handlers/subscribe-page.js') }}"></script>
@endsection
