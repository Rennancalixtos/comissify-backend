#!/usr/bin/env bash
# =============================================================================
# setup-local.sh — Instalação local do shopee-affiliate-automation
# Testado em Ubuntu 24.04 / WSL2
# Uso: sudo bash setup-local.sh
# =============================================================================
set -euo pipefail

# Evita prompts interativos do apt e needrestart
export DEBIAN_FRONTEND=noninteractive
export NEEDRESTART_MODE=a
export NEEDRESTART_SUSPEND=1

PROJECT_DIR="$(cd "$(dirname "$0")" && pwd)"
REAL_USER="${SUDO_USER:-$USER}"
DB_NAME="shopee_automation"
DB_USER="shopee"
DB_PASS="secret"
DB_ROOT_PASS="rootsecret"
APP_PORT=8082

RED='\033[0;31m'; GREEN='\033[0;32m'; YELLOW='\033[1;33m'; BLUE='\033[0;34m'; NC='\033[0m'
info()    { echo -e "${BLUE}[INFO]${NC}  $*"; }
ok()      { echo -e "${GREEN}[OK]${NC}    $*"; }
warn()    { echo -e "${YELLOW}[WARN]${NC}  $*"; }
die()     { echo -e "${RED}[ERRO]${NC}  $*" >&2; exit 1; }

[ "$(id -u)" -eq 0 ] || die "Execute com sudo: sudo bash $0"

echo ""
echo "============================================================"
echo "  Shopee Affiliate Automation — Setup Local"
echo "  Projeto: $PROJECT_DIR"
echo "  Porta:   $APP_PORT"
echo "============================================================"
echo ""

# ─── Helpers ──────────────────────────────────────────────────────────────────
start_service() {
    local svc="$1"
    if systemctl is-active --quiet "$svc" 2>/dev/null; then
        ok "$svc já está rodando"
    elif systemctl start "$svc" 2>/dev/null; then
        ok "$svc iniciado via systemctl"
    elif service "$svc" start 2>/dev/null; then
        ok "$svc iniciado via service"
    else
        warn "Não foi possível iniciar $svc automaticamente"
    fi
}

wait_mysql() {
    info "Aguardando MySQL ficar pronto..."
    local max=30 i=0
    until mysqladmin ping -u root --silent 2>/dev/null || \
          mysqladmin ping -u root -p"${DB_ROOT_PASS}" --silent 2>/dev/null; do
        i=$((i+1))
        [ $i -ge $max ] && warn "MySQL demorou demais, continuando mesmo assim..." && return
        sleep 1
    done
    ok "MySQL pronto"
}

# ─── 1. PHP 8.3 ───────────────────────────────────────────────────────────────
if php8.3 --version &>/dev/null; then
    ok "PHP 8.3 já instalado: $(php8.3 --version | head -1)"
else
    info "Instalando PHP 8.3..."
    apt-get update -qq -o Acquire::ForceIPv4=true
    apt-get install -y -qq software-properties-common gpg-agent
    LC_ALL=C.UTF-8 add-apt-repository -y ppa:ondrej/php
    apt-get update -qq -o Acquire::ForceIPv4=true
    apt-get install -y -qq \
        php8.3-cli php8.3-fpm \
        php8.3-mysql php8.3-redis php8.3-mbstring php8.3-xml \
        php8.3-curl php8.3-zip php8.3-gd php8.3-bcmath php8.3-intl
    ok "PHP 8.3 instalado: $(php8.3 --version | head -1)"
fi
# Garantir que 'php' aponta para 8.3
update-alternatives --set php /usr/bin/php8.3 2>/dev/null || true

# ─── 2. Composer ──────────────────────────────────────────────────────────────
if composer --version &>/dev/null; then
    ok "Composer já instalado: $(composer --version)"
else
    info "Instalando Composer..."
    curl -sS --connect-timeout 30 --max-time 120 \
        https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
    ok "Composer instalado: $(composer --version)"
fi

# ─── 3. Node.js 20 ────────────────────────────────────────────────────────────
if node --version &>/dev/null; then
    ok "Node.js já instalado: $(node --version)"
