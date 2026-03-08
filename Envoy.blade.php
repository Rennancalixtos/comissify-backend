{{--
 | Comissify — Envoy Deploy
 |
 | Uso:
 |   envoy run setup    → primeira vez no servidor (nginx, symlinks, permissões)
 |   envoy run deploy   → deploy normal (pull + build + migrate + restart)
 |   envoy run rollback → volta para o commit anterior
 |
 | Variáveis de ambiente (opcional, sobrescrever via --set):
 |   envoy run deploy --set="branch=main,php=php8.3"
--}}

@servers(['production' => ['deploy@SEU_IP_AQUI']])

@setup
    $app      = 'comissify';
    $repo     = 'git@github.com:SEU_USUARIO/SEU_REPO.git';
    $branch   = $branch ?? 'main';
    $base     = '/var/www/' . $app;
    $releases = $base . '/releases';
    $current  = $base . '/current';
    $shared   = $base . '/shared';
    $php      = $php ?? 'php8.3';
    $now      = date('YmdHis');
    $release  = $releases . '/' . $now;
@endsetup

{{-- ══════════════════════════════════════════════════════════════════════════ --}}
{{-- SETUP — rodar apenas na primeira vez                                       --}}
{{-- ══════════════════════════════════════════════════════════════════════════ --}}

@story('setup')
    setup:dirs
    setup:clone
    setup:env
    setup:symlinks
    setup:npm
    setup:composer
    setup:permissions
    setup:nginx
    setup:scheduler
    setup:horizon
@endstory

@task('setup:dirs', ['on' => 'production'])
    echo ">>> Criando estrutura de diretórios..."
    mkdir -p {{ $releases }}
    mkdir -p {{ $shared }}/storage/app/public
    mkdir -p {{ $shared }}/storage/logs
    mkdir -p {{ $shared }}/storage/framework/{sessions,views,cache}
    mkdir -p {{ $shared }}/storage/app/videos
    mkdir -p {{ $shared }}/storage/app/public/landing
@endtask

@task('setup:clone', ['on' => 'production'])
    echo ">>> Clonando repositório..."
    git clone --depth=1 --branch={{ $branch }} {{ $repo }} {{ $release }}
@endtask

@task('setup:env', ['on' => 'production'])
    echo ">>> Configurando .env..."
    echo "ATENÇÃO: copie o .env manualmente para {{ $shared }}/.env"
    echo "         cp /tmp/.env {{ $shared }}/.env"
    echo "         (ou edite agora: nano {{ $shared }}/.env)"
@endtask

@task('setup:symlinks', ['on' => 'production'])
    echo ">>> Criando symlinks shared..."
    ln -sfn {{ $shared }}/storage {{ $release }}/storage
    ln -sfn {{ $shared }}/.env     {{ $release }}/.env
    ln -sfn {{ $release }}         {{ $current }}
    cd {{ $current }} && {{ $php }} artisan storage:link
@endtask

@task('setup:composer', ['on' => 'production'])
    echo ">>> Instalando dependências Composer..."
    cd {{ $current }} && composer install --no-dev --optimize-autoloader --no-interaction
@endtask

@task('setup:npm', ['on' => 'production'])
    echo ">>> Build assets..."
    cd {{ $current }} && npm ci && npm run build
@endtask

@task('setup:permissions', ['on' => 'production'])
    echo ">>> Ajustando permissões..."
    chown -R www-data:www-data {{ $shared }}/storage
    chmod -R 775 {{ $shared }}/storage
    chown -R www-data:www-data {{ $current }}/bootstrap/cache
    chmod -R 775 {{ $current }}/bootstrap/cache
@endtask

@task('setup:nginx', ['on' => 'production'])
    echo ">>> Criando config Nginx..."
    cat > /etc/nginx/sites-available/{{ $app }} << 'NGINX'
server {
    listen 80;
    listen [::]:80;
    server_name SEU_DOMINIO.COM;
    root /var/www/comissify/current/public;
    index index.php;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.3-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
        fastcgi_hide_header X-Powered-By;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }

    client_max_body_size 20M;
}
NGINX

    ln -sfn /etc/nginx/sites-available/{{ $app }} /etc/nginx/sites-enabled/{{ $app }}
    nginx -t && systemctl reload nginx
    echo ">>> Nginx configurado. Rode: certbot --nginx -d SEU_DOMINIO.COM para SSL"
@endtask

@task('setup:scheduler', ['on' => 'production'])
    echo ">>> Adicionando cron do scheduler Laravel..."
    (crontab -l 2>/dev/null; echo "* * * * * www-data cd {{ $current }} && {{ $php }} artisan schedule:run >> /dev/null 2>&1") | crontab -
