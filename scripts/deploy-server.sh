#!/bin/bash
set -euo pipefail

# =============================================
# AgriVerse Server Setup Script
# Target: root@103.15.222.128
# Domain: agriverse.slink.id.vn
# =============================================

DOMAIN="agriverse.slink.id.vn"
DEPLOY_DIR="/opt/agriverse"

echo "=== Installing Docker ==="
apt-get update
apt-get install -y ca-certificates curl
install -m 0755 -d /etc/apt/keyrings
curl -fsSL https://download.docker.com/linux/ubuntu/gpg -o /etc/apt/keyrings/docker.asc
chmod a+r /etc/apt/keyrings/docker.asc
echo "deb [arch=$(dpkg --print-architecture) signed-by=/etc/apt/keyrings/docker.asc] https://download.docker.com/linux/ubuntu $(. /etc/os-release && echo "$VERSION_CODENAME") stable" | tee /etc/apt/sources.list.d/docker.list > /dev/null
apt-get update
apt-get install -y docker-ce docker-ce-cli containerd.io docker-compose-plugin
systemctl enable docker && systemctl start docker

echo "=== Installing Nginx (for initial SSL) ==="
apt-get install -y nginx
systemctl stop nginx || true

echo "=== Obtaining SSL Certificate ==="
apt-get install -y certbot python3-certbot-nginx
certbot certonly --standalone -d "$DOMAIN" --non-interactive --agree-tos -m "admin@$DOMAIN" || {
    echo "SSL cert failed. Create self-signed for now."
    mkdir -p /etc/nginx/ssl
    openssl req -x509 -nodes -days 365 -newkey rsa:2048 \
        -keyout /etc/nginx/ssl/privkey.pem \
        -out /etc/nginx/ssl/fullchain.pem \
        -subj "/CN=$DOMAIN"
}

echo "=== Creating deploy directory ==="
mkdir -p "$DEPLOY_DIR"
mkdir -p "$DEPLOY_DIR/docker/nginx/conf.d"
mkdir -p "$DEPLOY_DIR/docker/ssl"

# Copy SSL certs
cp /etc/letsencrypt/live/$DOMAIN/fullchain.pem "$DEPLOY_DIR/docker/ssl/" 2>/dev/null || true
cp /etc/letsencrypt/live/$DOMAIN/privkey.pem "$DEPLOY_DIR/docker/ssl/" 2>/dev/null || true
cp /etc/nginx/ssl/fullchain.pem "$DEPLOY_DIR/docker/ssl/" 2>/dev/null || true
cp /etc/nginx/ssl/privkey.pem "$DEPLOY_DIR/docker/ssl/" 2>/dev/null || true

# Create .env template
cat > "$DEPLOY_DIR/.env" << 'ENVEOF'
APP_ENV=production
APP_DEBUG=false
APP_URL=https://agriverse.slink.id.vn
DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=agriverse
DB_USERNAME=agriverse
DB_PASSWORD=AgriVerse2024!
DB_ROOT_PASSWORD=RootPass2024!
REDIS_HOST=redis
REDIS_PASSWORD=
PASSPORT_TOKEN_EXPIRATION=365
ENVEOF

chmod 600 "$DEPLOY_DIR/.env"

echo "=== Setting up SSL auto-renewal ==="
cat > /etc/cron.d/certbot-renew << 'CRONEOF'
0 3 * * * root certbot renew --quiet --post-hook "docker exec orchestrix-nginx nginx -s reload"
CRONEOF

echo "=== Done ==="
echo ""
echo "Next steps:"
echo "1. Upload code: cd /opt/agriverse && git clone <your-repo> orchestrix"
echo "2. Or copy files from local machine"
echo "3. Run: docker compose up -d --build"
echo "4. Run: docker compose exec php php artisan migrate --force"
echo "5. Run: docker compose exec php php artisan passport:keys --force"
echo "6. Run: docker compose exec php php artisan db:seed --force"
