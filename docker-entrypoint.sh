#!/bin/bash
set -e

echo "========================================="
echo "  Control de Accesos — Iniciando..."
echo "========================================="

# ── Create .env directly (no sed, no fragility) ──
if [ ! -f .env ]; then
    echo "Creando .env..."
    APP_KEY=$(php -r "echo 'base64:' . base64_encode(random_bytes(32));")

    cat > .env <<EOF
APP_NAME="Control de Accesos"
APP_ENV=local
APP_KEY=${APP_KEY}
APP_DEBUG=true
APP_URL=http://localhost:8000

DB_CONNECTION=mysql
DB_HOST=${DB_HOST:-db}
DB_PORT=${DB_PORT:-3306}
DB_DATABASE=${DB_DATABASE:-control_accesos}
DB_USERNAME=${DB_USERNAME:-control_accesos}
DB_PASSWORD=${DB_PASSWORD:-secret}

SESSION_DRIVER=database
SESSION_LIFETIME=120
CACHE_STORE=database
QUEUE_CONNECTION=database
EOF
    echo ".env creado!"
else
    # Ensure APP_KEY exists
    grep -q "APP_KEY=base64:" .env || php artisan key:generate --force --ansi
fi

# ── Wait for MySQL ────────────────────────────
echo "Esperando a que MySQL este listo..."
until php -r "new PDO('mysql:host=${DB_HOST:-db};port=${DB_PORT:-3306}', '${DB_USERNAME:-control_accesos}', '${DB_PASSWORD:-secret}');" 2>/dev/null; do
    sleep 2
done
echo "MySQL listo!"

# ── Wipe DB and run fresh migrations ──────────
echo "Limpiando y recreando base de datos..."
php artisan migrate:fresh --force --ansi

# ── Laravel setup ──────────────────────────────
php artisan storage:link --force --ansi 2>/dev/null || true
php artisan config:clear --ansi 2>/dev/null || true
php artisan view:clear --ansi 2>/dev/null || true

# ── Fix permissions ────────────────────────────
chown -R www-data:www-data storage bootstrap/cache

echo "========================================="
echo "  Listo! http://localhost:8000"
echo "========================================="

exec "$@"
