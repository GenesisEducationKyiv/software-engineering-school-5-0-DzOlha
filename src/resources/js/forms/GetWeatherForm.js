import CurrentWeatherBlock from "../blocks/CurrentWeatherBlock.js";
import MessageBlock from "../blocks/MessageBlock.js";
import CityValidator from "../validator/CityValidator.js";
import Form from "./Form.js";

class GetWeatherForm extends Form
{
    constructor(
        apiService,
        currentWeatherBlock = new CurrentWeatherBlock(),
        messageBlock = new MessageBlock(),
        cityInputId = 'city',
        submitBtnId = 'city-submit'
    ) {
        super();

        this.apiService = apiService;
        this.messageBlock = messageBlock;
        this.currentWeatherBlock = currentWeatherBlock;
        this.cityInput = document.getElementById(cityInputId);
        this.submitBtn = document.getElementById(submitBtnId);

        this.submitBtnLabel = this.submitBtn.innerText;

        this.setup();
    }

    setup() {
        this.submitBtn.addEventListener('click', this.handleSubmit);
    }

    handleSubmit = async (e) => {
        e.preventDefault();

        const cityValue = this.validatedCityName();
        if(!cityValue) {
            this.messageBlock.show('City name must be between 2 and 50 characters');
            return;
        }

        this.showLoader(this.submitBtn);

        try {
            const weatherResponse = await this.apiService.getCurrentWeather(cityValue);

            if (weatherResponse.success) {
                this.currentWeatherBlock.show(weatherResponse.data, cityValue);
            } else {
                this.messageBlock.show(`${weatherResponse.message}`);
            }
        } catch (err) {
            this.messageBlock.show(`${err.message || JSON.stringify(err)}`);
        }

        this.hideLoader(this.submitBtn, this.submitBtnLabel);
    }

    validatedCityName = () => {
        const cityValue = this.cityInput.value;
        if (CityValidator.validate(cityValue)) {
            this.messageBlock.show('City name must be between 2 and 50 characters');
            return false;
        }
        return cityValue;
    }

    clearForm() {
        this.cityInput.value = "";
    }
}
export default GetWeatherForm;