@endtask

@task('setup:horizon', ['on' => 'production'])
    echo ">>> Criando serviço Supervisor para Horizon..."
    cat > /etc/supervisor/conf.d/{{ $app }}-horizon.conf << 'SUPERVISOR'
[program:comissify-horizon]
process_name=%(program_name)s
command=php8.3 /var/www/comissify/current/artisan horizon
autostart=true
autorestart=true
user=www-data
redirect_stderr=true
stdout_logfile=/var/www/comissify/shared/storage/logs/horizon.log
stopwaitsecs=3600
SUPERVISOR

    supervisorctl reread
    supervisorctl update
    supervisorctl start {{ $app }}-horizon
@endtask


{{-- ══════════════════════════════════════════════════════════════════════════ --}}
{{-- DEPLOY — uso cotidiano                                                     --}}
{{-- ══════════════════════════════════════════════════════════════════════════ --}}

@story('deploy')
    deploy:clone
    deploy:symlinks
    deploy:composer
    deploy:npm
    deploy:migrate
    deploy:cache
    deploy:activate
    deploy:horizon
    deploy:cleanup
@endstory

@task('deploy:clone', ['on' => 'production'])
    echo ">>> Clonando release {{ $now }}..."
    git clone --depth=1 --branch={{ $branch }} {{ $repo }} {{ $release }}
@endtask

@task('deploy:symlinks', ['on' => 'production'])
    echo ">>> Linkando storage e .env..."
    ln -sfn {{ $shared }}/storage {{ $release }}/storage
    ln -sfn {{ $shared }}/.env     {{ $release }}/.env
@endtask

@task('deploy:composer', ['on' => 'production'])
    echo ">>> Composer install..."
    cd {{ $release }} && composer install --no-dev --optimize-autoloader --no-interaction
@endtask

@task('deploy:npm', ['on' => 'production'])
    echo ">>> npm build..."
    cd {{ $release }} && npm ci && npm run build
@endtask

@task('deploy:migrate', ['on' => 'production'])
    echo ">>> Migrações..."
    cd {{ $release }} && {{ $php }} artisan migrate --force
@endtask

@task('deploy:cache', ['on' => 'production'])
    echo ">>> Rebuilding caches..."
    cd {{ $release }} && {{ $php }} artisan config:cache
    cd {{ $release }} && {{ $php }} artisan route:cache
    cd {{ $release }} && {{ $php }} artisan view:cache
    cd {{ $release }} && {{ $php }} artisan event:cache
@endtask

@task('deploy:activate', ['on' => 'production'])
    echo ">>> Ativando release..."
    ln -sfn {{ $release }} {{ $current }}
    echo ">>> Storage link..."
    cd {{ $current }} && {{ $php }} artisan storage:link --force 2>/dev/null || true
    echo ">>> Permissões..."
    chown -R www-data:www-data {{ $shared }}/storage
    chmod -R 775 {{ $shared }}/storage
@endtask

@task('deploy:horizon', ['on' => 'production'])
    echo ">>> Reiniciando Horizon..."
    cd {{ $current }} && {{ $php }} artisan horizon:terminate
    supervisorctl restart {{ $app }}-horizon
@endtask

@task('deploy:cleanup', ['on' => 'production'])
    echo ">>> Removendo releases antigas (mantendo últimas 5)..."
    ls -dt {{ $releases }}/*/ | tail -n +6 | xargs rm -rf
    echo ">>> Deploy concluído: {{ $now }}"
@endtask


{{-- ══════════════════════════════════════════════════════════════════════════ --}}
{{-- ROLLBACK — volta para a release anterior                                   --}}
{{-- ══════════════════════════════════════════════════════════════════════════ --}}

@story('rollback')
    rollback:switch
    rollback:horizon
@endstory

@task('rollback:switch', ['on' => 'production'])
    echo ">>> Rollback..."
    @php
        // Pega a segunda release mais recente
    @endphp
    PREV=$(ls -dt {{ $releases }}/*/ | sed -n '2p' | sed 's:/$::')
    if [ -z "$PREV" ]; then
        echo "Nenhuma release anterior encontrada."
        exit 1
    fi
    echo "Voltando para: $PREV"
    ln -sfn $PREV {{ $current }}
@endtask

@task('rollback:horizon', ['on' => 'production'])
    cd {{ $current }} && {{ $php }} artisan horizon:terminate
    supervisorctl restart {{ $app }}-horizon
    echo ">>> Rollback concluído."
@endtask
