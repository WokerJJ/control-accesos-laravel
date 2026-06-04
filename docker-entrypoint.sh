#!/bin/bash
set -e

echo "═══════════════════════════════════════════════════"
echo "  Control de Accesos — Iniciando..."
echo "═══════════════════════════════════════════════════"

# ── Create .env if missing ──────────────────────────────────
if [ ! -f /var/www/html/.env ]; then
    echo "▸ Creando .env desde .env.example..."
    cp /var/www/html/.env.example /var/www/html/.env
    php artisan key:generate --ansi
fi

# ── Configure .env for MySQL if DB_CONNECTION=mysql ─────────
if [ "$DB_CONNECTION" = "mysql" ]; then
    echo "▸ Configurando conexión MySQL..."
    sed -i "s/^DB_CONNECTION=.*/DB_CONNECTION=mysql/" /var/www/html/.env
    sed -i "s/^# DB_HOST=.*/DB_HOST=mysql/" /var/www/html/.env 2>/dev/null || true
    sed -i "s/^# DB_PORT=.*/DB_PORT=3306/" /var/www/html/.env 2>/dev/null || true
    sed -i "s/^# DB_DATABASE=.*/DB_DATABASE=$DB_DATABASE/" /var/www/html/.env 2>/dev/null || true
    sed -i "s/^# DB_USERNAME=.*/DB_USERNAME=$DB_USERNAME/" /var/www/html/.env 2>/dev/null || true
    sed -i "s/^# DB_PASSWORD=.*/DB_PASSWORD=$DB_PASSWORD/" /var/www/html/.env 2>/dev/null || true

    # Wait for MySQL to be ready
    echo "▸ Esperando a que MySQL esté listo..."
    until php -r "new PDO('mysql:host=mysql;port=3306', '$DB_USERNAME', '$DB_PASSWORD');" 2>/dev/null; do
        echo "  MySQL no disponible aún, reintentando en 3s..."
        sleep 3
    done
    echo "  ✓ MySQL listo"
else
    echo "▸ Usando SQLite..."
    touch /var/www/html/database/database.sqlite
    chown www-data:www-data /var/www/html/database/database.sqlite
fi

# ── Install dependencies if vendor is missing ───────────────
if [ ! -d /var/www/html/vendor ]; then
    echo "▸ Instalando dependencias PHP..."
    composer install --no-dev --optimize-autoloader --no-interaction
fi

if [ ! -d /var/www/html/node_modules ]; then
    echo "▸ Instalando dependencias Node..."
    npm ci --ignore-scripts
fi

# ── Build frontend assets if missing ────────────────────────
if [ ! -d /var/www/html/public/build ]; then
    echo "▸ Compilando assets frontend..."
    npm run build
fi

# ── Copy AdminLTE assets if missing ─────────────────────────
if [ ! -d /var/www/html/public/adminlte/dist ]; then
    echo "▸ Copiando AdminLTE..."
    if [ -d /var/www/html/node_modules/admin-lte/dist ]; then
        mkdir -p /var/www/html/public/adminlte/dist
        cp -r /var/www/html/node_modules/admin-lte/dist/* /var/www/html/public/adminlte/dist/
    fi
fi

if [ ! -d /var/www/html/public/adminlte/plugins ]; then
    mkdir -p /var/www/html/public/adminlte/plugins
    # Copy jQuery
    [ -d /var/www/html/node_modules/jquery/dist ] && \
        cp -r /var/www/html/node_modules/jquery/dist /var/www/html/public/adminlte/plugins/jquery 2>/dev/null || true
    # Copy Bootstrap
    [ -d /var/www/html/node_modules/bootstrap/dist ] && \
        cp -r /var/www/html/node_modules/bootstrap/dist /var/www/html/public/adminlte/plugins/bootstrap 2>/dev/null || true
    # Copy FontAwesome
    [ -d /var/www/html/node_modules/@fortawesome/fontawesome-free ] && \
        cp -r /var/www/html/node_modules/@fortawesome/fontawesome-free /var/www/html/public/adminlte/plugins/fontawesome-free 2>/dev/null || true
fi

# ── Laravel setup ───────────────────────────────────────────
echo "▸ Generando APP_KEY..."
php artisan key:generate --force --ansi 2>/dev/null || true

echo "▸ Creando storage:link..."
php artisan storage:link --force --ansi 2>/dev/null || true

echo "▸ Limpiando caché..."
php artisan config:clear --ansi 2>/dev/null || true
php artisan route:clear --ansi 2>/dev/null || true
php artisan view:clear --ansi 2>/dev/null || true

echo "▸ Ejecutando migraciones..."
php artisan migrate --force --ansi

# ── Seed database (only if tables are empty) ────────────────
SEED_COUNT=$(php artisan tinker --execute="echo \DB::table('usuarios')->count();" 2>/dev/null || echo "0")
if [ "$SEED_COUNT" = "0" ]; then
    echo "▸ Sembrando base de datos..."
    php artisan db:seed --force --ansi
fi

echo "▸ Optimizando..."
php artisan config:cache --ansi 2>/dev/null || true
php artisan route:cache --ansi 2>/dev/null || true
php artisan view:cache --ansi 2>/dev/null || true

# ── Fix permissions ─────────────────────────────────────────
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

echo "═══════════════════════════════════════════════════"
echo "  ✓ Control de Accesos listo!"
echo "  → http://localhost:${APP_PORT:-8000}"
echo "═══════════════════════════════════════════════════"

exec "$@"
