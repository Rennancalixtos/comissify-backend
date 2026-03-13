#!/usr/bin/env bash
# =============================================================================
# deploy.sh — Atualiza a aplicação em produção (Oracle Cloud)
# Uso: bash deploy.sh   (sem sudo — exceto o reload do supervisor)
# =============================================================================
set -euo pipefail

PROJECT_DIR="$(cd "$(dirname "$0")" && pwd)"

RED='\033[0;31m'; GREEN='\033[0;32m'; BLUE='\033[0;34m'; NC='\033[0m'
info() { echo -e "${BLUE}[INFO]${NC}  $*"; }
ok()   { echo -e "${GREEN}[OK]${NC}    $*"; }
die()  { echo -e "${RED}[ERRO]${NC}  $*" >&2; exit 1; }

[ -f "$PROJECT_DIR/.env" ] || die ".env não encontrado. Rode setup-oracle.sh primeiro."

cd "$PROJECT_DIR"

# ─── 1. Pull ──────────────────────────────────────────────────────────────────
info "Atualizando código (git pull)..."
git pull origin main
ok "Código atualizado"

# ─── 2. Dependências PHP ──────────────────────────────────────────────────────
info "Atualizando dependências PHP..."
composer install --no-interaction --prefer-dist --optimize-autoloader --no-dev --no-progress 2>&1 | tail -3
ok "Pacotes PHP atualizados"

# ─── 3. Assets ────────────────────────────────────────────────────────────────
info "Compilando assets..."
npm ci --no-fund --no-audit 2>&1 | tail -3
npm run build 2>&1 | tail -3
ok "Assets compilados"

# ─── 4. Manutenção ligada ─────────────────────────────────────────────────────
php artisan down --retry=30

# ─── 5. Migrations ────────────────────────────────────────────────────────────
info "Rodando migrations..."
php artisan migrate --force
ok "Migrations concluídas"

# ─── 6. Caches ────────────────────────────────────────────────────────────────
info "Limpando e recriando caches..."
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan event:clear

php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache
ok "Caches atualizados"

# ─── 7. Storage link ─────────────────────────────────────────────────────────
php artisan storage:link 2>/dev/null || true

# ─── 8. Permissões ───────────────────────────────────────────────────────────
chmod -R 775 storage bootstrap/cache

# ─── 9. Reiniciar Horizon ────────────────────────────────────────────────────
info "Reiniciando Horizon..."
php artisan horizon:terminate
sleep 3
sudo supervisorctl restart comissify-horizon
ok "Horizon reiniciado"

# ─── 10. Manutenção desligada ────────────────────────────────────────────────
php artisan up

ok "Deploy concluído! App disponível."
