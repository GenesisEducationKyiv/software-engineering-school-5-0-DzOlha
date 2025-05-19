import WeatherApiService from "../api/WeatherApiService.js";
import GetWeatherForm from "../forms/GetWeatherForm.js";
import SubscribeForm from "../forms/SubscribeForm.js";

document.addEventListener('DOMContentLoaded', function() {
    const csrfToken = document
        .querySelector('meta[name="csrf-token"]')
        .getAttribute('content');

    const api = new WeatherApiService(csrfToken);

    const getWeatherForm = new GetWeatherForm(api);

    const subscribeForm = new SubscribeForm(api, getWeatherForm);
    subscribeForm.setup();
});
