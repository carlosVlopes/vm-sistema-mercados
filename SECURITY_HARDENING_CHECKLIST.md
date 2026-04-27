# Checklist — Finalizar Security Hardening

Branch: `security/auth-payment-hardening`
Commit: `30373ea`

Rode esta checklist **antes de mergear em `main`**. Marque cada item ao concluir.

---

## 1. Pré-deploy (antes de rodar migrations)

- [ ] **Backup do banco de produção.** A migration `encrypt_user_api_tokens` muta a coluna `users.api_token` — rollback sem backup = tokens perdidos.
  ```bash
  mysqldump -u <user> -p <db> > backup_before_security_$(date +%F).sql
  ```
- [ ] **Grep por uso raw do `api_token`.** Depois do cast `encrypted`, buscas do tipo `where('api_token', $plain)` não funcionam mais (valor cifrado é não-determinístico).
  ```bash
  grep -rn "api_token" app/ --include="*.php" | grep -v "casts\|fillable"
  ```
  Confirme que todo acesso é via `$user->api_token` (leitura pelo model, que aplica o cast).
- [ ] **Confirmar `APP_KEY` fixado em produção.** Rotacionar `APP_KEY` depois do deploy invalida todos os `api_token`. Se precisar rotacionar no futuro, usa `php artisan key:rotate` ou re-encripta manualmente.
- [ ] **Conferir que `SESSION_SECURE_COOKIE=true`** em `.env` de produção (HTTPS-only cookies).

---

## 2. Rodar as migrations

Em **staging primeiro**, depois produção:

```bash
php artisan migrate
```

Três migrations novas vão rodar nesta ordem:
1. `2026_04_22_100000_add_register_token_security_to_clients` — adiciona `register_token_expires_at` + índice prefix.
2. `2026_04_22_100100_encrypt_user_api_tokens` — cifra tokens existentes (idempotente).
3. `2026_04_22_100200_create_webhook_events_table` — nova tabela de idempotência do Stripe.
4. `2026_04_23_100000_add_2fa_columns_to_users` — adiciona `app_authentication_secret` + `app_authentication_recovery_codes` (nullable, só preenchidos quando dono opta por 2FA).

- [ ] Staging OK.
- [ ] Produção OK.

**Se precisar reverter em staging:**
```bash
php artisan migrate:rollback --step=3
```
A migration `encrypt_user_api_tokens` tem `down()` reversível (decifra de volta), desde que `APP_KEY` não tenha mudado.

---

## 3. Testes manuais (staging)

### 3.1 Login dono do mercado
- [ ] Login com credencial válida → cai em `/painel/dashboard`.
- [ ] Login com senha errada 7× em 1 min → 7ª requisição retorna `429 Too Many Requests` (throttle).
- [ ] Logout pelo menu do Filament → volta para `/login/mercado`.

### 3.2 Login síndico
- [ ] Login com credencial válida → cai em `/sindico/dashboard`.
- [ ] Throttle idem ao dono.

### 3.3 Fluxo completo de registro + pagamento
- [ ] `/registrar` → preencher form → continuar para checkout.
- [ ] Stripe checkout embedded carrega normal.
- [ ] Finalizar pagamento com cartão teste Stripe (4242...) → redireciona para `/registrar/retorno?session_id=...` → auto-login no painel.
- [ ] **Verificar sessão regenerada:** abrir DevTools → cookie `laravel_session` mudou após o retorno.
- [ ] **Tentar reutilizar a URL de retorno em uma aba anônima:** com `?session_id=...` da sessão que já foi consumida — **NÃO deve logar**. Deve redirecionar para `/login/mercado` com mensagem "Pagamento confirmado! Faça login...".

### 3.4 Bloqueio de sequestro de usuário pending
- [ ] Criar um usuário parando no checkout (status `pending`).
- [ ] Em aba anônima, tentar `POST /registrar` com o mesmo e-mail → deve retornar erro de validação "Este e-mail já está em uso."

### 3.5 Síndico — link de definir senha
- [ ] Criar um síndico no painel. Copiar a URL gerada.
- [ ] Verificar no DB: `clients.register_token` deve ser um **hash de 64 chars** (não igual ao token da URL).
- [ ] Verificar `register_token_expires_at` ≈ `now() + 72h`.
- [ ] Abrir o link, definir senha com 8 caracteres → OK.
- [ ] Abrir o mesmo link de novo → deve dar "Token inválido ou expirado" (já foi usado).
- [ ] Criar outro síndico, forçar `register_token_expires_at = DATE_SUB(NOW(), INTERVAL 1 DAY)` via SQL, abrir link → deve rejeitar por expirado.
- [ ] Tentar senha com 6 chars → erro "A senha deve ter no mínimo 8 caracteres."

### 3.6 Logout com assinatura inativa
- [ ] Num usuário com `subscription_status = 'canceled'`, tentar logar.
- [ ] Middleware redireciona para `/assinatura-inativa`.
- [ ] Clicar em "Sair" / logout → deve efetivamente deslogar, **não** voltar ao checkout em loop.

