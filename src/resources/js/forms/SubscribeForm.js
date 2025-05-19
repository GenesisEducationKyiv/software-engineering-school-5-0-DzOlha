import EmailValidator from "../validator/EmailValidator.js";
import FrequencyValidator from "../validator/FrequencyValidator.js";
import Form from "./Form.js";

class SubscribeForm extends Form
{
    constructor(
        apiService,
        getForm,
        triggerBtnId = 'showSubscribeFormBtn',
        formBlockId = 'subscriptionForm',
        emailId = 'email',
        selectFrequencyId = 'frequency',
        submitButtonId = 'subscribe-submit',
        cancelButtonId = 'subscribe-cancel',
    ) {
        super();

        this.apiService = apiService;
        this.getForm = getForm;

        this.triggerBtn = document.getElementById(triggerBtnId);
        this.form = document.getElementById(formBlockId);

        this.emailInput = document.getElementById(emailId);
        this.frequencySelect = document.getElementById(selectFrequencyId);

        this.submitBtn = document.getElementById(submitButtonId);
        this.cancelBtn = document.getElementById(cancelButtonId);

        this.submitBtnLabel = this.submitBtn.innerText;

        this.setup();
    }

    setup() {
        this.triggerBtn.addEventListener('click', this.handleShowForm);
        this.submitBtn.addEventListener('click', this.handleSubmitForm);
        this.cancelBtn.addEventListener('click', this.handleCancelForm);
    }

    handleShowForm = (e) => {
        this.hideTriggerBtn();
        this.showForm()
    }

    handleSubmitForm = async (e) => {
        e.preventDefault();

        const city = this.getForm.validatedCityName();
        const email = this.validatedEmail();
        const frequency = this.validatedFrequency();

        if(!city || !email || !frequency) return;

        this.showLoader(this.submitBtn);

        try {
            const response = await this.apiService.subscribeToWeather(email, city, frequency);
            this.getForm.messageBlock.show(response.message || 'Successfully subscribed!', false);

            this.clearForm();
        }
        catch (err) {
            if (err.validationErrors) {
                const errorMessages = [];
                for (const field in err.validationErrors) {
                    errorMessages.push(...err.validationErrors[field]);
                }
                this.getForm.messageBlock.show(
                    `Validation errors: ${errorMessages.join(', ')}`
                );
            } else {
                this.getForm.messageBlock.show(
                    `Subscription Error: ${err.message || JSON.stringify(err)}`
                );
            }
        }
        this.hideLoader(this.submitBtn, this.submitBtnLabel);
    }

    hideTriggerBtn = () =>  {
        this.triggerBtn.style.display = 'none';
    }

    showTriggerBtn = () =>  {
        this.triggerBtn.style.display = 'block';
    }

    hideForm = () =>  {
        this.form.style.display = 'none';
    }

    showForm = () =>  {
        this.form.style.display = 'block';
    }

    handleCancelForm = (e) => {
        this.hideForm();
        this.showTriggerBtn();
    }

    validatedEmail() {
        const email = this.emailInput.value;

        if(!EmailValidator.validate(email)) {
            this.getForm.messageBlock.show('Please enter a valid email address');
            return false;
        }

        return email;
    }

    validatedFrequency() {
        const frequency = this.frequencySelect.value;
        if(!FrequencyValidator.validate(frequency)) {
            this.getForm.messageBlock.show('Frequency must be either hourly or daily');
            return false;
        }

        return frequency;
    }

    clearForm() {
        this.emailInput.value = '';
        this.frequencySelect.value = 'daily';
        this.hideForm();
        this.getForm.clearForm();
        this.showTriggerBtn();
    }
}
export default SubscribeForm;
