# Shop Admin Blade/API

Laravel backend Blade/API running with Docker.

---

## Requirements

- Docker >= 24
- Docker Compose

---

## Setup

Clone project

```bash
git clone https://github.com/antv-runs/shop-admin.git
cd shop-admin
```

Create environment file

```bash
cp .env.example .env
```

Then copy the configuration from one of the following files into `.env` depending on your environment:

- `.env.local` for local development

- `.env.production` for production

---

## Build Image (Optional)

> The image has already been published on Docker Hub, so you normally **do not need to build it manually**.

> Only run the build command if you want to **build the image directly from the source code**.

```bash
docker build --target=prod -t antvrunss/shop-admin-api:latest .
```

---

## Run Containers

```bash
docker-compose up -d
```

Services will start:

- Nginx
- PHP-FPM
- MySQL
- Redis
- Queue worker
- Scheduler

---

## Initialize Application

For first-time local development setup only, run the commands below after the MySQL service defined in `docker-compose.yml` is up and running.

```bash
docker exec -it shop-admin-api-prod php artisan migrate --force
docker exec -it shop-admin-api-prod php artisan db:seed
docker exec -it shop-admin-api-prod php artisan config:cache
```

## Access

Blade App

```
http://localhost:8000
```

RESTful API (Swagger UI)

```
http://localhost:8000/api/documentation
```

---

## Useful Commands

Logs API

```bash
docker logs shop-admin-api-prod
```

Logs Queue

```bash
docker logs shop-admin-queue-prod
```

Logs Scheduler

```bash
docker logs shop-admin-scheduler-prod
```

Stop

```bash
docker-compose down
```

# Test Accounts

| Role  | Email                 | Password |
| ----- | --------------------- | -------- |
| Admin | uter.vanan@gmail.com  | password |
| User  | vanantran05@gmail.com | password |

> These accounts are for testing purposes only.

> The **User** account is used to test order placement.

> The **Admin** account is for testing management APIs.

# Database Connection (Local)

The application uses MySQL as the primary database.

Default connection settings:

```env
DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=shop_admin
DB_USERNAME=shop
DB_PASSWORD=shop
```
