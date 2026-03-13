#!/usr/bin/env bash
# =============================================================================
# setup-oracle.sh — Instalação em produção Oracle Cloud Free Tier (ARM Ubuntu)
# Uso: sudo bash setup-oracle.sh
# =============================================================================
set -euo pipefail

export DEBIAN_FRONTEND=noninteractive
export NEEDRESTART_MODE=a

PROJECT_DIR="$(cd "$(dirname "$0")" && pwd)"
REAL_USER="${SUDO_USER:-$USER}"
DB_NAME="comissify"
DB_USER="comissify"
DB_PASS="$(openssl rand -base64 16)"
DB_ROOT_PASS="$(openssl rand -base64 16)"
REDIS_PASS="$(openssl rand -base64 16)"
SERVER_IP="$(curl -s --max-time 5 ifconfig.me || hostname -I | awk '{print $1}')"
APP_URL="http://${SERVER_IP}"

RED='\033[0;31m'; GREEN='\033[0;32m'; YELLOW='\033[1;33m'; BLUE='\033[0;34m'; NC='\033[0m'
info()  { echo -e "${BLUE}[INFO]${NC}  $*"; }
ok()    { echo -e "${GREEN}[OK]${NC}    $*"; }
warn()  { echo -e "${YELLOW}[WARN]${NC}  $*"; }
die()   { echo -e "${RED}[ERRO]${NC}  $*" >&2; exit 1; }

[ "$(id -u)" -eq 0 ] || die "Execute com sudo: sudo bash $0"

echo ""
echo "============================================================"
echo "  Comissify — Setup Oracle Cloud (ARM Ubuntu)"
echo "  Projeto: $PROJECT_DIR"
echo "  IP:      $SERVER_IP"
echo "============================================================"
echo ""

# ─── 1. Sistema ───────────────────────────────────────────────────────────────
info "Atualizando sistema..."
apt-get update -qq
apt-get upgrade -y -qq
apt-get install -y -qq curl ca-certificates gnupg unzip git software-properties-common

# ─── 2. PHP 8.3 ───────────────────────────────────────────────────────────────
if php8.3 --version &>/dev/null; then
    ok "PHP 8.3 já instalado"
else
    info "Instalando PHP 8.3..."
    LC_ALL=C.UTF-8 add-apt-repository -y ppa:ondrej/php
    apt-get update -qq
    apt-get install -y -qq \
        php8.3-fpm php8.3-cli \
        php8.3-mysql php8.3-redis php8.3-mbstring php8.3-xml \
        php8.3-curl php8.3-zip php8.3-gd php8.3-bcmath php8.3-intl
    ok "PHP 8.3 instalado"
fi
update-alternatives --set php /usr/bin/php8.3 2>/dev/null || true

# ─── 3. Nginx ─────────────────────────────────────────────────────────────────
if nginx -v &>/dev/null; then
    ok "Nginx já instalado"
else
    info "Instalando Nginx..."
    apt-get install -y -qq nginx
    ok "Nginx instalado"
fi

# ─── 4. Composer ──────────────────────────────────────────────────────────────
if composer --version &>/dev/null; then
    ok "Composer já instalado"
else
    info "Instalando Composer..."
    curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
    ok "Composer instalado"
fi

# ─── 5. Node.js 20 ────────────────────────────────────────────────────────────
if node --version &>/dev/null; then
    ok "Node.js já instalado: $(node --version)"
else
    info "Instalando Node.js 20 LTS..."
    mkdir -p /etc/apt/keyrings
    curl -fsSL https://deb.nodesource.com/gpgkey/nodesource-repo.gpg.key \
        | gpg --dearmor -o /etc/apt/keyrings/nodesource.gpg
    echo "deb [signed-by=/etc/apt/keyrings/nodesource.gpg] https://deb.nodesource.com/node_20.x nodistro main" \
        > /etc/apt/sources.list.d/nodesource.list
    apt-get update -qq
    apt-get install -y -qq nodejs
    ok "Node.js $(node --version)"
fi

# ─── 6. MySQL 8 ───────────────────────────────────────────────────────────────
if mysql --version &>/dev/null; then
    ok "MySQL já instalado"
else
    info "Instalando MySQL 8..."
    apt-get install -y -qq mysql-server
    ok "MySQL instalado"
fi

systemctl enable --now mysql

info "Aguardando MySQL..."
until mysqladmin ping --silent 2>/dev/null; do sleep 1; done
ok "MySQL pronto"