else
    info "Instalando Node.js 20 LTS..."
    apt-get install -y -qq curl ca-certificates gnupg
    mkdir -p /etc/apt/keyrings
    curl -fsSL --connect-timeout 30 --max-time 60 \
        https://deb.nodesource.com/gpgkey/nodesource-repo.gpg.key \
        | gpg --dearmor -o /etc/apt/keyrings/nodesource.gpg
    echo "deb [signed-by=/etc/apt/keyrings/nodesource.gpg] https://deb.nodesource.com/node_20.x nodistro main" \
        > /etc/apt/sources.list.d/nodesource.list
    apt-get update -qq
    apt-get install -y -qq nodejs
    ok "Node.js $(node --version) | npm $(npm --version)"
fi

# ─── 4. MySQL 8 ───────────────────────────────────────────────────────────────
if mysql --version &>/dev/null; then
    ok "MySQL já instalado: $(mysql --version)"
else
    info "Instalando MySQL 8..."
    apt-get install -y -qq mysql-server
    ok "MySQL instalado"
fi

start_service mysql
wait_mysql

info "Configurando banco de dados..."
MYSQL_SETUP_SQL="
ALTER USER 'root'@'localhost' IDENTIFIED WITH mysql_native_password BY '${DB_ROOT_PASS}';
CREATE DATABASE IF NOT EXISTS \`${DB_NAME}\` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER IF NOT EXISTS '${DB_USER}'@'127.0.0.1' IDENTIFIED BY '${DB_PASS}';
CREATE USER IF NOT EXISTS '${DB_USER}'@'localhost' IDENTIFIED BY '${DB_PASS}';
GRANT ALL PRIVILEGES ON \`${DB_NAME}\`.* TO '${DB_USER}'@'127.0.0.1';
GRANT ALL PRIVILEGES ON \`${DB_NAME}\`.* TO '${DB_USER}'@'localhost';
FLUSH PRIVILEGES;
"
if echo "${MYSQL_SETUP_SQL}" | mysql -u root 2>/dev/null; then
    ok "Banco configurado (auth_socket)"
elif echo "${MYSQL_SETUP_SQL}" | mysql -u root -p"${DB_ROOT_PASS}" 2>/dev/null; then
    ok "Banco configurado (senha root)"
else
    warn "Não foi possível configurar o banco automaticamente."
    warn "Execute manualmente: mysql -u root -e \"CREATE DATABASE ${DB_NAME};\""
fi
ok "Banco: ${DB_NAME} | user: ${DB_USER} | senha: ${DB_PASS}"

# ─── 5. Redis ─────────────────────────────────────────────────────────────────
if redis-cli --version &>/dev/null; then
    ok "Redis já instalado: $(redis-cli --version)"
else
    info "Instalando Redis..."
    apt-get install -y -qq redis-server
    ok "Redis instalado"
fi

REDIS_CONF="/etc/redis/redis.conf"
if [ -f "$REDIS_CONF" ]; then
    if grep -q "^requirepass" "$REDIS_CONF"; then
        sed -i "s/^requirepass .*/requirepass redissecret/" "$REDIS_CONF"
    elif grep -q "^# requirepass" "$REDIS_CONF"; then
        sed -i "s/^# requirepass .*/requirepass redissecret/" "$REDIS_CONF"
    else
        echo "requirepass redissecret" >> "$REDIS_CONF"
    fi
fi

start_service redis-server

# ─── 6. FFmpeg + yt-dlp ───────────────────────────────────────────────────────
if ffmpeg -version &>/dev/null; then
    ok "FFmpeg já instalado"
else
    info "Instalando FFmpeg..."
    apt-get install -y -qq ffmpeg
    ok "FFmpeg instalado"
fi

if yt-dlp --version &>/dev/null; then
    ok "yt-dlp já instalado: $(yt-dlp --version)"
else
    info "Instalando yt-dlp..."
    curl -sSL --connect-timeout 30 --max-time 120 \
        https://github.com/yt-dlp/yt-dlp/releases/latest/download/yt-dlp \
        -o /usr/local/bin/yt-dlp
    chmod +x /usr/local/bin/yt-dlp
    ok "yt-dlp $(yt-dlp --version)"
fi

# ─── 7. Python venv ───────────────────────────────────────────────────────────
if [ ! -f /opt/venv/bin/python3 ]; then
    info "Criando Python venv em /opt/venv..."
    apt-get install -y -qq python3-venv python3-pip
    python3 -m venv /opt/venv
    /opt/venv/bin/pip install --upgrade pip -q --no-input
    /opt/venv/bin/pip install yt-dlp -q --no-input
    ok "Python venv configurado"
else
    ok "Python venv já existe"
fi

# ─── 8. Configurar projeto ────────────────────────────────────────────────────
cd "$PROJECT_DIR"

info "Configurando .env..."
if [ ! -f .env ]; then
    cp .env.example .env
fi
# Ajustar para localhost com porta $APP_PORT
sed -i \
    -e "s|DB_HOST=mysql|DB_HOST=127.0.0.1|" \
    -e "s|DB_HOST=redis|DB_HOST=127.0.0.1|" \
    -e "s|REDIS_HOST=redis|REDIS_HOST=127.0.0.1|" \
    -e "s|APP_URL=http://localhost:[0-9]*|APP_URL=http://localhost:${APP_PORT}|" \
    -e "s|DB_DATABASE=shopee_automation|DB_DATABASE=${DB_NAME}|" \
    -e "s|DB_USERNAME=shopee|DB_USERNAME=${DB_USER}|" \
    -e "s|DB_PASSWORD=secret|DB_PASSWORD=${DB_PASS}|" \
    -e "s|DB_ROOT_PASSWORD=rootsecret|DB_ROOT_PASSWORD=${DB_ROOT_PASS}|" \
    -e "s|PYTHON_PATH=.*|PYTHON_PATH=/opt/venv/bin/python3|" \
    -e "s|YTDLP_PATH=.*|YTDLP_PATH=/usr/local/bin/yt-dlp|" \
    -e "s|FFMPEG_PATH=.*|FFMPEG_PATH=/usr/bin/ffmpeg|" \
    -e "s|FFPROBE_PATH=.*|FFPROBE_PATH=/usr/bin/ffprobe|" \
    .env
ok ".env configurado (porta ${APP_PORT})"

# ─── 9. Composer install ──────────────────────────────────────────────────────
info "Instalando dependências PHP (pode demorar)..."
sudo -u "$REAL_USER" composer install \
    --no-interaction \
    --prefer-dist \
    --optimize-autoloader \
    --no-progress \
    2>&1 | tail -5
ok "Pacotes PHP instalados"

info "Gerando APP_KEY..."
sudo -u "$REAL_USER" php artisan key:generate --force
ok "APP_KEY gerado"

# ─── 10. NPM + build ─────────────────────────────────────────────────────────
info "Instalando dependências Node.js (pode demorar)..."
sudo -u "$REAL_USER" npm install \
    --no-fund \
    --no-audit \
    --prefer-offline \
    2>&1 | tail -5
info "Compilando assets front-end..."
sudo -u "$REAL_USER" npm run build 2>&1 | tail -5
ok "Assets compilados em public/build"

# ─── 11. Storage + Migrations ────────────────────────────────────────────────
info "Criando link de storage..."
sudo -u "$REAL_USER" php artisan storage:link 2>/dev/null || true

mkdir -p storage/app/videos/temp \
         storage/app/public/videos/ready \
         storage/app/public/thumbnails
chown -R "$REAL_USER":"$REAL_USER" storage bootstrap/cache
chmod -R 775 storage bootstrap/cache

info "Rodando migrations + seeders..."
sudo -u "$REAL_USER" php artisan migrate --seed --force
ok "Banco populado"

info "Publicando config do Horizon..."
sudo -u "$REAL_USER" php artisan horizon:install 2>/dev/null || true

# ─── 12. Sumário ─────────────────────────────────────────────────────────────
echo ""
echo "============================================================"
echo -e "  ${GREEN}Setup concluído com sucesso!${NC}"
echo "============================================================"
echo ""
echo "  Para iniciar, abra 3 terminais:"
echo ""
echo -e "  ${BLUE}Terminal 1${NC} — Servidor web:"
echo "    cd $PROJECT_DIR && php artisan serve --port=${APP_PORT}"
echo ""
echo -e "  ${BLUE}Terminal 2${NC} — Filas (Horizon):"
echo "    cd $PROJECT_DIR && php artisan horizon"
echo ""
echo -e "  ${BLUE}Terminal 3${NC} — Scheduler (opcional):"
echo "    cd $PROJECT_DIR && php artisan schedule:work"
echo ""
echo "  URLs:"
echo "    Admin:   http://localhost:${APP_PORT}/admin"
echo "    Horizon: http://localhost:${APP_PORT}/horizon"
echo ""
echo "  Credenciais padrão:"
echo "    E-mail:  admin@example.com"
echo "    Senha:   password"
echo ""
echo "  MySQL:"
echo "    Host: 127.0.0.1:3306"
echo "    DB:   ${DB_NAME}"
echo "    User: ${DB_USER} / ${DB_PASS}"
echo ""
