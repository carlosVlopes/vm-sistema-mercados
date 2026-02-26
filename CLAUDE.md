# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

Sistema de gerenciamento de repasses financeiros de donos de mercados de condomínios para síndicos. Laravel 12 + Filament 5, com integração à API VM-PAY para buscar condomínios e vendas.

## Common Commands

```bash
# Development server
php artisan serve

# Build frontend assets
npm run dev          # Vite dev server with HMR
npm run build        # Production build

# Database
php artisan migrate
php artisan db:seed  # Seeds test user

# Queue worker (required for sales sync jobs)
php artisan queue:work

# Code style
./vendor/bin/pint    # Laravel Pint (PSR-12)

# Tests (Pest PHP)
php artisan test
./vendor/bin/pest
./vendor/bin/pest --filter="test name"
./vendor/bin/pest tests/Feature/ExampleTest.php
```

## Architecture

### Dual Panel System (Filament 5)

- **Painel** (`/painel`) — Admin panel for Users. Guard: `web`, model: `User`. Manages clients, creates transfers, syncs sales from API.
- **Sindico** (`/sindico`) — Client-facing panel. Guard: `client`, model: `Client`. Read-only access to their own transfers.

Panel providers: `app/Providers/Filament/PainelPanelProvider.php` and `SindicoPanelProvider.php`.

### Filament Resource Structure

Resources follow a modular pattern with separate directories for schemas, tables, and pages:
```
app/Filament/Resources/Clients/
├── ClientResource.php
├── Pages/          # ListClients, CreateClient, EditClient
├── Schemas/        # ClientForm.php
└── Tables/         # ClientsTable.php
```

The Transfer creation uses a **multi-step Wizard** (Informações Básicas → Resumo Financeiro) with real-time API data fetching and financial calculations.

### VM-PAY API Integration

Base URL: `https://vmpay.vertitecnologia.com.br/api/v1`

- `GET /clients` — List condominiums (cached in Filament forms)
- `GET /clients/{id}` — Condominium details
- `GET /cashless_sales` — Paginated sales data (300/page)

Auth: API token stored on `User.api_token`, validated in the SetupAccount page. Users without a configured token are redirected by `SetUserSettings` middleware.

### Sales Sync Pipeline

1. Admin selects client + condominium + date range in Transfer wizard
2. `SyncSalesJob` dispatched per day (page 1)
3. Job fetches sales, upserts via `Sale::upsertFromApi()`, dispatches next page if 300 results returned
4. Progress tracked in `Calculation` model (status: pending → processing → done/error)
5. Financial calculations use **Brick\Money** for precision: gross → minus machine_fee% → minus taxes_fee% → multiply by client percentage%

### Client Registration Flow

Admin creates client → generates `register_token` → copies link `/registrar-senha/{token}` → client sets password via public Blade form → can then log into Sindico panel.

### Key Models & Relationships

- **User** hasMany Client, Transfer, Sale, Calculation
- **Client** belongsTo User, belongsToMany Condominium (pivot: `clients_condominiums`), hasMany Transfer, Calculation
- **Sale** unique on `(api_id, client_id)`, has `scopePeriod()` for date filtering
- **Transfer** auto-sets `user_id` via model `booted()` hook
- **Sindico\Transfer** — separate model for the Sindico panel with scoped access

### Database

MySQL with database-backed sessions, cache, and queue. Queue is essential — sales sync runs as background jobs.

## Conventions

- All UI text in **Portuguese (pt_BR)**
- Monetary values handled with `Brick\Money\Money` (never raw floats)
- Filament forms fetch condominium data from API with caching
- File uploads (proof_payment, proof_light) on transfers
- Pest PHP for testing
