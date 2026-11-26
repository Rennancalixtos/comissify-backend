#!/bin/bash
# Sobe o Comissify em modo demonstração com um banco SQLite novo a cada boot.
set -e
cd /var/www

export DEMO_MODE=true
export DB_CONNECTION=sqlite
export DB_DATABASE=/var/www/database/database.sqlite
export CACHE_STORE=file
export SESSION_DRIVER=file
export QUEUE_CONNECTION=sync
export MAIL_MAILER=log
export LOG_CHANNEL=stderr
export APP_ENV=${APP_ENV:-production}
export APP_DEBUG=${APP_DEBUG:-false}

# Chave nova a cada boot: o banco também é recriado, então nada se perde.
if [ -z "$APP_KEY" ]; then
    export APP_KEY="base64:$(head -c 32 /dev/urandom | base64)"
fi
# Admin criado pelo seeder padrão nunca fica com a senha de exemplo.
export ADMIN_PASSWORD=${ADMIN_PASSWORD:-$(head -c 24 /dev/urandom | base64)}

echo "🔌 Nginx na porta ${PORT}"
sed -i "s/listen 80;/listen ${PORT};/" /etc/nginx/conf.d/default.conf

echo "⚡ Cacheando config e rotas..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "🗄️  Criando banco de demonstração..."
rm -f "$DB_DATABASE" && touch "$DB_DATABASE"
php artisan migrate:fresh --force --seed --seeder=DemoSeeder

chown -R www-data:www-data storage bootstrap/cache database

echo "🚀 Comissify (demo) no ar."
exec /usr/bin/supervisord -c /etc/supervisor/supervisord.conf
