# 🧪 Running Tests Locally

This guide describes how to run **Unit**, **Integration**, and **E2E (End-to-End)** tests locally, replicating the steps used in GitHub Actions pipelines.

> ✅ Prerequisites:
> - [Docker](https://docs.docker.com/get-docker/) installed
> - `infra/test/php/.env` file already contains the `WEATHER_API_KEY` (you only need to **replace** it if needed)
> - Project cloned locally

---

## 🔹 Unit Tests

These tests are executed in the `unit-runner` container.

### 🔧 Run locally

```bash
docker compose -f infra/test/docker-compose.yaml up --build --abort-on-container-exit --remove-orphans unit-runner
docker compose -f infra/test/docker-compose.yaml down
```

---

## 🔹 Integration Tests

These tests require a valid `WEATHER_API_KEY` in the `.env` file.

### 1. Update the key in `.env`

```bash
# Replace YOUR_KEY with your actual key
sed -i "s/^WEATHER_API_KEY=.*/WEATHER_API_KEY=YOUR_KEY/" infra/test/php/.env
```

### 🔧 Run locally

```bash
docker compose -f infra/test/docker-compose.yaml up --build --abort-on-container-exit --remove-orphans integration-runner
docker compose -f infra/test/docker-compose.yaml down
```

---

## 🔹 E2E (End-to-End) Tests

Also requires the `WEATHER_API_KEY`.

### 1. Update the key in `.env`

```bash
# Replace YOUR_KEY with your actual key
sed -i "s/^WEATHER_API_KEY=.*/WEATHER_API_KEY=YOUR_KEY/" infra/test/php/.env
```

### 🔧 Run locally

```bash
docker compose -f infra/test/docker-compose.yaml up --build --abort-on-container-exit --remove-orphans e2e-runner
docker compose -f infra/test/docker-compose.yaml down
```

---

## 🧼 Notes

- All test types use the same `docker-compose` file: `infra/test/docker-compose.yaml`.
- Use `--abort-on-container-exit` to ensure the test runner controls the lifecycle.
- Always run `docker compose down` after tests to clean up containers.
