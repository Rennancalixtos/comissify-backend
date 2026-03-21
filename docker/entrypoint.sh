#!/bin/bash
set -e

cd /var/www

echo "⏳ Aguardando banco de dados..."
until php -r "
    try {
        new PDO(
            'mysql:host=' . getenv('DB_HOST') . ';port=' . (getenv('DB_PORT') ?: 3306) . ';dbname=' . getenv('DB_DATABASE'),
            getenv('DB_USERNAME'),
            getenv('DB_PASSWORD')
        );
        echo 'ok';
    } catch (Exception \$e) {
        exit(1);
    }
" 2>/dev/null | grep -q ok; do
    sleep 2
done

echo "✅ Banco disponível."

echo "🔄 Rodando migrations..."
php artisan migrate --force

echo "🔗 Criando symlink de storage..."
php artisan storage:link --force

echo "⚡ Otimizando..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "🔒 Ajustando permissões..."
chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache
chmod -R 775 /var/www/storage /var/www/bootstrap/cache

echo "🖼️  Gerando WebP responsivos da landing..."
php artisan landing:generate-webp

echo "🚀 Iniciando serviços..."
exec /usr/bin/supervisord -c /etc/supervisor/supervisord.conf
