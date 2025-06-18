FROM mcr.microsoft.com/playwright:v1.43.1-jammy

WORKDIR /var/www/html/tests/e2e

COPY ./src/tests/e2e/package*.json ./

RUN npm ci && npx playwright install --with-deps

COPY ./src/tests/e2e .

CMD ["npx", "playwright", "test"]