### 3.7 Webhook Stripe (staging com Stripe CLI)
- [ ] `stripe listen --forward-to <staging>/stripe/webhook`.
- [ ] `stripe trigger checkout.session.completed` → resposta 200, linha criada em `webhook_events` com `processed_at` preenchido.
- [ ] Reenviar o mesmo evento manualmente (pelo dashboard Stripe) → resposta `{"status":"duplicate"}`, `webhook_events` não duplica.
- [ ] Conferir logs em `storage/logs/laravel.log` — nenhuma exceção.

### 3.8 VM-PAY API (cast encrypted)
- [ ] Um usuário existente (cujo `api_token` foi cifrado na migration) abre `/painel/configuracoes` → campo `api_token` aparece preenchido normalmente (cast decifra em leitura).
- [ ] Sincronizar vendas (SyncSalesJob) com esse usuário → funciona, API VM-PAY responde 200.

---

## 4. Deploy em produção

- [ ] Backup feito (passo 1).
- [ ] Pull da branch ou merge do PR.
- [ ] `php artisan migrate --force`.
- [ ] `php artisan config:cache && php artisan route:cache`.
- [ ] `php artisan queue:restart` (workers precisam recarregar models com cast novo).
- [ ] Smoke test rápido em produção: 1 login dono + 1 login síndico.
- [ ] Monitorar `storage/logs/laravel.log` por 24h.

---

## 5. Rollback de emergência (se algo quebrar em prod)

```bash
git revert 30373ea         # reverte o commit no repo
php artisan migrate:rollback --step=3
php artisan config:clear && php artisan queue:restart
```
⚠ Rollback da migration de encrypt só funciona se `APP_KEY` não foi rotacionada.

---

## 6. Itens que ficaram de fora (próxima onda)

Não afetam este deploy, mas ficam no radar.

### Feitos nesta segunda onda

- [x] `LogoutController` agora desloga **ambos** os guards (`web` + `client`) antes de invalidar a sessão.
- [x] `SetupAccount::validateToken` com `try/catch` + `Http::timeout(10)` → não trava o loader se API VM-PAY cair.
- [x] `@json($clientSecret)` e `@json($stripePublishableKey)` no checkout (em vez de `"{{ }}"`).
- [x] Política de senha forte `Password::min(8)->mixedCase()->numbers()` aplicada em `/registrar` e em `/registrar-senha/{token}`. Placeholder das views atualizado. **Nota:** `->uncompromised()` deixado fora de propósito — faz request HTTP p/ HIBP e pode travar o form se a API cair.
- [x] `$fillable` de `User` — removidos `subscription_status` e `stripe_subscription_id`. Agora só ficam setáveis por atribuição direta (`$user->subscription_status = ...; $user->save();`). Ajustados os call sites em `AuthController`, `StripeWebhookController`, `ManageSubscription`.
- [x] Middleware `ForceHttps` registrado no pipeline global em produção (via `bootstrap/app.php`, condicionado a `APP_ENV=production`).

### Ainda pendentes (escopo maior — agendar)

- [x] Validação real de CPF/CNPJ — pacote `laravellegends/pt-br-validator`, regra `cpf_ou_cnpj` aplicada em `AuthController::register`.
- [x] Token em URL de `/registrar-senha/{token}` — **mitigação mínima aplicada** (não migrado para POST): `<meta name="referrer" content="no-referrer">` na view + header `Referrer-Policy: no-referrer` na resposta do GET. Token continua único-uso, hash SHA-256 no DB, expira em 72h. Migração completa p/ POST/sessão fica no backlog se algum dia virar requisito.
- [x] 2FA no painel admin (dono) — MFA nativo do Filament 5 (TOTP + recovery codes). Enrollment via `/painel/profile`. Login custom em `/login/mercado` agora faz gate de 2FA: se usuário tem secret setado, redireciona para `/login/mercado/2fa` antes de logar. Instalado `bacon/bacon-qr-code` para renderizar QR code em SVG sem depender de imagick.

### Operação do 2FA — coisas a saber

- **Opt-in por usuário.** Cada dono entra em *Perfil* (menu canto superior direito no painel) e clica em "Configurar autenticação por app". Lê QR code no Google Authenticator/Authy, confirma, salva recovery codes.
- **Escape hatch** se o dono perder o celular E os recovery codes: rodar via tinker:
  ```php
  User::find($id)->saveAppAuthenticationSecret(null);
  User::find($id)->saveAppAuthenticationRecoveryCodes(null);
  ```
- **Síndico fica sem 2FA** (intencional — só painel do dono).
- **Não força 2FA para todo mundo.** Se quiser tornar obrigatório, a regra pode ser aplicada no `FilamentAuthenticate` middleware (redirecionar para `/painel/profile` se `app_authentication_secret` estiver vazio). Não foi feito agora porque não estava no escopo.

Quando quiser atacar, rodar `/security-review` ou pedir uma nova onda de agentes.
