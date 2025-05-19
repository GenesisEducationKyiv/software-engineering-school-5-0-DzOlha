import {API_URL} from "../constants.js";

class WeatherApiService {
    constructor(
        csrfToken,
        baseUrl = API_URL
    ) {
        this.csrfToken = csrfToken;
        this.baseUrl = baseUrl;

        this.endpoints = {
            getWeather: `${this.baseUrl}/weather`,
            subscribe: `${this.baseUrl}/subscribe`,
            confirm: `${this.baseUrl}/confirm`,
            unsubscribe: `${this.baseUrl}/unsubscribe`
        }
    }
    /**
     * Get current weather for a city
     * @param {string} city - The city name
     * @returns {Promise<Object>} - Weather data
     */
    async getCurrentWeather(city) {
        const url = `${this.endpoints.getWeather}?city=${encodeURIComponent(city)}`;

        const response = await fetch(url, {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': this.csrfToken
            }
        });

        const data = await response.json();

        if (!response.ok) {
            if (response.status === 400 && data.errors) {
                throw new Error(Object.values(data.errors).flat().join(', '));
            }
            throw new Error(data.message || 'Failed to get weather data');
        }

        return data;
    }

    /**
     * Subscribe to weather updates
     * @param {string} email - User's email
     * @param {string} city - The city to get updates for
     * @param {string} frequency - Update frequency (hourly/daily)
     * @returns {Promise<Object>} - Subscription response
     */
    async subscribeToWeather(email, city, frequency) {
        const url = `${this.endpoints.subscribe}`;

        const response = await fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': this.csrfToken
            },
            body: JSON.stringify({ email, city, frequency })
        });

        const data = await response.json();

        if (!response.ok) {
            const error = new Error(data.message || 'Failed to subscribe');

            if (response.status === 400 && data.errors) {
                error.validationErrors = data.errors;
            }

            throw error;
        }

        return data;
    }

    /**
     * Confirm subscription using token
     * @param {string} token - Confirmation token
     * @returns {Promise<Object>} - Confirmation response
     */
    confirmSubscription = async (token) => {
        const url = `${this.endpoints.confirm}/${encodeURIComponent(token)}`;

        const response = await fetch(url, {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': this.csrfToken
            }
        });

        const data = await response.json();

        if (!response.ok) {
            const error = new Error(data.message || 'Failed to confirm subscription');
            if (response.status === 400 && data.errors) {
                error.validationErrors = data.errors;
            }
            throw error;
        }

        return data;
    }

    /**
     * Unsubscribe using token
     * @param {string} token - Unsubscribe token
     * @returns {Promise<Object>} - Unsubscribe response
     */
    unsubscribeFromWeather = async (token) => {
        const url = `${this.endpoints.unsubscribe}/${encodeURIComponent(token)}`;

        const response = await fetch(url, {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': this.csrfToken
            }
        });

        const data = await response.json();

        if (!response.ok) {
            const error = new Error(data.message || 'Failed to unsubscribe');
            if (response.status === 400 && data.errors) {
                error.validationErrors = data.errors;
            }
            throw error;
        }

        return data;
    }
}
export default WeatherApiService;
