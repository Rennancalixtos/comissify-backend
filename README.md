# Comissify — Plataforma de Automatização para Afiliados Shopee

Plataforma SaaS para afiliados Shopee. Permite buscar produtos com melhores comissões, gerar links afiliados, criar conteúdo (texto, imagem, vídeo, Pinterest) e acompanhar relatórios de conversão.

**Stack:** Laravel 11 · Livewire 3 · Alpine.js · Tailwind CSS · MySQL · Laravel Horizon (Redis)

---

## Requisitos do servidor

- PHP 8.3+ com extensões: `gd`, `redis`, `pdo_mysql`, `mbstring`, `xml`, `curl`, `zip`
- Node.js 20+
- Composer 2+
- MySQL 8+
- Redis 7+
- Supervisor
- Nginx
- Python 3.10+ com `yt-dlp` (para busca de vídeos Pinterest)

---

## Instalação local (desenvolvimento)

```bash
git clone git@github.com:SEU_USUARIO/SEU_REPO.git comissify
cd comissify

composer install
npm install

cp .env.example .env
php artisan key:generate

# Configure o banco de dados no .env, depois:
php artisan migrate --seed

php artisan storage:link
npm run dev

# Em outro terminal (fila/Horizon):
php artisan horizon
```

---

## Variáveis de ambiente importantes (`.env`)

```env
APP_NAME=Comissify
APP_ENV=production
APP_URL=https://seudominio.com

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=comissify
DB_USERNAME=
DB_PASSWORD=

REDIS_HOST=127.0.0.1
REDIS_PORT=6379

# Shopee Affiliate API
SHOPEE_APP_ID=
SHOPEE_APP_SECRET=

# Pinterest (deixe PINTEREST_MOCK=true até ter o token)
PINTEREST_MOCK=true
PINTEREST_ACCESS_TOKEN=

# Caminhos de binários (ajustar conforme servidor)
PYTHON_PATH=/usr/bin/python3
YTDLP_PATH=/usr/local/bin/yt-dlp
VIDEO_DOWNLOAD_TIMEOUT=120

# WhatsApp para CTA de compra de créditos
# Formato: apenas números com DDI, ex: 5511999999999
WHATSAPP_NUMBER=
```

---

## Deploy com Envoy

### Pré-requisitos

```bash
# Na sua máquina local
composer global require laravel/envoy

# No servidor: criar usuário de deploy e configurar SSH
# Gerar deploy key e adicionar ao GitHub (Settings → Deploy keys)
ssh-keygen -t ed25519 -C "deploy@comissify"
cat ~/.ssh/id_ed25519.pub  # copiar para o GitHub
```

### Primeira vez no servidor

1. Edite o `Envoy.blade.php` na raiz do projeto:
   - Substitua `SEU_IP_AQUI` pelo IP/hostname do servidor
   - Substitua `SEU_USUARIO/SEU_REPO` pela URL do repositório
   - Substitua `SEU_DOMINIO.COM` pelo domínio

2. Execute o setup:

```bash
envoy run setup
```

3. Copie o `.env` para o servidor:

```bash
scp .env.production deploy@SEU_IP:/var/www/comissify/shared/.env
```

4. Ative SSL:

```bash
# No servidor
certbot --nginx -d seudominio.com
```

### Deploy normal

```bash
envoy run deploy
```

O que acontece em cada deploy:
1. Clona nova release em `/var/www/comissify/releases/TIMESTAMP/`
2. Linka `storage` e `.env` compartilhados (nunca sobrescritos)
3. `composer install --no-dev --optimize-autoloader`
4. `npm ci && npm run build`
5. `php artisan migrate --force`
6. Rebuild de todos os caches (config, route, view, event)
7. Ativa a release (`current` → nova release)
8. `horizon:terminate` + reinício via Supervisor
9. Remove releases antigas (mantém últimas 5)

### Rollback

```bash
envoy run rollback
```

Volta para a release anterior e reinicia o Horizon.

---

## Estrutura de diretórios no servidor

```
/var/www/comissify/
├── current/          → symlink para a release ativa
├── shared/
│   ├── .env          → variáveis de ambiente (nunca versionado)
│   └── storage/      → uploads, logs, cache (persistido entre deploys)
└── releases/
    ├── 20260101120000/
    ├── 20260102150000/
    └── ...           → últimas 5 releases mantidas
```

---

## Funcionalidades

### Usuário
- **Buscar Produtos** — busca na API Shopee com filtros; gera links afiliados com 1 clique (persistidos)
- **Posts** — cria conteúdo a partir dos produtos afiliados:
  - Texto (R$ 0,10) — título + descrição
  - Imagem IA (R$ 0,90) — imagem promocional gerada
  - Vídeo de vendas (R$ 2,00) — vídeo curto 20-30s
  - Busca Pinterest (R$ 1,50) — encontra vídeos/imagens similares
- **Relatório de Conversões** — acompanha comissões via API Shopee (requer pacote ≥ R$ 24)
- **Créditos** — compra pacotes via WhatsApp

### Admin (`/admin`)
- Dashboard, Produtos, Vídeos, Fila, Contas, Configurações
- Tokens e Usuários
- Screenshots da landing page (upload com redimensionamento automático para 1600×1000)

---

## Créditos e preços

| Ação | Custo |
|---|---|
| Busca de produtos | R$ 0,20 |
| Gerar link afiliado | R$ 0,10 |
| Post texto | R$ 0,10 |
| Post imagem IA | R$ 0,90 |
| Post vídeo | R$ 2,00 |
| Busca Pinterest | R$ 1,50 |

Pacotes ≥ R$ 24 desbloqueiam acesso ao Relatório de Conversões.

---

## Licença

Proprietário — todos os direitos reservados.
