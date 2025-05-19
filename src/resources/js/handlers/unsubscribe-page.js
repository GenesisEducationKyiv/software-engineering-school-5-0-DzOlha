import WeatherApiService from "../api/WeatherApiService.js";
import TokenForm from "../forms/TokenForm.js";

document.addEventListener('DOMContentLoaded', function() {
    const csrfToken = document
        .querySelector('meta[name="csrf-token"]')
        .getAttribute('content');

    const api = new WeatherApiService(csrfToken);

    const form = new TokenForm();
    form.process(
        api.unsubscribeFromWeather
    );
});
