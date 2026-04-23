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

Não afetam este deploy, mas ficam no radar:

- [ ] `LogoutController::session()->invalidate()` derruba sessão compartilhada entre painéis.
- [ ] Validação real de CPF/CNPJ (hoje aceita qualquer string de 18 chars).
- [ ] Token em URL de `/registrar-senha/{token}` — migrar para POST / sessão temporária.
- [ ] 2FA no painel admin (dono).
- [ ] `SetupAccount::validateToken` sem try/catch → loader infinito se API cair.
- [ ] `@json($clientSecret)` em vez de `{{ }}` no checkout.
- [ ] Política de senha forte: `Password::min(8)->mixedCase()->numbers()->uncompromised()`.
- [ ] Reavaliar `$fillable` de `User` — `subscription_status` e `stripe_subscription_id` não deveriam ser mass-assignable.
- [ ] Registrar o middleware `ForceHttps` no pipeline de produção (hoje existe mas não está aplicado).

Quando quiser atacar, rodar `/security-review` ou pedir uma segunda onda de agentes.
