import { Page, Locator } from '@playwright/test';

export class SubscribePage {
    readonly page: Page;
    readonly cityInput: Locator;
    readonly getWeatherButton: Locator;
    readonly showSubscribeFormBtn: Locator;
    readonly emailInput: Locator;
    readonly frequencySelect: Locator;
    readonly subscribeSubmit: Locator;
    readonly messageBox: Locator;

    constructor(page: Page) {
        this.page = page;
        this.cityInput = page.locator('#city');
        this.getWeatherButton = page.locator('#city-submit');
        this.showSubscribeFormBtn = page.locator('#showSubscribeFormBtn');
        this.emailInput = page.locator('#email');
        this.frequencySelect = page.locator('#frequency');
        this.subscribeSubmit = page.locator('#subscribe-submit');
        this.messageBox = page.locator('#message');
    }

    async goto() {
        await this.page.goto('/');
    }

    async enterCity(city: string) {
        await this.cityInput.fill(city);
        await this.getWeatherButton.click();
    }

    // @ts-ignore
    async openSubscriptionForm() {
        await this.showSubscribeFormBtn.click();
    }

    async subscribe(email: string, frequency: string = 'daily') {
        await this.emailInput.fill(email);
        await this.frequencySelect.selectOption(frequency);
        await this.subscribeSubmit.click();
    }

    async getMessage() {
        return this.messageBox.textContent();
    }
}
