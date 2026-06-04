#!/bin/bash
set -e

echo "========================================="
echo "  Control de Accesos — Iniciando..."
echo "========================================="

# ── .env ───────────────────────────────────────
if [ ! -f .env ]; then
    cp .env.example .env
fi

# ── Configure .env for MySQL ───────────────────
sed -i "s/^DB_CONNECTION=.*/DB_CONNECTION=mysql/" .env
sed -i "s/^#\? *DB_HOST=.*/DB_HOST=${DB_HOST}/" .env
sed -i "s/^#\? *DB_PORT=.*/DB_PORT=${DB_PORT}/" .env
sed -i "s/^#\? *DB_DATABASE=.*/DB_DATABASE=${DB_DATABASE}/" .env
sed -i "s/^#\? *DB_USERNAME=.*/DB_USERNAME=${DB_USERNAME}/" .env
sed -i "s/^#\? *DB_PASSWORD=.*/DB_PASSWORD=${DB_PASSWORD}/" .env

# ── Generate APP_KEY ──────────────────────────
php artisan key:generate --force --ansi

# ── Wait for MySQL ────────────────────────────
echo "Esperando a que MySQL este listo..."
until php -r "new PDO('mysql:host=${DB_HOST};port=${DB_PORT}', '${DB_USERNAME}', '${DB_PASSWORD}');" 2>/dev/null; do
    sleep 2
done
echo "MySQL listo!"

# ── Laravel setup ──────────────────────────────
php artisan storage:link --force --ansi 2>/dev/null || true
php artisan migrate --force --ansi
php artisan config:clear --ansi 2>/dev/null || true
php artisan view:clear --ansi 2>/dev/null || true

# ── Fix permissions ────────────────────────────
chown -R www-data:www-data storage bootstrap/cache

echo "========================================="
echo "  Listo! http://localhost:8000"
echo "========================================="

exec "$@"
