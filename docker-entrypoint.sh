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
sed -Ei "s/^#?[[:space:]]*DB_HOST=.*/DB_HOST=${DB_HOST}/" .env
sed -Ei "s/^#?[[:space:]]*DB_PORT=.*/DB_PORT=${DB_PORT}/" .env
sed -Ei "s/^#?[[:space:]]*DB_DATABASE=.*/DB_DATABASE=${DB_DATABASE}/" .env
sed -Ei "s/^#?[[:space:]]*DB_USERNAME=.*/DB_USERNAME=${DB_USERNAME}/" .env
sed -Ei "s/^#?[[:space:]]*DB_PASSWORD=.*/DB_PASSWORD=${DB_PASSWORD}/" .env

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

# Only run migrations if the database is empty (first run)
TABLE_EXISTS=$(php artisan tinker --execute="echo Schema::hasTable('accesos') ? 'yes' : 'no';" 2>/dev/null || echo "no")
if [ "$TABLE_EXISTS" = "yes" ]; then
    echo "Base de datos ya inicializada, saltando migraciones"
else
    echo "Base de datos nueva, ejecutando migraciones..."
    php artisan migrate --force --ansi
fi

php artisan config:clear --ansi 2>/dev/null || true
php artisan view:clear --ansi 2>/dev/null || true

# ── Fix permissions ────────────────────────────
chown -R www-data:www-data storage bootstrap/cache

echo "========================================="
echo "  Listo! http://localhost:8000"
echo "========================================="

exec "$@"
