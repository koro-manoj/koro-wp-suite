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

```bash
cp .env.example .env
docker compose up -d
```

Open [http://localhost:8080](http://localhost:8080) and complete the WordPress installer.

### Post-install

1. **Activate plugins** (in order is fine — all are independent):
   - Koro Roles
   - Koro Admin
   - Koro Payments
   - Koro Booking

2. **Activate theme**: Koro Base

3. **Configure payments** (`Koro Suite → Payments`):
   - Enable payments
   - Set mode to **Sandbox**
   - Add test keys (e.g. `pk_test_...` / `sk_test_...`) — stored encrypted in `wp_options`

4. **Create services** (`Services → Add Service`) with price and duration

5. **Set front page**: Settings → Reading → Static page → assign a page or use default front-page template

Cart and Checkout pages are created automatically when Koro Booking is activated.

## Manual Setup (without Docker)

1. Install WordPress 6.7+ with PHP 8.1+ and MySQL 8
2. Copy `wp-content/themes/koro-base` and all `wp-content/plugins/koro-*` into your WordPress install
3. Activate theme and plugins as above

## Git Workflow

| Branch | Purpose |
|--------|---------|
| `main` | Stable releases |
| `dev` | Integration branch |
| `feature/*` | Feature work (e.g. `feature/stripe-webhooks`) |

Use [Conventional Commits](https://www.conventionalcommits.org/): `feat(booking): add date validation`.

## Architecture

See [docs/architecture.md](docs/architecture.md) for module boundaries, data flow, and security notes.

## Demo Data

After setup, add 2–3 sample services with prices. Sandbox mode completes payments without charging real cards.

## License

GPL-2.0-or-later (WordPress ecosystem standard).
