# EyeTech

Point of sale, inventory and service-request tracking for a phone repair and accessories shop, with a public marketing site, built for the shop's admins and cashiers.

## Features

Everything below is implemented in the routes, controllers and Vue pages in this repository.

- **Public website** (Blade): home, about, services and contact pages. Featured services and products are read from the database. The contact form validates input but does not send mail yet.
- **Authentication**: session login at `/login` with role-based landing page (admins go to the dashboard, cashiers go to the POS).
- **Point of sale** (`/app/pos`): product grid with category filter, cart, payment method (cash, card, mobile money, bank transfer), and a printable receipt after checkout.
- **Checkout** is a single database transaction: rows are locked with `lockForUpdate`, stock is checked, the sale and its line items are written, stock is decremented and a stock movement is logged. Insufficient stock aborts the whole sale.
- **Sales history**: list and detail views. Cashiers see only sales they made; admins see all.
- **Service requests**: repair tickets with generated request numbers, status (pending, in progress, completed, cancelled), estimated and final cost.
- **Admin only**: dashboard with revenue stats and charts, products (with image upload), categories, suppliers, services, stock movement log, users, and reports (a 7-day overview and a 30-day extended report).
- **Profile**: every user can edit their own name, email, phone, picture and password (current password required to change it).

## Stack

| Layer | Technology |
|---|---|
| Backend | PHP 8.2+ (developed on 8.4), Laravel 12.60 |
| Bridge | Inertia.js: `inertiajs/inertia-laravel` 3.1, `@inertiajs/vue3` 2.3 |
| Frontend | Vue 3.5, TypeScript 5.9, Vite 7.1, Tailwind CSS 4.1 |
| UI primitives | radix-vue 1.9, class-variance-authority, lucide-vue-next, vue-sonner |
| Authorization | spatie/laravel-permission 7.4, Laravel policies |
| Auth | Laravel session guard, laravel/sanctum 4.3 installed |
| Database | SQLite by default (any Laravel-supported driver works) |
| Tests | PHPUnit 11 (backend), Vitest 4 with happy-dom (frontend) |
| CI | GitHub Actions: backend tests, type-check, frontend tests, build |

## Architecture

- **Roles and authorization**: two roles, `admin` and `cashier`, stored both in a `users.role` column and in spatie roles. The `/app/*` route group requires `auth`; the management routes sit inside a `role:admin` group. Policies (`SalePolicy`, `ProductPolicy`, `UserPolicy` and others) are registered in `AppServiceProvider`. For example, `SalePolicy::view` lets an admin see any sale and a cashier only their own.
- **Validation**: dedicated Form Request classes in `app/Http/Requests` (for example `CheckoutSaleRequest`, `StoreUserRequest`, `UpdateProfileRequest`).
- **Inertia page structure**: controllers in `app/Http/Controllers/App` return `Inertia::render()` with API Resources from `app/Http/Resources`. Pages live in `resources/js/Pages/<Area>/Index.vue` (plus `Show`/`Edit` where needed), share a layout in `resources/js/Layouts`, and reuse `DataTable`, `FormDialog` and `StatsCard` components. `HandleInertiaRequests` shares the authenticated user, flash messages and app info.
- **Services**: `CheckoutService` (transactional checkout described above) and `ReportService` (sales and revenue totals for today, week and month, daily chart data, top products, payment method breakdown). `InsufficientStockException` carries the product and quantities involved.
- **Models**: `User`, `Category`, `Supplier`, `Product`, `Sales`, `SalesItem`, `StockMovement`, `Service`, `ServiceRequest`.
- **Seeding**: `config/seed.php` reads `SEED_ADMIN_EMAIL` and `SEED_ADMIN_PASSWORD`. No credentials are stored in the repository.

## Setup

Requirements: PHP 8.2+ with the sqlite extension, Composer, Node 22+ and npm.

```bash
git clone https://github.com/tallup/Eye-Tech.git
cd Eye-Tech

composer install
cp .env.example .env
php artisan key:generate

# Create the SQLite database file (DB_CONNECTION=sqlite is the default)
touch database/database.sqlite

# Optional: choose the seeded admin account. If the password is left empty,
# the seeder generates one and prints it in the console.
#   SEED_ADMIN_EMAIL=admin@example.com
#   SEED_ADMIN_PASSWORD=choose-a-strong-password

php artisan migrate --seed
php artisan storage:link

npm install
npm run dev          # in one terminal
php artisan serve    # in another
```

Open `http://localhost:8000` for the public site and `http://localhost:8000/login` to sign in with the seeded admin. Additional users, including cashiers, are created from the Users page.

### Tests

```bash
php artisan test     # backend
npm test             # frontend (Vitest)
npm run type-check   # vue-tsc
npm run build        # type-check plus production build
```

## Screenshots

<!-- Add screenshots here: public site, POS, dashboard, service requests. -->

## License

MIT
