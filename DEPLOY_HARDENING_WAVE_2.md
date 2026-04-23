# Deploy — Segunda onda de security hardening

Branch: `security/auth-payment-hardening`
Commits relevantes: `64a1bee` (segunda onda) + 1 commit pendente (2FA).

Este guia cobre **apenas as mudanças feitas nesta leva** (em cima do deploy da primeira onda descrito em `SECURITY_HARDENING_CHECKLIST.md`). Se o primeiro deploy ainda não subiu, siga aquele guia antes.

---

## O que tem de novo nesta onda

1. Política de senha forte (`min(8)` + maiúscula + minúscula + número) em `/registrar` e `/registrar-senha/{token}`.
2. `User`: `subscription_status` e `stripe_subscription_id` fora do `$fillable`.
3. `LogoutController` desloga ambos os guards (`web` + `client`).
4. `SetupAccount::validateToken` com `try/catch` + timeout de 10s na API VM-PAY.
5. `ForceHttps` middleware registrado no pipeline global quando `APP_ENV=production`.
6. `@json($clientSecret)`/`@json($stripePublishableKey)` no checkout do Stripe.
7. Validação real de CPF/CNPJ via `laravellegends/pt-br-validator`.
8. `Referrer-Policy: no-referrer` em `/registrar-senha/{token}` (meta tag + header).
9. **2FA no painel do dono** (TOTP + recovery codes, nativo do Filament 5).

---

## 1. Pré-deploy

- [ ] **Backup do banco de produção** (o mesmo comando de sempre):
  ```bash
  mysqldump -u <user> -p <db> > backup_before_wave2_$(date +%F).sql
  ```
  Esta onda adiciona 2 colunas nullable em `users` — rollback é seguro, mas backup é seguro-de-verdade.

- [ ] **Confirmar `APP_ENV=production`** em `.env` de produção. Sem isso, o middleware `ForceHttps` **não ativa**.

- [ ] **Confirmar `APP_KEY` estável.** As novas colunas de 2FA são cifradas com `APP_KEY`. Rotacionar a chave depois de usuários cadastrarem 2FA = secrets perdidos (usuários teriam que desabilitar via escape hatch e reconfigurar).

---

## 2. Deploy

Na ordem, em **staging primeiro**, depois produção:

```bash
# 1. Pull da branch
git pull origin security/auth-payment-hardening

# 2. Instalar deps novas (laravellegends/pt-br-validator + bacon/bacon-qr-code)
composer install --no-dev --optimize-autoloader

# 3. Rodar a migration nova
php artisan migrate --force

# 4. Limpar caches
php artisan config:cache
php artisan route:cache
php artisan view:cache

# 5. Reiniciar workers (models mudaram — User tem casts novos)
php artisan queue:restart
```

Migration nova desta onda (rodará uma vez):
- `2026_04_23_100000_add_2fa_columns_to_users` — adiciona `app_authentication_secret` + `app_authentication_recovery_codes`, ambos nullable.

- [ ] Staging OK.
- [ ] Produção OK.

---

## 3. Smoke test em produção (5 min)

### 3.1 Login dono (sem 2FA ainda)
- [ ] Logar em `/login/mercado` com credencial normal → cai em `/painel/dashboard`. Fluxo igual de antes (não deve ter regressão).
- [ ] Logout pelo menu → volta para `/login/mercado`.

### 3.2 Logout dispara dois guards
- [ ] Logar no painel dono e no painel síndico (outro browser / aba anônima).
- [ ] Fazer logout de um deles → sessão do outro **também** morre (`session()->invalidate()` agora cobre ambos).

### 3.3 Registro novo com CPF/CNPJ
- [ ] Em `/registrar`, tentar digitar CPF inválido tipo `111.111.111-11` → deve rejeitar com "Informe um CPF ou CNPJ válido."
- [ ] CPF válido (ex: `111.444.777-35`) ou CNPJ válido → aceita.

### 3.4 Senha forte
- [ ] Em `/registrar`, tentar `senha123` (sem maiúscula) → erro "A senha deve conter letras maiúsculas e minúsculas."
- [ ] Tentar `Senhasegura` (sem número) → erro "A senha deve conter pelo menos um número."
- [ ] `Senha1234` → aceita.
- [ ] Idem em `/registrar-senha/{token}` (link do síndico).