info "Configurando banco..."
mysql -u root <<SQL
ALTER USER 'root'@'localhost' IDENTIFIED WITH mysql_native_password BY '${DB_ROOT_PASS}';
CREATE DATABASE IF NOT EXISTS \`${DB_NAME}\` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER IF NOT EXISTS '${DB_USER}'@'localhost' IDENTIFIED BY '${DB_PASS}';
GRANT ALL PRIVILEGES ON \`${DB_NAME}\`.* TO '${DB_USER}'@'localhost';
FLUSH PRIVILEGES;
SQL
ok "Banco: ${DB_NAME} | user: ${DB_USER}"

# ─── 7. Redis ─────────────────────────────────────────────────────────────────
if redis-cli --version &>/dev/null; then
    ok "Redis já instalado"
else
    info "Instalando Redis..."
    apt-get install -y -qq redis-server
fi

REDIS_CONF="/etc/redis/redis.conf"
sed -i "s/^# requirepass.*/requirepass ${REDIS_PASS}/" "$REDIS_CONF" 2>/dev/null || \
    echo "requirepass ${REDIS_PASS}" >> "$REDIS_CONF"
systemctl enable --now redis-server
ok "Redis configurado"

# ─── 8. FFmpeg + yt-dlp ───────────────────────────────────────────────────────
apt-get install -y -qq ffmpeg
if ! yt-dlp --version &>/dev/null; then
    curl -sSL https://github.com/yt-dlp/yt-dlp/releases/latest/download/yt-dlp \
        -o /usr/local/bin/yt-dlp
    chmod +x /usr/local/bin/yt-dlp
fi
ok "FFmpeg + yt-dlp prontos"

# ─── 9. Python venv ───────────────────────────────────────────────────────────
if [ ! -f /opt/venv/bin/python3 ]; then
    apt-get install -y -qq python3-venv python3-pip
    python3 -m venv /opt/venv
    /opt/venv/bin/pip install --upgrade pip yt-dlp -q --no-input
fi
ok "Python venv: /opt/venv"

# ─── 10. Supervisor (para Horizon) ───────────────────────────────────────────
if ! supervisord --version &>/dev/null; then
    apt-get install -y -qq supervisor
fi
systemctl enable --now supervisor
ok "Supervisor instalado"

# ─── 11. Configurar projeto ───────────────────────────────────────────────────
cd "$PROJECT_DIR"

info "Configurando .env..."
if [ ! -f .env ]; then
    cp .env.example .env
fi

sed -i \
    -e "s|APP_ENV=.*|APP_ENV=production|" \
    -e "s|APP_DEBUG=.*|APP_DEBUG=false|" \
    -e "s|APP_URL=.*|APP_URL=${APP_URL}|" \
    -e "s|DB_DATABASE=.*|DB_DATABASE=${DB_NAME}|" \
    -e "s|DB_USERNAME=.*|DB_USERNAME=${DB_USER}|" \
    -e "s|DB_PASSWORD=.*|DB_PASSWORD=${DB_PASS}|" \
    -e "s|REDIS_PASSWORD=.*|REDIS_PASSWORD=${REDIS_PASS}|" \
    -e "s|PYTHON_PATH=.*|PYTHON_PATH=/opt/venv/bin/python3|" \
    -e "s|YTDLP_PATH=.*|YTDLP_PATH=/usr/local/bin/yt-dlp|" \
    -e "s|FFMPEG_BINARY=.*|FFMPEG_BINARY=/usr/bin/ffmpeg|" \
    -e "s|FFPROBE_BINARY=.*|FFPROBE_BINARY=/usr/bin/ffprobe|" \
    .env
ok ".env configurado"

info "Instalando dependências PHP..."
sudo -u "$REAL_USER" composer install \
    --no-interaction --prefer-dist --optimize-autoloader --no-dev --no-progress 2>&1 | tail -3

info "Gerando APP_KEY..."
sudo -u "$REAL_USER" php artisan key:generate --force

info "Instalando dependências Node.js e compilando assets..."
sudo -u "$REAL_USER" npm ci --no-fund --no-audit 2>&1 | tail -3
sudo -u "$REAL_USER" npm run build 2>&1 | tail -3
ok "Assets compilados"

info "Permissões de storage..."
sudo -u "$REAL_USER" php artisan storage:link 2>/dev/null || true
mkdir -p storage/app/videos/temp storage/app/public/videos/ready storage/app/public/thumbnails
chown -R "$REAL_USER":www-data storage bootstrap/cache
chmod -R 775 storage bootstrap/cache

info "Migrations + Seeders..."
sudo -u "$REAL_USER" php artisan migrate --seed --force

info "Otimizando para produção..."
sudo -u "$REAL_USER" php artisan config:cache
sudo -u "$REAL_USER" php artisan route:cache
sudo -u "$REAL_USER" php artisan view:cache
sudo -u "$REAL_USER" php artisan event:cache
ok "Caches gerados"

# ─── 12. Nginx config ─────────────────────────────────────────────────────────
info "Configurando Nginx..."
PHP_SOCK=$(ls /var/run/php/php8.3-fpm.sock 2>/dev/null || echo "/var/run/php/php8.3-fpm.sock")

cat > /etc/nginx/sites-available/comissify <<NGINX
server {
    listen 80;
    server_name ${SERVER_IP} _;

    root ${PROJECT_DIR}/public;
    index index.php;

    client_max_body_size 100M;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    charset utf-8;

    location / {
        try_files \$uri \$uri/ /index.php?\$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php\$ {
        fastcgi_pass unix:${PHP_SOCK};
        fastcgi_param SCRIPT_FILENAME \$realpath_root\$fastcgi_script_name;
        include fastcgi_params;
        fastcgi_read_timeout 300;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
NGINX

ln -sf /etc/nginx/sites-available/comissify /etc/nginx/sites-enabled/comissify
rm -f /etc/nginx/sites-enabled/default
nginx -t && systemctl reload nginx
ok "Nginx configurado"

# ─── 13. Supervisor para Horizon ──────────────────────────────────────────────
info "Configurando Supervisor (Horizon)..."
cat > /etc/supervisor/conf.d/comissify-horizon.conf <<SUPERVISOR
[program:comissify-horizon]
process_name=%(program_name)s
command=php ${PROJECT_DIR}/artisan horizon
autostart=true
autorestart=true
user=${REAL_USER}
redirect_stderr=true
stdout_logfile=${PROJECT_DIR}/storage/logs/horizon.log
stopwaitsecs=3600
SUPERVISOR

supervisorctl reread
supervisorctl update
supervisorctl start comissify-horizon || supervisorctl restart comissify-horizon
ok "Horizon rodando via Supervisor"

# ─── 14. Scheduler via Cron ──────────────────────────────────────────────────
info "Configurando Cron (scheduler)..."
CRON_JOB="* * * * * cd ${PROJECT_DIR} && php artisan schedule:run >> /dev/null 2>&1"
(crontab -u "$REAL_USER" -l 2>/dev/null | grep -v "artisan schedule"; echo "$CRON_JOB") \
    | crontab -u "$REAL_USER" -
ok "Cron configurado"

# ─── 15. Firewall Oracle (iptables) ──────────────────────────────────────────
info "Abrindo portas 80 e 443 no iptables..."
iptables -I INPUT 6 -m state --state NEW -p tcp --dport 80 -j ACCEPT
iptables -I INPUT 6 -m state --state NEW -p tcp --dport 443 -j ACCEPT
# Persistir regras
if apt-get install -y -qq iptables-persistent 2>/dev/null; then
    netfilter-persistent save
fi
ok "Portas abertas"

# ─── 16. Salvar credenciais ───────────────────────────────────────────────────
CREDS_FILE="${PROJECT_DIR}/.deploy-credentials"
cat > "$CREDS_FILE" <<CREDS
# Gerado por setup-oracle.sh em $(date)
# GUARDE ESTE ARQUIVO EM LOCAL SEGURO E APAGUE DO SERVIDOR
APP_URL=${APP_URL}
DB_NAME=${DB_NAME}
DB_USER=${DB_USER}
DB_PASS=${DB_PASS}
DB_ROOT_PASS=${DB_ROOT_PASS}
REDIS_PASS=${REDIS_PASS}
CREDS
chmod 600 "$CREDS_FILE"

# ─── Sumário ──────────────────────────────────────────────────────────────────
echo ""
echo "============================================================"
echo -e "  ${GREEN}Setup concluído!${NC}"
echo "============================================================"
echo ""
echo "  Acesso:"
echo "    App:     ${APP_URL}"
echo "    Admin:   ${APP_URL}/admin"
echo "    Horizon: ${APP_URL}/horizon"
echo ""
echo "  Credenciais padrão:"
echo "    E-mail: admin@example.com"
echo "    Senha:  password"
echo ""
echo -e "  ${YELLOW}IMPORTANTE:${NC} Credenciais salvas em .deploy-credentials"
echo -e "  ${YELLOW}Abra o .env e configure as APIs${NC} (Shopee, OpenAI, Mail, etc.)"
echo ""
echo "  Para updates: sudo bash deploy.sh"
echo ""
