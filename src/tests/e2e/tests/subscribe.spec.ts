// tests/subscribe.spec.ts
import { test, expect } from '@playwright/test';
import { SubscribePage } from './pages/SubscribePage';

test.describe('Weather Subscription', () => {
    // @ts-ignore
    test('should allow user to subscribe to weather updates', async ({ page }) => {
        const subscribePage = new SubscribePage(page);

        await subscribePage.goto();

        await subscribePage.enterCity('Kyiv');

        await subscribePage.openSubscriptionForm();

        await subscribePage.subscribe('john@gmail.com', 'daily');

        await expect(subscribePage.messageBox).toBeVisible();
        const message = await subscribePage.getMessage();
        expect(message?.toLowerCase()).toContain(
            'subscription successful. confirmation email sent. please check your inbox.'
        );
    });
});
