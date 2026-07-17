# Laravel Docker Setup - shop-admin

## 1. Cấu trúc thư mục

```txt
shop-admin/
├─ .env
├─ artisan
├─ composer.json
├─ public/
├─ storage/
├─ bootstrap/
└─ docker/
   ├─ docker-compose.yml
   ├─ docker-compose.phpmyadmin.yml
   ├─ Dockerfile
   ├─ README.md
   ├─ nginx/
   │  └─ default.conf
   └─ php/
      ├─ entrypoint.sh
      └─ uploads.ini
```

---

## 2. Khởi chạy dự án (Docker)

Đứng tại thư mục root của dự án `shop-admin` và chạy:

```bash
docker compose -f docker/docker-compose.yml up -d --build
```

Hoặc di chuyển vào thư mục `docker/` rồi chạy:

```bash
cd docker
docker compose up -d --build
```

Sau khi các container chạy thành công, truy cập hệ thống tại trình duyệt:

```txt
http://localhost:8000
```

---

## 3. Các lệnh cần chạy thủ công khi cập nhật code

### 3.1. Khi thêm/cập nhật thư viện mới (`composer.json`)

Khi cập nhật hoặc cài đặt gói package PHP mới, bạn cần chạy lệnh cài đặt **bên trong container**:

```bash
docker compose -f docker/docker-compose.yml exec app composer install
```

### 3.2. Khi cập nhật giao diện (CSS / JS / Tailwind classes mới)

Cập nhật giao diện cần biên dịch lại tài nguyên frontend. Bạn có 2 cách:

**1️⃣ Chạy trên máy host (đề xuất)**
```bash
npm install   # một lần, nếu chưa có các package
npm run dev   # Vite dev server, hot‑reload
```

**2️⃣ Chạy trong Docker (sử dụng container `node`)**
```bash
# Cài đặt các package (chỉ chạy 1 lần)
docker compose -f docker/docker-compose.yml exec node npm install

# Chế độ phát triển (hot‑reload)
docker compose -f docker/docker-compose.yml exec node npm run dev

# Hoặc biên dịch bản production
docker compose -f docker/docker-compose.yml exec node npm run build
```

### 3.3. Khi cập nhật Database (Migrations)

Để cập nhật cơ sở dữ liệu khi có migrations mới:

```bash
docker compose -f docker/docker-compose.yml exec app php artisan migrate
```

### 3.4. Khi chạy dữ liệu mẫu (Seeders)

Để tạo dữ liệu mẫu (seed) vào cơ sở dữ liệu:

```bash
docker compose -f docker/docker-compose.yml exec app php artisan db:seed
```

Nếu chỉ muốn chạy một file seeder cố định (ví dụ: `UserSeeder`):
```bash
docker compose -f docker/docker-compose.yml exec app php artisan db:seed --class=UserSeeder
```

Hoặc để vừa chạy lệnh migrate vừa tự động seed luôn dữ liệu:
```bash
docker compose -f docker/docker-compose.yml exec app php artisan migrate --seed
```

### 3.5. Khi chỉnh sửa file `.env`

Bạn cần khởi động lại container để cập nhật biến môi trường mới và tiến hành xóa cache cấu hình:

```bash
# 1. Restart container app
docker compose -f docker/docker-compose.yml restart app

# 2. Xóa cache cấu hình trong container
docker compose -f docker/docker-compose.yml exec app php artisan config:clear
```

### 3.6. Mở công cụ quản lý Database (phpMyAdmin)

Dự án có sẵn một file cấu hình riêng để mở giao diện quản lý MySQL (đã được trỏ sẵn vào database trên máy bạn):

```bash
docker compose -f docker/docker-compose.phpmyadmin.yml up -d
```
Sau đó truy cập: `http://localhost:8889` để vào phpMyAdmin.

---

## 4. Các lệnh tiện ích khác

- **Truy cập vào Bash của container app:**
    ```bash
    docker compose -f docker/docker-compose.yml exec app bash
    ```
- **Xem log Laravel thời gian thực:**
    ```bash
    docker compose -f docker/docker-compose.yml exec app tail -f storage/logs/laravel.log
    ```
- **Dọn dẹp toàn bộ cache của Laravel:**
    ```bash
    docker compose -f docker/docker-compose.yml exec app php artisan optimize:clear
    ```
- **Dừng các container:**
    ```bash
    docker compose -f docker/docker-compose.yml down
    ```
