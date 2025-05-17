# 🌦️ WeatherNet Application

A Docker-based web application for monitoring weather data.

---

## 🚀 Setup Instructions

### 🔧 Development Environment

1. **Create environment configuration files**:
    - Copy `.env.example` to `.env` for each service:
      ```bash
      cp infra/dev/php/.env.example infra/dev/php/.env
      cp infra/dev/postgres/.env.example infra/dev/postgres/.env
      ```

2. **(Optional) Customize database settings**:

   In `infra/dev/postgres/.env`:
   ```env
   POSTGRES_DB=your_database_name
   POSTGRES_USER=your_username
   POSTGRES_PASSWORD=your_password
   ```

   In `infra/dev/php/.env`, set the DB config to match:
   ```env
   DB_DATABASE=your_database_name
   DB_USERNAME=your_username
   DB_PASSWORD=your_password
   ```

3. **Run the application**:
   ```bash
   cd infra/dev
   docker compose up --build
   ```

4. Open your browser and go to: [http://localhost:8088](http://localhost:8088)

---

### 🏗️ Production Environment

1. **Create environment configuration files**:
    - Copy `.env.example` to `.env` for each service:
      ```bash
      cp infra/prod/php/.env.example infra/prod/php/.env
      cp infra/prod/postgres/.env.example infra/prod/postgres/.env
      ```

2. **(Optional) Customize database settings**:

   In `infra/prod/postgres/.env`:
   ```env
   POSTGRES_DB=your_database_name
   POSTGRES_USER=your_username
   POSTGRES_PASSWORD=your_password
   ```

   In `infra/prod/php/.env`, set the DB config to match:
   ```env
   DB_DATABASE=your_database_name
   DB_USERNAME=your_username
   DB_PASSWORD=your_password
   ```

3. **Run the application in production-like mode** (no bind mounts for real-time code updates):
   ```bash
   cd infra/prod
   docker compose up --build
   ```

4. Open your browser and go to: [http://localhost:8088](http://localhost:8088)

---

## ✅ Prerequisites

- [Docker](https://www.docker.com/) & [Docker Compose](https://docs.docker.com/compose/)
- Access to ports `8088` and `5432` on your machine

---

## 📁 Project Structure

```
infra/
├── dev/
│   ├── php/
│   │   └── .env
│   └── postgres/
│       └── .env
├── prod/
│   ├── php/
│   │   └── .env
│   └── postgres/
│       └── .env
```

---

## 📬 Support

For any issues, please open an [Issue](https://github.com/your-repo/weathernet/issues) or contact the maintainer.
