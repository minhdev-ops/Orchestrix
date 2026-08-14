#!/bin/bash
set -euo pipefail

# =====================================================
# Deploy both Orchestrix + JakartaEE to production server
# Usage: ./scripts/deploy-local.sh
# =====================================================

SSH_HOST="root@103.15.222.128"
DEPLOY_DIR="/opt/agriverse"
ORCHESTRIX_DIR="/home/wanmin/1.Work/03.Project/2026/Orchestrix"
JAKARTA_DIR="/home/wanmin/1.Work/03.Project/2026/03.JakartaEE/JakartaEE"

echo "=== 1. Build JakartaEE WAR ==="
cd "$JAKARTA_DIR"
./mvnw clean package -DskipTests
echo "  WAR: target/JakartaEE-1.0-SNAPSHOT.war"

echo "=== 2. Prepare deploy structure ==="
mkdir -p /tmp/agriverse-deploy/jakarta
cp -r "$JAKARTA_DIR/deploy/"* /tmp/agriverse-deploy/jakarta/
cp "$JAKARTA_DIR/target/JakartaEE-1.0-SNAPSHOT.war" /tmp/agriverse-deploy/jakarta/target/

echo "=== 3. Build Orchestrix frontend ==="
cd "$ORCHESTRIX_DIR"
npm ci && npm run build

echo "=== 4. Create remote directories ==="
ssh "$SSH_HOST" "mkdir -p $DEPLOY_DIR/docker/nginx/conf.d $DEPLOY_DIR/docker/ssl $DEPLOY_DIR/docker/mysql/conf.d"

echo "=== 5. Upload Orchestrix code ==="
rsync -avz --progress --delete \
  --exclude='.git' \
  --exclude='node_modules' \
  --exclude='vendor' \
  --exclude='storage/framework/cache/data' \
  --exclude='storage/logs' \
  --exclude='.env' \
  "$ORCHESTRIX_DIR/" "$SSH_HOST:$DEPLOY_DIR/orchestrix/"

echo "=== 6. Upload docker-compose & nginx config ==="
scp "$ORCHESTRIX_DIR/docker-compose.yml" "$SSH_HOST:$DEPLOY_DIR/"
scp "$ORCHESTRIX_DIR/docker/nginx/conf.d/app.conf" "$SSH_HOST:$DEPLOY_DIR/docker/nginx/conf.d/"

echo "=== 7. Upload JakartaEE deploy files ==="
rsync -avz --progress /tmp/agriverse-deploy/jakarta/ "$SSH_HOST:$DEPLOY_DIR/jakarta/"

echo "=== 8. SSH and deploy ==="
ssh "$SSH_HOST" bash -s << 'REMOTESCR'
  set -euo pipefail
  cd /opt/agriverse

  # Create .env if not exists
  if [ ! -f .env ]; then
    cat > .env << 'ENVEOF'
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
JAKARTA_PATH=/opt/agriverse/jakarta
ENVEOF
    chmod 600 .env
    echo "  Created .env"
  fi

  # Build and start all services
  echo "  Starting Docker services..."
  docker compose down --remove-orphans || true
  docker compose up -d --build

  # Wait for services
  echo "  Waiting for MySQL..."
  sleep 10

  # Run Laravel setup
  echo "  Running migrations..."
  docker compose exec -T php php artisan migrate --force || echo "  Migrations may have already run"

  echo "  Generating Passport keys..."
  docker compose exec -T php php artisan passport:keys --force || true

  echo "  Optimizing..."
  docker compose exec -T php php artisan optimize || true

  echo "  Storage link..."
  docker compose exec -T php php artisan storage:link || true

  echo ""
  echo "=== Deploy complete! ==="
  echo "  Laravel: https://agriverse.slink.id.vn"
  echo "  Jakarta: https://agriverse.slink.id.vn/jakarta/"
  echo "  Check: docker compose ps"
REMOTESCR

rm -rf /tmp/agriverse-deploy
echo "Done!"
