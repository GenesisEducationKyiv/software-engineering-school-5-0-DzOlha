import { defineConfig, devices } from '@playwright/test';

export default defineConfig({
    testDir: './tests',
    timeout: 30_000,
    retries: 1,
    use: {
        baseURL: process.env.BASE_URL || 'http://localhost:8088',
        headless: true,
        viewport: { width: 1280, height: 720 },
        ignoreHTTPSErrors: true,
        video: 'retain-on-failure',
    },
    projects: [
        {
            name: 'chromium',
            use: {
                ...devices['Desktop Chrome'],
            },
        },
    ],
    reporter: [['list'], ['html', { outputFolder: 'playwright-report', open: 'never' }]],
});
