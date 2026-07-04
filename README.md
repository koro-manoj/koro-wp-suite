# Koro WordPress Suite

Modular WordPress showcase demonstrating a custom booking platform with encrypted payment credentials, role-based editorial workflows, and a dedicated admin dashboard.

## Stack

| Layer | Technology |
|-------|------------|
| CMS | WordPress 6.7+ |
| Runtime | PHP 8.1+ |
| Database | MySQL 8.0 |
| Local dev | Docker Compose |

## Modules

| Path | Purpose |
|------|---------|
| `wp-content/themes/koro-base/` | Custom front-end theme (hero, service catalog, cart integration) |
| `wp-content/plugins/koro-booking/` | Service CPT, session cart, checkout flow, orders |
| `wp-content/plugins/koro-payments/` | Gateway settings with AES-256-GCM encrypted secrets, transaction log |
| `wp-content/plugins/koro-roles/` | Booking Manager & Content Editor roles, workflow restrictions |
| `wp-content/plugins/koro-admin/` | Koro Suite dashboard, stats, quick links |

## Quick Start (Docker)

### 1. Clone and configure

```bash
git clone https://github.com/koro-manoj/koro-wp-suite.git
cd koro-wp-suite
cp .env.example .env
```

The `.env` file configures database credentials and ports only. Payment API keys are **not** stored here — they are entered via the WordPress admin and encrypted in the database.

### 2. Start the stack

```bash
docker compose up -d
```

Verify the compose file parses correctly:

```bash
docker compose config
```

### 3. Install WordPress

Open [http://localhost:8080](http://localhost:8080) and complete the WordPress installer.

### 4. Activate plugins and theme

Activate in this order (order is not strict — plugins are independent):

1. Koro Roles
2. Koro Admin
3. Koro Payments
4. Koro Booking

Then activate the **Koro Base** theme.

### 5. Configure payments

Go to **Koro Suite → Payments**:

- Enable payments
- Set mode to **Sandbox**
- Enter test keys (e.g. `pk_test_...` / `sk_test_...`)

Credentials are encrypted with AES-256-GCM and stored in `wp_options` (`koro_payments_settings`).

### 6. Add services and set front page

1. Create services under **Services → Add Service** (set price and duration)
2. Settings → Reading → Static page, or use the default front-page template

Cart and Checkout pages are created automatically when Koro Booking is activated.

## Manual Setup (without Docker)

1. Install WordPress 6.7+ with PHP 8.1+ and MySQL 8
2. Copy `wp-content/themes/koro-base` and all `wp-content/plugins/koro-*` into your WordPress install
3. Activate theme and plugins as above
4. Ensure `wp-config.php` has unique authentication salts (required for payment encryption)

## Deployment

See [docs/DEPLOYMENT.md](docs/DEPLOYMENT.md) for production deployment, security checklist, and troubleshooting.

## Git Workflow

| Branch | Purpose |
|--------|---------|
| `main` | Stable releases |
| `dev` | Integration branch |
| `feature/*` | Feature work (e.g. `feature/ui-polish`) |

Use [Conventional Commits](https://www.conventionalcommits.org/): `feat(booking): add date validation`.

## Architecture

See [docs/architecture.md](docs/architecture.md) for module boundaries, data flow, and security notes.

## Demo Data

After setup, add 2–3 sample services with prices. Sandbox mode completes payments without charging real cards.

## Live demo

**URL:** _Pending deployment_ — [GitHub](https://github.com/koro-manoj/koro-wp-suite)

## License

GPL-2.0-or-later (WordPress ecosystem standard).
