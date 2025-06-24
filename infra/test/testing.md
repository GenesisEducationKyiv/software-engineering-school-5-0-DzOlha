# 🧪 Running Tests Locally

This guide describes how to run **Unit**, **Integration**, and **E2E (End-to-End)** tests locally, replicating the steps used in GitHub Actions pipelines.

> ✅ Prerequisites:
> - [Docker](https://docs.docker.com/get-docker/) installed
> - `infra/test/php/.env` file already contains the `WEATHER_API_KEY`, `OPEN_WEATHER_API_KEY`, and `WEATHER_STACK_API_KEY` (you only need to **replace** them if needed)
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

These tests require valid `WEATHER_API_KEY`, `OPEN_WEATHER_API_KEY`, and `WEATHER_STACK_API_KEY` values in `.env`.

### 1. Update the keys in `.env`

```bash
# Replace YOUR_*_KEY with your actual keys
sed -i "s/^WEATHER_API_KEY=.*/WEATHER_API_KEY=YOUR_WEATHER_API_KEY/" infra/test/php/.env
sed -i "s/^OPEN_WEATHER_API_KEY=.*/OPEN_WEATHER_API_KEY=YOUR_OPEN_WEATHER_API_KEY/" infra/test/php/.env
sed -i "s/^WEATHER_STACK_API_KEY=.*/WEATHER_STACK_API_KEY=YOUR_WEATHER_STACK_API_KEY/" infra/test/php/.env
```

If the keys are missing, you can append them manually:

```bash
echo "WEATHER_API_KEY=YOUR_WEATHER_API_KEY" >> infra/test/php/.env
echo "OPEN_WEATHER_API_KEY=YOUR_OPEN_WEATHER_API_KEY" >> infra/test/php/.env
echo "WEATHER_STACK_API_KEY=YOUR_WEATHER_STACK_API_KEY" >> infra/test/php/.env
```

### 🔧 Run locally

```bash
docker compose -f infra/test/docker-compose.yaml up --build --abort-on-container-exit --remove-orphans integration-runner
docker compose -f infra/test/docker-compose.yaml down
```

---

## 🔹 E2E (End-to-End) Tests

These also require all three API keys to be present in `.env`.

### 1. Update the keys in `.env`

```bash
# Replace YOUR_*_KEY with your actual keys
sed -i "s/^WEATHER_API_KEY=.*/WEATHER_API_KEY=YOUR_WEATHER_API_KEY/" infra/test/php/.env
sed -i "s/^OPEN_WEATHER_API_KEY=.*/OPEN_WEATHER_API_KEY=YOUR_OPEN_WEATHER_API_KEY/" infra/test/php/.env
sed -i "s/^WEATHER_STACK_API_KEY=.*/WEATHER_STACK_API_KEY=YOUR_WEATHER_STACK_API_KEY/" infra/test/php/.env
```

If the keys are missing, you can append them manually:

```bash
echo "WEATHER_API_KEY=YOUR_WEATHER_API_KEY" >> infra/test/php/.env
echo "OPEN_WEATHER_API_KEY=YOUR_OPEN_WEATHER_API_KEY" >> infra/test/php/.env
echo "WEATHER_STACK_API_KEY=YOUR_WEATHER_STACK_API_KEY" >> infra/test/php/.env
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
