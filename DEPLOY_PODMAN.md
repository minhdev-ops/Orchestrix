# DEPLOY ORCHESTRIX VỚI PODMAN

> Hướng dẫn deploy toàn diện dự án Orchestrix (Laravel 12 + Vue 3 + MySQL + Redis) menggunakan Podman.

---

## Mục lục

1. [Kiến trúc tổng quan](#1-kiến-trúc-tổng-quan)
2. [Yêu cầu hệ thống](#2-yêu-cầu-hệ-thống)
3. [Cài đặt Podman & Podman Compose](#3-cài-đặt-podman--podman-compose)
4. [Chuẩn bị cấu hình](#4-chuẩn-bị-cấu-hình)
5. [Tạo container images](#5-tạo-container-images)
6. [Khởi chạy toàn bộ dịch vụ](#6-khởi-chạy-toàn-bộ-dịch-vụ)
7. [Khởi tạo Laravel (migrations, key, seed)](#7-khởi-tạo-laravel)
8. [Xây dựng Frontend (Vite)](#8-xây-dựng-frontend)
9. [Cấu hình Nginx](#9-cấu-hình-nginx)
10. [SSL/TLS (Tùy chọn)](#10-ssltls-tùy-chọn)
11. [Quản lý Queue Worker & Scheduler](#11-quản-lý-queue-worker--scheduler)
12. [Bảo mật Production](#12-bảo-mật-production)
13. [Monitoring & Logs](#13-monitoring--logs)
14. [Backup & Restore Database](#14-backup--restore-database)
15. [Triển khai cập nhật (Rolling Update)](#15-triển-khai-cập-nhật)
16. [Khắc phục sự cố](#16-khắc-phục-sự-cố)
17. [Podman Compose file đầy đủ](#17-podman-compose-file-đầy-đủ)
18. [Checklist trước khi deploy](#18-checklist-trước-khi-deploy)

---

## 1. Kiến trúc tổng quan

```
┌─────────────────────────────────────────────────────────┐
│                    HOST (Podman)                        │
│                                                         │
│  ┌─────────────┐                                        │
│  │   NGINX     │ :80, :443                              │
│  │  (alpine)   │──► PHP-FPM :9000                       │
│  └─────────────┘                                        │
│                     ┌──────────────┐                     │
│                     │   PHP-FPM    │                     │
│                     │  (8.3-fpm)   │                     │
│                     └──────┬───────┘                     │
│                            │                             │
│              ┌─────────────┼─────────────┐               │
│              ▼             ▼             ▼               │
│       ┌──────────┐  ┌──────────┐  ┌──────────┐         │
│       │  MySQL   │  │  Redis   │  │ Storage  │         │
│       │  (8.0)   │  │ (alpine) │  │  (vol)   │         │
│       └──────────┘  └──────────┘  └──────────┘         │
│                                                         │
│  ┌──────────────┐    ┌──────────────┐                   │
│  │ Queue Worker │    │  Scheduler   │                   │
│  │ (php artisan │    │ (php artisan │                   │
│  │  queue:work) │    │  schedule)   │                   │
│  └──────────────┘    └──────────────┘                   │
└─────────────────────────────────────────────────────────┘
```

**Dịch vụ bao gồm:**

| Dịch vụ | Image | Port | Mục đích |
|---------|-------|------|----------|
| nginx | nginx:alpine | 80, 443 | Web server, reverse proxy |
| php | php:8.3-fpm (custom) | 9000 | Application server (Laravel) |
| mysql | mysql:8.0 | 3306 | Database |
| redis | redis:alpine | 6379 | Cache, Queue, Session |
| queue | php:8.3-fpm (custom) | - | Background job processing |
| scheduler | php:8.3-fpm (custom) | - | Cron task scheduling |

---

## 2. Yêu cầu hệ thống

```bash
# Kiểm tra hệ điều hành (Ubuntu/Debian/CentOS/Fedora)
cat /etc/os-release

# Yêu cầu tối thiểu:
# - RAM: 2GB (khuyến nghị 4GB+)
# - Disk: 10GB+ free space
# - OS: Linux (Ubuntu 20.04+, Debian 11+, CentOS 8+, Fedora 36+)
# - Root hoặc sudo access
```

---

## 3. Cài đặt Podman & Podman Compose

### 3.1. Ubuntu / Debian

```bash
# Cài đặt Podman
sudo apt-get update
sudo apt-get install -y podman

# Cài đặt Podman Compose
sudo apt-get install -y podman-compose

# Hoặc cài qua pip (phiên bản mới hơn)
pip3 install podman-compose

# Kiểm tra phiên bản
podman --version
podman-compose --version
```

### 3.2. CentOS / RHEL / Fedora

```bash
# Fedora
sudo dnf install -y podman podman-compose

# CentOS 8+ / RHEL 8+
sudo dnf install -y epel-release
sudo dnf install -y podman
pip3 install podman-compose
```

### 3.3. Verify installation

```bash
# Kiểm tra Podman hoạt động
podman info

# Kiểm tra rootless mode (khuyến nghị)
podman unshare cat /proc/self/uid_map
```

### 3.4. Cấu hình Podman (tùy chọn nhưng khuyến nghị)

```bash
# Tạo file cấu hình containers.conf
mkdir -p ~/.config/containers
cat > ~/.config/containers/containers.conf << 'EOF'
[containers]
# Rootless DNS resolver
dns_bind_port = 5353

[engine]
# Tự động clean up container khi exit
stop_timeout = 30

[network]
# Tạo default network
EOF

# Tạo Podman network cho project
podman network create orchestrix-net
```

---

## 4. Chuẩn bị cấu hình

### 4.1. Clone dự án

```bash
# Clone repo
git clone <your-repo-url> orchestrix
cd orchestrix

# Hoặc nếu đã có source code
cd /path/to/orchestrix
```

### 4.2. Tạo file `.env`

```bash
# Copy từ template
cp .env.example .env

# Tạo APP_KEY ngẫu nhiên (dùng trong container sau)
php artisan key:generate --show
# Copy output key的价值 và paste vào .env
```

### 4.3. Chỉnh sửa `.env` cho Production

```bash
nano .env
```

**Các dòng QUAN TRỌNG cần thay đổi:**

```env
# ===== APP =====
APP_NAME=Orchestrix
APP_ENV=production
APP_KEY=base64:xxxxx    # Paste key vừa generate
APP_DEBUG=false
APP_URL=https://yourdomain.com

# ===== DATABASE =====
DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=orchestrix
DB_USERNAME=orchestrix_user
DB_PASSWORD=<TẠO_MẬT_KHẨU_MẠNH_Ở_DAY>

# ===== SESSION & CACHE =====
SESSION_DRIVER=redis
SESSION_LIFETIME=120
CACHE_STORE=redis
QUEUE_CONNECTION=redis

# ===== REDIS =====
REDIS_CLIENT=predis
REDIS_HOST=redis
REDIS_PASSWORD=<TẠO_MẬT_KHẨU_MẠNH_CHO_REDIS>
REDIS_PORT=6379

# ===== LOG =====
LOG_CHANNEL=stack
LOG_LEVEL=warning

# ===== MAIL (cấu hình khi cần) =====
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-app-password
MAIL_ENCRYPTION=tls
```

### 4.4. Tạo file `.env.production` (backup)

```bash
cp .env .env.production
```

---

## 5. Tạo container images

### 5.1. Tạo Podman network

```bash
podman network create orchestrix-net
```

### 5.2. Build Image PHP-FPM

```bash
# Build image PHP-FPM với tất cả extensions cần thiết
podman build -t orchestrix-php:latest .
```

**Dockerfile nội dung (đã có sẵn trong project):**

```dockerfile
FROM php:8.3-fpm

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    zip \
    unzip \
    supervisor \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www
COPY . /var/www
COPY --chown=www-data:www-data . /var/www

USER www-data
EXPOSE 9000
CMD ["php-fpm"]
```

### 5.3. Pull các image gốc

```bash
podman pull nginx:alpine
podman pull mysql:8.0
podman pull redis:alpine
```

### 5.4. Kiểm tra images

```bash
podman images | grep -E 'orchestrix|nginx|mysql|redis'
```

---

## 6. Khởi chạy toàn bộ dịch vụ

### 6.1. Tạo các thư mục cần thiết

```bash
# Tạo thư mục volumes
mkdir -p /opt/orchestrix/data/mysql
mkdir -p /opt/orchestrix/data/redis
mkdir -p /opt/orchestrix/data/storage
mkdir -p /opt/orchestrix/logs/nginx
mkdir -p /opt/orchestrix/logs/php

# Tạo thư mục SSL (nếu dùng HTTPS)
mkdir -p /opt/orchestrix/ssl
```

### 6.2. Tạo MySQL init script

```bash
mkdir -p /opt/orchestrix/init-db
cat > /opt/orchestrix/init-db/init.sql << 'EOF'
CREATE DATABASE IF NOT EXISTS orchestrix CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER IF NOT EXISTS 'orchestrix_user'@'%' IDENTIFIED BY 'CHANGE_ME_STRONG_PASSWORD';
GRANT ALL PRIVILEGES ON orchestrix.* TO 'orchestrix_user'@'%';
FLUSH PRIVILEGES;
EOF
```

### 6.3. Khởi chạy từng container (Podman commands)

#### Bước 6.3.1: MySQL

```bash
podman run -d \
  --name orchestrix-mysql \
  --network orchestrix-net \
  -e MYSQL_ROOT_PASSWORD=<ROOT_PASSWORD_MẠNH> \
  -e MYSQL_DATABASE=orchestrix \
  -e MYSQL_USER=orchestrix_user \
  -e MYSQL_PASSWORD=<CHANGE_ME_STRONG_PASSWORD> \
  -v /opt/orchestrix/data/mysql:/var/lib/mysql:Z \
  -v /opt/orchestrix/init-db/init.sql:/docker-entrypoint-initdb.d/init.sql:Z \
  --restart unless-stopped \
  mysql:8.0
```

#### Bước 6.3.2: Redis

```bash
podman run -d \
  --name orchestrix-redis \
  --network orchestrix-net \
  -v /opt/orchestrix/data/redis:/data:Z \
  --restart unless-stopped \
  redis:alpine redis-server --appendonly yes --requirepass <REDIS_PASSWORD>
```

#### Bước 6.3.3: PHP-FPM

```bash
podman run -d \
  --name orchestrix-php \
  --network orchestrix-net \
  -v /path/to/orchestrix:/var/www:Z \
  -v /opt/orchestrix/logs/php:/var/log:Z \
  -e DB_HOST=mysql \
  -e DB_PORT=3306 \
  -e DB_DATABASE=orchestrix \
  -e DB_USERNAME=orchestrix_user \
  -e DB_PASSWORD=<CHANGE_ME_STRONG_PASSWORD> \
  -e REDIS_HOST=redis \
  -e REDIS_PASSWORD=<REDIS_PASSWORD> \
  -e APP_ENV=production \
  --restart unless-stopped \
  orchestrix-php:latest
```

#### Bước 6.3.4: Nginx

```bash
podman run -d \
  --name orchestrix-nginx \
  --network orchestrix-net \
  -p 80:80 \
  -p 443:443 \
  -v /path/to/orchestrix:/var/www:Z \
  -v /path/to/orchestrix/docker/nginx/conf.d:/etc/nginx/conf.d:Z \
  -v /opt/orchestrix/ssl:/etc/nginx/ssl:Z \
  --restart unless-stopped \
  nginx:alpine
```

#### Bước 6.3.5: Queue Worker

```bash
podman run -d \
  --name orchestrix-queue \
  --network orchestrix-net \
  -v /path/to/orchestrix:/var/www:Z \
  -e DB_HOST=mysql \
  -e DB_PORT=3306 \
  -e DB_DATABASE=orchestrix \
  -e DB_USERNAME=orchestrix_user \
  -e DB_PASSWORD=<CHANGE_ME_STRONG_PASSWORD> \
  -e REDIS_HOST=redis \
  -e REDIS_PASSWORD=<REDIS_PASSWORD> \
  -e APP_ENV=production \
  --restart unless-stopped \
  orchestrix-php:latest \
  php artisan queue:work redis --sleep=3 --tries=3 --max-time=3600
```

#### Bước 6.3.6: Scheduler

```bash
podman run -d \
  --name orchestrix-scheduler \
  --network orchestrix-net \
  -v /path/to/orchestrix:/var/www:Z \
  -e DB_HOST=mysql \
  -e DB_PORT=3306 \
  -e DB_DATABASE=orchestrix \
  -e DB_USERNAME=orchestrix_user \
  -e DB_PASSWORD=<CHANGE_ME_STRONG_PASSWORD> \
  -e REDIS_HOST=redis \
  -e REDIS_PASSWORD=<REDIS_PASSWORD> \
  -e APP_ENV=production \
  --restart unless-stopped \
  orchestrix-php:latest \
  sh -c "while true; do php artisan schedule:run --verbose --no-interaction; sleep 60; done"
```

### 6.4. Kiểm tra trạng thái tất cả containers

```bash
podman ps -a --filter "name=orchestrix"
```

Expected output:
```
CONTAINER ID  IMAGE                  STATUS          PORTS                   NAMES
xxxxxx        nginx:alpine           Up 2 minutes    0.0.0.0:80->80/tcp      orchestrix-nginx
xxxxxx        orchestrix-php:latest  Up 2 minutes                            orchestrix-php
xxxxxx        mysql:8.0              Up 3 minutes    0.0.0.0:3306->3306/tcp  orchestrix-mysql
xxxxxx        redis:alpine           Up 3 minutes    0.0.0.0:6379->6379/tcp  orchestrix-redis
xxxxxx        orchestrix-php:latest  Up 2 minutes                            orchestrix-queue
xxxxxx        orchestrix-php:latest  Up 2 minutes                            orchestrix-scheduler
```

---

## 7. Khởi tạo Laravel

### 7.1. Chạy commands trong PHP container

```bash
# Vào container PHP
podman exec -it orchestrix-php bash
```

```bash
# Trong container - Kiểm tra Composer
composer --version

# Cài dependencies (nếu chưa có vendor/)
composer install --no-dev --optimize-autoloader

# Tạo APP_KEY
php artisan key:generate

# Chạy migrations
php artisan migrate --force

# Seed dữ liệu (nếu có)
php artisan db:seed --force

# Tạo storage link
php artisan storage:link

# Cache config, routes, views (production optimization)
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache

# Set permissions
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache

# Thoát container
exit
```

### 7.2. Hoặc chạy trực tiếp từ host

```bash
# Chạy migration
podman exec orchestrix-php php artisan migrate --force

# Seed
podman exec orchestrix-php php artisan db:seed --force

# Cache optimize
podman exec orchestrix-php php artisan config:cache
podman exec orchestrix-php php artisan route:cache
podman exec orchestrix-php php artisan view:cache
podman exec orchestrix-php php artisan event:cache

# Storage link
podman exec orchestrix-php php artisan storage:link
```

---

## 8. Xây dựng Frontend

### 8.1. Sử dụng Node.js container

```bash
# Tạo temporary container để build frontend
podman run --rm \
  --name orchestrix-node-build \
  -v /path/to/orchestrix:/var/www:Z \
  -w /var/www \
  node:20-alpine \
  sh -c "npm install && npm run build"
```

### 8.2. Hoặc build trên host trước khi deploy

```bash
# Trên host machine
cd /path/to/orchestrix
npm install
npm run build
# Sau đó copy public/build/ vào container hoặc mount trực tiếp
```

---

## 9. Cấu hình Nginx

### 9.1. File cấu hình chính

File đã có tại `docker/nginx/conf.d/app.conf`:

```nginx
server {
    listen 80;
    server_name localhost;
    root /var/www/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php;
    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass php:9000;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }

    location ~* \.(jpg|jpeg|png|gif|ico|css|js|webp|woff2|woff|ttf|svg)$ {
        expires 30d;
        add_header Cache-Control "public, immutable";
    }

    location ~ /\. {
        deny all;
    }
}
```

### 9.2. Cấu hình với domain thực

```bash
nano docker/nginx/conf.d/app.conf
```

Thay `server_name localhost;` bằng domain thực:

```nginx
server {
    listen 80;
    server_name yourdomain.com www.yourdomain.com;
    # ... phần còn lại giữ nguyên
}
```

---

## 10. SSL/TLS (Tùy chọn)

### 10.1. Self-signed certificate (test/development)

```bash
# Tạo SSL certs
openssl req -x509 -nodes -days 365 \
  -newkey rsa:2048 \
  -keyout /opt/orchestrix/ssl/server.key \
  -out /opt/orchestrix/ssl/server.crt \
  -subj "/C=VN/ST=Hanoi/L=Hanoi/O=Orchestrix/CN=localhost"
```

### 10.2. Cấu hình Nginx với SSL

```bash
cat > docker/nginx/conf.d/ssl.conf << 'NGINX_EOF'
server {
    listen 80;
    server_name yourdomain.com;
    return 301 https://$server_name$request_uri;
}

server {
    listen 443 ssl http2;
    server_name yourdomain.com;

    ssl_certificate /etc/nginx/ssl/server.crt;
    ssl_certificate_key /etc/nginx/ssl/server.key;
    ssl_protocols TLSv1.2 TLSv1.3;
    ssl_ciphers HIGH:!aNULL:!MD5;
    ssl_prefer_server_ciphers on;

    root /var/www/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";
    add_header Strict-Transport-Security "max-age=31536000; includeSubDomains" always;

    index index.php;
    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass php:9000;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }

    location ~* \.(jpg|jpeg|png|gif|ico|css|js|webp|woff2|woff|ttf|svg)$ {
        expires 30d;
        add_header Cache-Control "public, immutable";
    }
}
NGINX_EOF
```

### 10.3. Let's Encrypt (Production)

```bash
# Cài certbot trên host (không trong container)
sudo apt-get install -y certbot

# Dừng nginx temporarily
podman stop orchestrix-nginx

# Tạo certificate
sudo certbot certonly --standalone \
  -d yourdomain.com \
  -d www.yourdomain.com

# Copy certs vào thư mục SSL
sudo cp /etc/letsencrypt/live/yourdomain.com/fullchain.pem /opt/orchestrix/ssl/server.crt
sudo cp /etc/letsencrypt/live/yourdomain.com/privkey.pem /opt/orchestrix/ssl/server.key
sudo chmod 600 /opt/orchestrix/ssl/server.key

# Khởi động lại nginx
podman start orchestrix-nginx
```

---

## 11. Quản lý Queue Worker & Scheduler

### 11.1. Kiểm tra Queue Worker

```bash
# Xem log queue worker
podman logs -f orchestrix-queue

# Kiểm tra số jobs đang chờ
podman exec orchestrix-php php artisan queue:monitor redis
```

### 11.2. Restart Queue Worker

```bash
# Nếu worker bị stuck
podman restart orchestrix-queue

# Hoặc kill và restart
podman stop orchestrix-queue
podman rm orchestrix-queue
# Sau đó chạy lại lệnh tạo container ở Bước 6.3.5
```

### 11.3. Kiểm tra Scheduler

```bash
# Test scheduler chạy đúng
podman exec orchestrix-scheduler php artisan schedule:list

# Xem scheduler log
podman logs -f orchestrix-scheduler
```

---

## 12. Bảo mật Production

### 12.1. Firewall (iptables / firewalld)

```bash
# Ubuntu/Debian - UFW
sudo ufw allow 22/tcp    # SSH
sudo ufw allow 80/tcp    # HTTP
sudo ufw allow 443/tcp   # HTTPS
sudo ufw enable

# CentOS/Fedora - firewalld
sudo firewall-cmd --permanent --add-port=80/tcp
sudo firewall-cmd --permanent --add-port=443/tcp
sudo firewall-cmd --reload
```

### 12.2. Không expose MySQL & Redis ra ngoài

```bash
# Kiểm tra - KHÔNG nên thấy port 3306/6379 từ bên ngoài
ss -tlnp | grep -E '3306|6379'
# Nếu thấy, sửa podman run commands - bỏ -p 3306:3306 và -p 6379:6379
```

### 12.3. Podman Security Features

```bash
# Podman chạy rootless (an toàn hơn Docker)
# Kiểm tra是否 đang chạy rootless
podman info | grep rootless

# Sử dụng --security-opt để giảm privilege
# Thêm vào podman run:
#   --security-opt label=disable
#   --cap-drop ALL
#   --cap-add NET_BIND_SERVICE
```

### 12.4. Secrets Management

```bash
# KHÔNG lưu password trong .env ở production
# Sử dụng Podman secrets hoặc mounted file

# Tạo secret file
echo "CHANGE_ME_STRONG_PASSWORD" > /opt/orchestrix/secrets/db_password.txt
chmod 600 /opt/orchestrix/secrets/db_password.txt

# Mount secret vào container
podman run -d \
  --name orchestrix-php \
  -v /opt/orchestrix/secrets/db_password.txt:/run/secrets/db_password:ro \
  ...
```

---

## 13. Monitoring & Logs

### 13.1. Xem logs tất cả containers

```bash
# Log tất cả containers orchestrix
for container in orchestrix-nginx orchestrix-php orchestrix-mysql orchestrix-redis orchestrix-queue orchestrix-scheduler; do
  echo "=== $container ==="
  podman logs --tail 20 $container
  echo ""
done
```

### 13.2. Real-time logs

```bash
# Xem log real-time từng container
podman logs -f orchestrix-nginx
podman logs -f orchestrix-php
podman logs -f orchestrix-queue
```

### 13.3. Kiểm tra resource usage

```bash
# Xem CPU, Memory, Network của tất cả containers
podman stats --no-stream

# Output:
# ID        NAME                  CPU %   MEM USAGE / LIMIT   MEM %   NET I/O       BLOCK I/O
# xxxxxxxx  orchestrix-mysql      0.5%    200MiB / 1GiB       19.5%   1.5MB / 2MB   10MB / 5MB
# xxxxxxxx  orchestrix-redis      0.1%    10MiB / 512MiB      1.9%    500kB / 300kB 0B / 1MB
# ...
```

### 13.4. Health check scripts

```bash
cat > /opt/orchestrix/scripts/health-check.sh << 'HEALTHEOF'
#!/bin/bash
echo "=== Orchestrix Health Check ==="
echo ""

# Check containers
echo "--- Containers Status ---"
podman ps -a --filter "name=orchestrix" --format "table {{.Names}}\t{{.Status}}\t{{.Ports}}"
echo ""

# Check MySQL
echo "--- MySQL ---"
podman exec orchestrix-mysql mysqladmin ping -u root -p<ROOT_PASSWORD> 2>/dev/null
echo ""

# Check Redis
echo "--- Redis ---"
podman exec orchestrix-redis redis-cli -a <REDIS_PASSWORD> ping 2>/dev/null
echo ""

# Check PHP-FPM
echo "--- PHP-FPM ---"
podman exec orchestrix-php php -r "echo 'PHP OK';" 2>/dev/null
echo ""

# Check Nginx
echo "--- Nginx ---"
curl -s -o /dev/null -w "%{http_code}" http://localhost/
echo ""

# Check Laravel app
echo "--- Laravel App ---"
podman exec orchestrix-php php artisan about 2>/dev/null | head -5
echo ""
HEALTHEOF
chmod +x /opt/orchestrix/scripts/health-check.sh
```

---

## 14. Backup & Restore Database

### 14.1. Backup MySQL

```bash
# Backup toàn bộ database
podman exec orchestrix-mysql mysqldump \
  -u root \
  -p<ROOT_PASSWORD> \
  orchestrix > /opt/orchestrix/backups/orchestrix_$(date +%Y%m%d_%H%M%S).sql

# Hoặc backup ra file trên host
podman exec orchestrix-mysql mysqldump \
  -u root \
  -p<ROOT_PASSWORD> \
  orchestrix > /opt/orchestrix/backups/orchestrix_$(date +%Y%m%d_%H%M%S).sql

# Tạo script backup tự động
cat > /opt/orchestrix/scripts/backup.sh << 'BACKUPEOF'
#!/bin/bash
BACKUP_DIR="/opt/orchestrix/backups"
DATE=$(date +%Y%m%d_%H%M%S)
DB_NAME="orchestrix"
DB_USER="root"
DB_PASS="<ROOT_PASSWORD>"

# Tạo thư mục backup
mkdir -p $BACKUP_DIR

# Backup database
podman exec orchestrix-mysql mysqldump \
  -u $DB_USER -p$DB_PASS $DB_NAME \
  > $BACKUP_DIR/orchestrix_${DATE}.sql

# Nén file
gzip $BACKUP_DIR/orchestrix_${DATE}.sql

# Xóa backup cũ hơn 30 ngày
find $BACKUP_DIR -name "*.sql.gz" -mtime +30 -delete

echo "Backup completed: orchestrix_${DATE}.sql.gz"
BACKUPEOF
chmod +x /opt/orchestrix/scripts/backup.sh
```

### 14.2. Restore Database

```bash
# Restore từ backup
gunzip < /opt/orchestrix/backups/orchestrix_YYYYMMDD_HHMMSS.sql.gz | \
  podman exec -i orchestrix-mysql mysql \
  -u root -p<ROOT_PASSWORD> orchestrix
```

### 14.3. Cron backup (host machine)

```bash
# Thêm vào crontab host
crontab -e

# Backup mỗi ngày lúc 2:00 AM
0 2 * * * /opt/orchestrix/scripts/backup.sh >> /opt/orchestrix/logs/backup.log 2>&1
```

---

## 15. Triển khai cập nhật

### 15.1. Pull code mới

```bash
cd /path/to/orchestrix
git pull origin main
```

### 15.2. Update dependencies

```bash
# PHP dependencies
podman exec orchestrix-php composer install --no-dev --optimize-autoloader

# Frontend build
podman run --rm \
  -v /path/to/orchestrix:/var/www:Z \
  -w /var/www \
  node:20-alpine \
  sh -c "npm install && npm run build"
```

### 15.3. Run migrations

```bash
podman exec orchestrix-php php artisan migrate --force
```

### 15.4. Clear & rebuild caches

```bash
podman exec orchestrix-php php artisan config:clear
podman exec orchestrix-php php artisan route:clear
podman exec orchestrix-php php artisan view:clear
podman exec orchestrix-php php artisan event:clear

podman exec orchestrix-php php artisan config:cache
podman exec orchestrix-php php artisan route:cache
podman exec orchestrix-php php artisan view:cache
podman exec orchestrix-php php artisan event:cache
```

### 15.5. Restart services

```bash
podman restart orchestrix-nginx orchestrix-php orchestrix-queue orchestrix-scheduler
```

---

## 16. Khắc phục sự cố

### 16.1. Container không start được

```bash
# Xem log lỗi
podman logs orchestrix-php
podman logs orchestrix-mysql

# Kiểm tra port đang bị chiếm
ss -tlnp | grep -E ':80|:443|:3306|:6379|:9000'

# Kill process chiếm port
sudo kill $(sudo lsof -t -i:80)
```

### 16.2. PHP-FPM không kết nối được MySQL

```bash
# Kiểm tra MySQL đã sẵn sàng chưa
podman exec orchestrix-mysql mysqladmin ping -u root -p<ROOT_PASSWORD>

# Kiểm tra DNS resolution trong Podman network
podman exec orchestrix-php ping mysql

# Nếu không resolve được, thử dùng IP thay vì tên
podman inspect orchestrix-mysql --format '{{.NetworkSettings.Networks.orchestrix-net.IPAddress}}'
```

### 16.3. Permission issues

```bash
# Fix storage permissions
podman exec orchestrix-php chown -R www-data:www-data /var/www/storage
podman exec orchestrix-php chown -R www-data:www-data /var/www/bootstrap/cache
podman exec orchestrix-php chmod -R 775 /var/www/storage
podman exec orchestrix-php chmod -R 775 /var/www/bootstrap/cache
```

### 16.4. Redis connection refused

```bash
# Kiểm tra Redis password có đúng không
podman exec orchestrix-redis redis-cli -a <REDIS_PASSWORD> ping
# Should return: PONG

# Kiểm tra REDIS_HOST env trong container PHP
podman exec orchestrix-php env | grep REDIS
# REDIS_HOST phải là "redis" (tên container)
```

### 16.5. Nginx 502 Bad Gateway

```bash
# Kiểm tra PHP-FPM có chạy không
podman exec orchestrix-php ps aux | grep php-fpm

# Kiểm tra fastcgi_pass có đúng port không
podman exec orchestrix-nginx cat /etc/nginx/conf.d/app.conf | grep fastcgi_pass
# Phải là: fastcgi_pass php:9000;
```

---

## 17. Podman Compose file đầy đủ

> **Lưu ý:** `podman-compose` không hỗ trợ `build:` trực tiếp như Docker Compose. Cần build image trước.

### 17.1. File: `podman-compose.yml`

```yaml
version: '3.8'

services:
  # MySQL Database
  mysql:
    image: mysql:8.0
    container_name: orchestrix-mysql
    restart: unless-stopped
    environment:
      MYSQL_DATABASE: ${DB_DATABASE:-orchestrix}
      MYSQL_ROOT_PASSWORD: ${MYSQL_ROOT_PASSWORD:-rootpassword}
      MYSQL_USER: ${DB_USERNAME:-orchestrix_user}
      MYSQL_PASSWORD: ${DB_PASSWORD:-password}
    volumes:
      - mysql_data:/var/lib/mysql
      - ./init-db:/docker-entrypoint-initdb.d:Z
    networks:
      - orchestrix
    healthcheck:
      test: ["CMD", "mysqladmin", "ping", "-h", "localhost", "-u", "root", "-p${MYSQL_ROOT_PASSWORD:-rootpassword}"]
      interval: 10s
      timeout: 5s
      retries: 5

  # Redis
  redis:
    image: redis:alpine
    container_name: orchestrix-redis
    restart: unless-stopped
    command: redis-server --appendonly yes --requirepass ${REDIS_PASSWORD:-redispassword}
    volumes:
      - redis_data:/data
    networks:
      - orchestrix
    healthcheck:
      test: ["CMD", "redis-cli", "-a", "${REDIS_PASSWORD:-redispassword}", "ping"]
      interval: 10s
      timeout: 5s
      retries: 5

  # PHP-FPM
  php:
    image: orchestrix-php:latest
    container_name: orchestrix-php
    restart: unless-stopped
    volumes:
      - app_data:/var/www
    depends_on:
      mysql:
        condition: service_healthy
      redis:
        condition: service_healthy
    environment:
      - DB_HOST=mysql
      - DB_PORT=3306
      - DB_DATABASE=${DB_DATABASE:-orchestrix}
      - DB_USERNAME=${DB_USERNAME:-orchestrix_user}
      - DB_PASSWORD=${DB_PASSWORD:-password}
      - REDIS_HOST=redis
      - REDIS_PASSWORD=${REDIS_PASSWORD:-redispassword}
      - APP_ENV=${APP_ENV:-production}
      - APP_DEBUG=${APP_DEBUG:-false}
    networks:
      - orchestrix

  # Nginx Web Server
  nginx:
    image: nginx:alpine
    container_name: orchestrix-nginx
    restart: unless-stopped
    ports:
      - "${HTTP_PORT:-80}:80"
      - "${HTTPS_PORT:-443}:443"
    volumes:
      - app_data:/var/www
      - ./docker/nginx/conf.d:/etc/nginx/conf.d:Z
      - ./ssl:/etc/nginx/ssl:Z
    depends_on:
      - php
    networks:
      - orchestrix

  # Queue Worker
  queue:
    image: orchestrix-php:latest
    container_name: orchestrix-queue
    restart: unless-stopped
    command: php artisan queue:work redis --sleep=3 --tries=3 --max-time=3600
    volumes:
      - app_data:/var/www
    depends_on:
      php:
        condition: service_started
      redis:
        condition: service_healthy
    environment:
      - DB_HOST=mysql
      - DB_PORT=3306
      - DB_DATABASE=${DB_DATABASE:-orchestrix}
      - DB_USERNAME=${DB_USERNAME:-orchestrix_user}
      - DB_PASSWORD=${DB_PASSWORD:-password}
      - REDIS_HOST=redis
      - REDIS_PASSWORD=${REDIS_PASSWORD:-redispassword}
      - APP_ENV=${APP_ENV:-production}
    networks:
      - orchestrix

  # Scheduler
  scheduler:
    image: orchestrix-php:latest
    container_name: orchestrix-scheduler
    restart: unless-stopped
    command: >
      sh -c "while true; do php artisan schedule:run --verbose --no-interaction; sleep 60; done"
    volumes:
      - app_data:/var/www
    depends_on:
      php:
        condition: service_started
    environment:
      - DB_HOST=mysql
      - DB_PORT=3306
      - DB_DATABASE=${DB_DATABASE:-orchestrix}
      - DB_USERNAME=${DB_USERNAME:-orchestrix_user}
      - DB_PASSWORD=${DB_PASSWORD:-password}
      - REDIS_HOST=redis
      - REDIS_PASSWORD=${REDIS_PASSWORD:-redispassword}
      - APP_ENV=${APP_ENV:-production}
    networks:
      - orchestrix

volumes:
  mysql_data:
    driver: local
  redis_data:
    driver: local
  app_data:
    driver: local

networks:
  orchestrix:
    driver: bridge
```

### 17.2. File: `.env` (dùng cho podman-compose)

```env
# App
APP_ENV=production
APP_DEBUG=false

# MySQL
DB_DATABASE=orchestrix
DB_USERNAME=orchestrix_user
DB_PASSWORD=<TẠO_MẬT_KHẨU_MẠNH>
MYSQL_ROOT_PASSWORD=<TẠO_ROOT_PASSWORD_KHÁC>

# Redis
REDIS_PASSWORD=<TẠO_REDIS_PASSWORD>

# Ports
HTTP_PORT=80
HTTPS_PORT=443
```

### 17.3. Khởi chạy bằng podman-compose

```bash
cd /path/to/orchestrix

# 1. Build image trước
podman build -t orchestrix-php:latest .

# 2. Tạo init-db script
mkdir -p init-db
cat > init-db/init.sql << EOF
CREATE DATABASE IF NOT EXISTS ${DB_DATABASE:-orchestrix} CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
GRANT ALL PRIVILEGES ON ${DB_DATABASE:-orchestrix}.* TO '${DB_USERNAME:-orchestrix_user}'@'%';
FLUSH PRIVILEGES;
EOF

# 3. Khởi chạy tất cả
podman-compose up -d

# 4. Kiểm tra trạng thái
podman-compose ps

# 5. Khởi tạo Laravel
podman exec orchestrix-php php artisan key:generate
podman exec orchestrix-php php artisan migrate --force
podman exec orchestrix-php php artisan db:seed --force
podman exec orchestrix-php php artisan storage:link
podman exec orchestrix-php php artisan config:cache
podman exec orchestrix-php php artisan route:cache
podman exec orchestrix-php php artisan view:cache

# 6. Build frontend
podman run --rm \
  -v "$(pwd):/var/www:Z" \
  -w /var/www \
  node:20-alpine \
  sh -c "npm install && npm run build"

# 7. Fix permissions
podman exec orchestrix-php chown -R www-data:www-data /var/www/storage
podman exec orchestrix-php chown -R www-data:www-data /var/www/bootstrap/cache
```

---

## 18. Checklist trước khi deploy

```bash
# ===== PRE-DEPLOY CHECKLIST =====

# [ ] 1. Cài đặt Podman & podman-compose
podman --version
podman-compose --version

# [ ] 2. Clone code
cd /path/to/orchestrix
git status

# [ ] 3. Cấu hình .env
cp .env.example .env
nano .env
# Verify: APP_ENV=production, APP_DEBUG=false, DB_HOST=mysql

# [ ] 4. Tạo Podman network
podman network create orchestrix-net

# [ ] 5. Tạo thư mục volumes
mkdir -p /opt/orchestrix/{data/mysql,data/redis,backups,logs,ssl,init-db,secrets,scripts}

# [ ] 6. Build PHP-FPM image
podman build -t orchestrix-php:latest .

# [ ] 7. Pull images gốc
podman pull nginx:alpine
podman pull mysql:8.0
podman pull redis:alpine

# [ ] 8. Khởi chạy MySQL & Redis trước
podman-compose up -d mysql redis

# [ ] 9. Đợi MySQL ready
podman exec orchestrix-mysql mysqladmin ping -u root -p<ROOT_PASSWORD>

# [ ] 10. Khởi chạy tất cả
podman-compose up -d

# [ ] 11. Kiểm tra containers
podman ps -a --filter "name=orchestrix"

# [ ] 12. Khởi tạo Laravel
podman exec orchestrix-php php artisan key:generate
podman exec orchestrix-php php artisan migrate --force
podman exec orchestrix-php php artisan config:cache
podman exec orchestrix-php php artisan route:cache
podman exec orchestrix-php php artisan view:cache
podman exec orchestrix-php php artisan storage:link

# [ ] 13. Build frontend
podman run --rm -v "$(pwd):/var/www:Z" -w /var/www node:20-alpine sh -c "npm install && npm run build"

# [ ] 14. Fix permissions
podman exec orchestrix-php chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache

# [ ] 15. Test website
curl -I http://localhost
# Should return: HTTP/1.1 200 OK

# [ ] 16. Test API
curl http://localhost/api/health
# Should return: {"status":"ok"}

# [ ] 17. Setup backup script
chmod +x /opt/orchestrix/scripts/backup.sh
crontab -e
# Add: 0 2 * * * /opt/orchestrix/scripts/backup.sh >> /opt/orchestrix/logs/backup.log 2>&1

# [ ] 18. Setup firewall
sudo ufw allow 22/tcp
sudo ufw allow 80/tcp
sudo ufw allow 443/tcp
sudo ufw enable

# [ ] 19. Verify all services running
podman stats --no-stream
/opt/orchestrix/scripts/health-check.sh

# [ ] 20. Monitor logs
podman logs -f orchestrix-nginx
podman logs -f orchestrix-queue
```

---

## Lệnh nhanh (Quick Reference)

```bash
# ===== KHỞI CHẠY =====
podman-compose up -d                        # Khởi chạy tất cả
podman-compose up -d mysql redis            # Chỉ start DB & Cache
podman-compose down                         # Dừng tất cả
podman-compose down -v                      # Dừng và xóa volumes

# ===== QUẢN LÝ =====
podman ps -a --filter "name=orchestrix"    # Xem trạng thái
podman logs -f orchestrix-nginx             # Xem log Nginx
podman logs -f orchestrix-queue             # Xem log Queue
podman restart orchestrix-php               # Restart PHP
podman exec -it orchestrix-php bash         # Vào container PHP

# ===== LARAVEL =====
podman exec orchestrix-php php artisan migrate --force
podman exec orchestrix-php php artisan config:cache
podman exec orchestrix-php php artisan route:cache
podman exec orchestrix-php php artisan view:cache
podman exec orchestrix-php php artisan queue:restart

# ===== MONITORING =====
podman stats --no-stream                    # Resource usage
podman inspect orchestrix-mysql             # Chi tiết container
/opt/orchestrix/scripts/health-check.sh     # Health check

# ===== BACKUP =====
/opt/orchestrix/scripts/backup.sh           # Backup database
gunzip < backup.sql.gz | podman exec -i orchestrix-mysql mysql -u root -pXXX orchestrix  # Restore

# ===== CLEANUP =====
podman system prune -af                     # Xóa unused images/containers
podman volume prune                         # Xóa unused volumes
```

---

> **Lưu ý quan trọng khi dùng Podman thay Docker:**
> - Podman chạy rootless (an toàn hơn) nhưng có thể gặp vấn đề với port < 1024
> - Dùng `:Z` hoặc `:z` flag trên volumes để SELinux không block
> - `podman-compose` hoạt động tương tự `docker-compose` nhưng có vài khác biệt nhỏ
> - Nếu gặp lỗi DNS, thêm `--network=orchestrix-net` vào mọi container
