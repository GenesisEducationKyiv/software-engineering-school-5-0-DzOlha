FROM mcr.microsoft.com/playwright:v1.43.1-jammy

WORKDIR /var/www/html/tests/e2e

COPY ./src/tests/e2e/package*.json ./

RUN npm cache clean --force
RUN npm install -g npm@latest
RUN npm install

COPY ./src/tests/e2e .

RUN npx playwright install --with-deps

CMD ["npx", "playwright", "test"]