### 3.5 Referrer-Policy no link do síndico
- [ ] Abrir `/registrar-senha/{token}` (qualquer token válido) em DevTools → aba Network → resposta deve ter header `Referrer-Policy: no-referrer`.
- [ ] `view-source:` na mesma página → `<meta name="referrer" content="no-referrer">` presente no `<head>`.

### 3.6 Stripe checkout (regressão)
- [ ] Fluxo `/registrar` → checkout → pagamento com cartão teste → retorna logado. Mesmo comportamento de antes.

### 3.7 HTTPS forçado
- [ ] Tentar acessar `http://<dominio>/login/mercado` → deve redirecionar automaticamente pra `https://`.

### 3.8 2FA — enrollment (você mesmo, primeiro)
- [ ] Logar no painel dono → menu canto superior direito → **Profile** (`/painel/profile`).
- [ ] Seção "Autenticação em duas etapas" aparece. Clicar em **Set up**.
- [ ] QR code renderiza (SVG). Ler no Google Authenticator / Authy / 1Password.
- [ ] Digitar código de 6 dígitos → passa para tela de recovery codes.
- [ ] **CRÍTICO: salvar os 8 recovery codes em lugar seguro** (password manager, cofre). Se perder o celular e não tiver esses codes, só sai com escape hatch via tinker.
- [ ] Confirmar conclusão.

### 3.9 2FA — login
- [ ] Logout.
- [ ] Logar de novo em `/login/mercado` com email+senha.
- [ ] Deve redirecionar para `/login/mercado/2fa`.
- [ ] Digitar código do app → entra no dashboard.
- [ ] Logout e repetir, mas clicar em "Usar código de recuperação" → usar 1 recovery code → entra. Voltar em `/painel/profile` e conferir que agora sobraram 7 codes (ou usar "Regenerate recovery codes").

### 3.10 2FA — proteção contra bypass
- [ ] Tentar ir direto em `/login/mercado/2fa` **sem** ter feito login antes → deve redirecionar de volta pra `/login/mercado`.
- [ ] No POST de `/login/mercado/2fa`, digitar código errado 7x em 1 min → 7ª tentativa pega o throttle (`429`).

---

## 4. Comunicar aos outros donos (se houver mais de 1)

Quando estabilizar, avisar:

> "Agora o painel tem autenticação em duas etapas opcional. Recomendo ativar:
> Menu (canto superior direito) → Profile → Autenticação em duas etapas → Set up.
> Guarde os códigos de recuperação num gerenciador de senhas."

Não é obrigatório no momento. Se você quiser tornar obrigatório depois, é trocar 2 linhas no `FilamentAuthenticate` middleware (tem nota na `SECURITY_HARDENING_CHECKLIST.md`).

---

## 5. Rollback se algo quebrar

```bash
# Reverter o commit do 2FA (mantém a primeira onda em prod)
git revert <hash-do-commit-do-2fa>
git push

# Reverter migration do 2FA
php artisan migrate:rollback --step=1

# Limpar caches
php artisan config:cache && php artisan route:cache && php artisan queue:restart
```

Se precisar voltar também a segunda onda inteira, reverter o `64a1bee` (já documentado em `SECURITY_HARDENING_CHECKLIST.md` seção 5).

⚠ Cuidado: se alguém já ativou 2FA, `migrate:rollback` joga fora `app_authentication_secret` + `app_authentication_recovery_codes`. Se precisar rollback depois de gente ativar, **avise antes** para as pessoas exportarem/anotarem os secrets, ou assuma que elas vão re-configurar do zero.

---

## 6. Escape hatch — dono perdeu celular e recovery codes

Via `php artisan tinker`:

```php
$u = App\Models\User::where('email', 'dono@exemplo.com')->first();
$u->saveAppAuthenticationSecret(null);
$u->saveAppAuthenticationRecoveryCodes(null);
```

Isso zera o 2FA do usuário. Próximo login cai direto no painel (sem challenge). Ele pode re-configurar em `/painel/profile` quando quiser.

**Antes de rodar, confirme a identidade** do dono por canal separado (ligação, e-mail corporativo, etc.) — esse é literalmente o bypass do 2FA.

---

## 7. Ainda no backlog (próxima onda futura, não urgente)

- Migração real do `/registrar-senha/{token}` para POST/sessão (hoje com mitigação via `Referrer-Policy`).
- Forçar 2FA para todos os donos (hoje opt-in).
- `@json` / escape do `$clientSecret` do Stripe já foi feito — sem pendência aqui, só mencionando para consistência.
