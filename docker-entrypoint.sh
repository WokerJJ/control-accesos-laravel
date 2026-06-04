#!/bin/bash
set -e

echo "========================================="
echo "  Control de Accesos — Iniciando..."
echo "========================================="

# ── .env ───────────────────────────────────────
if [ ! -f .env ]; then
    cp .env.example .env
    php artisan key:generate --force --ansi
fi

# ── SQLite ─────────────────────────────────────
touch database/database.sqlite
chown www-data:www-data database/database.sqlite

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
