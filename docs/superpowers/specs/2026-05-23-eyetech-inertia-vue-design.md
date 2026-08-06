# EyeTech: Filament → Inertia + Vue 3 Migration

**Status:** Design approved, awaiting user spec review
**Date:** 2026-05-23
**Owner:** Taal
**Scope:** Replace the entire Filament admin panel with a custom Laravel + Inertia + Vue 3 + TypeScript + shadcn-vue application. Port the public Blade site to the same stack. Migrate via parallel routes — Filament stays alive at `/admin` until the new Vue app at `/app` is feature-complete, then Filament is removed.

---

## 1. Goals & Non-Goals

### Goals
- Replace Filament's generic admin look with a branded, modern, professional UI.
- Ship a dedicated POS screen at `/app/pos` that feels purpose-built (not a generic CRUD form).
- Support two roles: `admin` (full access) and `cashier` (POS + own sales only).
- Keep the system shippable throughout the migration — Filament stays available until the Vue replacement passes acceptance for every resource.
- Port the public marketing site (`/`, `/about`, `/services`, `/contact`) to the same Vue stack so the whole app is one cohesive codebase.

### Non-Goals
- No multi-tenant / multi-shop support.
- No mobile app — web responsive only.
- No third-party payment gateway integration in this migration (POS records `cash | card | mobile` as a label only).
- No SSR — Inertia client-side rendering is acceptable; the public site does not need crawler-grade SEO beyond standard Inertia SEO defaults.
- No real-time / WebSocket features.

---

## 2. Context

### Current state
- Laravel application at `/home/taal/Documents/eyetech-system`.
- Filament 4 admin panel at `/admin`.
- Tailwind CSS 4 + Vite already configured (`package.json`).
- 9 Eloquent models: `Category`, `Product`, `Service`, `ServiceRequest`, `Sales`, `SalesItem`, `StockMovement`, `Supplier`, `User`.
- 8 Filament Resources providing full CRUD: Categories, Products, Sales, ServiceRequests, Services, StockMovements, Suppliers, Users.
- 4 Filament Pages: `Dashboard`, `Profile`, `Reports`, `CustomReports`.
- 2 Filament Widgets: `InventoryStatsWidget`, `LowStockAlertWidget`.
- Public Blade pages: home, about, services, contact (in `resources/views/website/`).
- Admin seed account: `admin@example.com` / `REDACTED` (single user, no role column yet).

### Primary user
The owner's brother runs an eye-tech shop. His most frequent task is **ringing up customers at the point of sale**. His top complaint about the current Filament UI is that it looks generic and not professional in front of customers (not speed, not missing features).

### Constraints
- Timeline: no rush — the project can take months. Doing it right matters more than shipping fast.
- Solo developer.
- Must remain functional throughout — Filament `/admin` continues to work until the Vue replacement is feature-complete.

---

## 3. Migration Strategy: Parallel Routes

Filament keeps running at `/admin` until every resource is ported. New Inertia + Vue app lives at `/app`. Both panels share the same `users` table and the same Sanctum session. After login, role-based redirect sends users to the right home page.

| Path | Owner | Audience | Status |
|---|---|---|---|
| `/admin/...` | Filament (existing) | Brother during transition | Kept until end |
| `/app/...` | New Inertia + Vue | All users (new home) | Built during migration |
| `/app/pos` | Vue POS screen | Cashier-first | Phase 1 deliverable |
| `/login` | Shared Sanctum form | Both panels | Phase 0 |
| `/`, `/about`, `/services`, `/contact` | Blade today → Vue later | Public | Phase 3 |

### Phased delivery

| Phase | Output | Acceptance |
|---|---|---|
| 0 — Foundation | Inertia + Vue + TS + Tailwind + shadcn-vue scaffolded. Shared Sanctum login. Roles installed. Empty `/app/dashboard` reachable. | Login works for admin@example.com, redirects to `/app/dashboard`. Filament login at `/admin` still works. |
| 1 — POS | `/app/pos` fully functional: product search, cart, checkout, receipt. `SaleController` with DB transaction + `lockForUpdate` for stock. Sales index + detail page. | Cashier can complete a sale end-to-end. Stock decrements correctly under concurrent load (test). Receipt prints. |
| 2 — Resources | Categories, Products, Suppliers, Stock Movements, Services, Service Requests, Users ported to `/app/...`. Dashboard with stats + chart. Reports + Custom Reports. Profile. | Every Filament Resource has a working Vue equivalent passing feature tests. Admin can stop opening `/admin` for daily work. |
| 3 — Public site | Marketing pages ported to Vue using `PublicLayout`. | All Blade routes return Inertia equivalents. |
| 4 — Cutover | Remove `app/Filament/` directory. Remove `filament/filament` from `composer.json`. Drop `/admin` routes. Move `AdminPanelProvider` styling into Vue layouts if any was custom. | `composer.json` no longer lists filament. App runs without `/admin`. |

Rollback per phase: each phase merges in its own feature branch; if a phase fails acceptance, revert the merge — Filament is untouched until Phase 4.

---

## 4. Architecture

### Stack additions

| Package | Purpose |
|---|---|
| `laravel/sanctum` | Cookie-based session auth (Inertia uses the same XSRF cookie) |
| `inertiajs/inertia-laravel` | Server adapter |
| `@inertiajs/vue3` | Client adapter |
| `vue@^3` | Vue 3 |
| `@vitejs/plugin-vue` | Vite plugin |
| `typescript`, `vue-tsc` | TypeScript |
| `tailwindcss-animate`, `class-variance-authority`, `clsx`, `tailwind-merge`, `lucide-vue-next`, `radix-vue` | shadcn-vue dependencies |
| `spatie/laravel-permission` | Roles (`admin`, `cashier`) |
| `sonner` (or shadcn toast) | Toast notifications |
| `vitest`, `@vue/test-utils`, `happy-dom` | Frontend tests |

Already present and kept: `laravel/framework`, `tailwindcss@^4`, `vite@^7`, `axios`, `concurrently`.

### Folder layout

```
app/
  Filament/                # untouched until Phase 4
  Http/
    Controllers/
      App/                 # NEW — Inertia controllers
        DashboardController.php
        PosController.php
        SaleController.php
        ProductController.php
        ... one per resource
      WebsiteController.php  # ported to return Inertia::render in Phase 3
    Middleware/
      HandleInertiaRequests.php   # NEW — shares auth.user, flash, app meta
    Requests/
      StoreProductRequest.php
      UpdateProductRequest.php
      CheckoutSaleRequest.php
      ... per action
    Resources/
      ProductResource.php
      SaleResource.php
      ... shape data for Vue
    Exceptions/
      InsufficientStockException.php  # NEW
  Models/                   # unchanged
  Policies/
    ProductPolicy.php
    SalePolicy.php
    ...
  Providers/
    Filament/               # removed in Phase 4
    AuthServiceProvider.php # NEW or existing — register policies

resources/
  js/
    app.ts                  # Inertia entry
    Pages/
      Auth/
        Login.vue
      Dashboard.vue
      POS/
        Index.vue
        Receipt.vue
      Sales/
        Index.vue
        Show.vue
      Products/
        Index.vue
        Edit.vue
      Categories/
        Index.vue
      Suppliers/
        Index.vue
      StockMovements/
        Index.vue
      Services/
        Index.vue
        Edit.vue
      ServiceRequests/
        Index.vue
        Show.vue
      Users/
        Index.vue
        Edit.vue
      Profile/
        Edit.vue
      Reports/
        Index.vue
        Custom.vue
      Public/
        Home.vue
        About.vue
        Services.vue
        Contact.vue
      Errors/
        403.vue
        404.vue
        500.vue
    Layouts/
      AppLayout.vue          # sidebar + topbar
      PosLayout.vue          # full-screen POS
      PublicLayout.vue
    components/
      ui/                    # shadcn-vue primitives (copied in, owned by us)
        button.vue
        input.vue
        dialog.vue
        table.vue
        ...
      DataTable.vue
      FormDialog.vue
      StatsCard.vue
      ConfirmDelete.vue
      EmptyState.vue
    composables/
      useForm.ts
      useTable.ts
      useAuth.ts
    types/
      models.ts              # Product, Sale, SaleItem, etc.
      inertia.d.ts           # PageProps shape
    lib/
      utils.ts               # cn() helper for class-variance-authority
  views/
    app.blade.php            # NEW Inertia root template
    website/                 # removed in Phase 3
    filament/                # removed in Phase 4

routes/
  web.php                    # add /app/* and /login; keep /admin (Filament owns it)
  auth.php                   # NEW — login/logout handled by Sanctum + Inertia

tests/
  Feature/
    App/
      DashboardTest.php
      PosCheckoutTest.php
      ProductCrudTest.php
      AuthorizationTest.php
      ...
  Unit/
    Policies/
      ProductPolicyTest.php
      SalePolicyTest.php
  js/                        # Vitest tests
    DataTable.spec.ts
    POSIndex.spec.ts
    useForm.spec.ts
    useAuth.spec.ts
```

### Authentication flow

1. Visitor hits any `/app/*` route → `auth` middleware → redirect to `/login` if unauthenticated.
2. `/login` renders `Pages/Auth/Login.vue` via Inertia.
3. Form posts to `LoginController@store` (Sanctum session login via `Auth::attempt`).
4. On success, redirect based on role: `admin` → `/app/dashboard`, `cashier` → `/app/pos`.
5. Filament's `/admin` continues to use its own login flow against the same `users` table until Phase 4 — no conflict because both use Laravel's session guard.

### Roles & authorization

- Add `role` column to `users` table via migration: `enum('admin','cashier')`, default `cashier`. Seed existing admin user with `role = 'admin'`.
- Use `spatie/laravel-permission` for the role model. Define two roles only; no fine-grained permissions in this iteration.
- Backend enforcement: `Route::middleware(['auth', 'role:admin'])->group(...)` for admin-only routes (Users, Suppliers, Reports). Cashier routes use `auth` only.
- Frontend enforcement: `useAuth().isAdmin` hides nav links and admin-only buttons. Backend is the source of truth — frontend hiding is UX only.
- Policies enforce row-level rules (e.g. cashier can only view their own sales).

---

## 5. Components & Pages

### Shared components

| Component | Responsibility |
|---|---|
| `AppLayout.vue` | Admin shell: collapsible sidebar with nav, topbar with user menu and global search, slot for page content. |
| `PosLayout.vue` | Full-screen POS shell: branded header, cart panel always visible, no sidebar. |
| `PublicLayout.vue` | Marketing site nav + footer. |
| `DataTable.vue` | Wraps shadcn `Table`. Props: `columns`, `rows`, `pagination`, `filters`. Emits sort/filter/page changes that the parent forwards to Inertia via `router.get` with `preserveState`. |
| `FormDialog.vue` | Modal form using shadcn `Dialog`. Slot for fields. Uses `useForm` internally. |
| `StatsCard.vue` | Dashboard tile: title, big number, delta indicator, icon. |
| `ConfirmDelete.vue` | Reusable destructive-action confirmation. |
| `EmptyState.vue` | "No items yet" placeholder with optional CTA. |
| `Toast` (sonner) | Mounted in `AppLayout`. Watches `page.props.flash` and surfaces success/error messages. |

### Pages

| Page | Route | Notes |
|---|---|---|
| Login | `/login` | shadcn Card + Form. Role-based redirect after success. |
| Dashboard | `/app/dashboard` | Admin home. StatsCards: today's sales total, low-stock count, pending service requests, total products. Sales-last-7-days line chart. Replaces `InventoryStatsWidget` + `LowStockAlertWidget`. Admin-only. |
| POS | `/app/pos` | Two-pane layout. Left: searchable/filterable product grid + barcode/SKU input field. Right: cart with line items, qty controls, subtotal/tax/total, customer name field, payment method radio, big checkout button. |
| Receipt | `/app/sales/{id}` (with `?print=1` flash) | Receipt-style view of completed sale. Triggers `window.print()` once on mount if flagged. |
| Sales index | `/app/sales` | DataTable: `sale_number`, date, customer, `total_amount`, `payment_method`, `status`, cashier (`user.name`). Filters: date range, cashier, payment, status. Cashier sees only own sales (`user_id = current user`). |
| Sale detail | `/app/sales/{id}` | Read-only sale view with line items. Admin can void; cashier cannot. |
| Products index | `/app/products` | DataTable with stock badge (red when `stock_quantity` below `min_stock_level`). Search by name/SKU. Filter by category, supplier, `is_active`. |
| Product edit | `/app/products/{id}/edit`, `/app/products/create` | Form: name, SKU, description, selling_price, cost_price, stock_quantity, min_stock_level, category, supplier, brand, model, image upload, is_active. |
| Categories | `/app/categories` | DataTable + inline create/edit via `FormDialog`. |
| Suppliers | `/app/suppliers` | DataTable + edit drawer. Admin-only. |
| Stock movements | `/app/stock-movements` | DataTable. Type filter (in / out / adjust). Read-only; movements created automatically by sales and product edits. |
| Services | `/app/services` | DataTable + featured toggle (corresponds to `is_featured` column from `2026_05_23_172453_add_is_featured_to_services_table` migration). |
| Service requests | `/app/service-requests` | DataTable + status workflow buttons (pending → in-progress → done). |
| Users | `/app/users` | Admin-only. Role assignment. |
| Profile | `/app/profile` | Edit name, email, password. Available to all roles. |
| Reports | `/app/reports` | Date-range filter, sales chart, top products, low stock list. Admin-only. |
| Custom reports | `/app/reports/custom` | Port the data-shaping logic from `App\Filament\Pages\CustomReports::getViewData()`. Admin-only. |
| Public Home/About/Services/Contact | `/`, `/about`, `/services`, `/contact` | Ported in Phase 3. |
| Errors | implicit | `Pages/Errors/{403,404,500}.vue` rendered via custom exception handler. |

### Composables

- `useTable(initialFilters)` — manages search/sort/filter/page state, updates URL via `router.get('...', filters, { preserveState: true })`. Returns reactive state + handlers wired into `DataTable`.
- `useForm()` — thin wrapper on Inertia's `useForm` adding sonner toast on submit success and on validation error.
- `useAuth()` — returns `user`, `isAdmin`, `isCashier` derived from `usePage().props.auth.user`.

---

## 6. Data Flow

### Inertia request lifecycle

No separate REST API. No client-side Axios calls except where unavoidable (none planned). Every navigation and form submission goes through Inertia.

1. Browser visits `/app/products` → Laravel router → `ProductController@index`.
2. Controller paginates `Product::with(['category','supplier'])`, shapes via `ProductResource::collection`, calls `Inertia::render('Products/Index', ['products' => $paginator, 'filters' => $request->only([...])])`.
3. Response:
   - First load: full HTML document with the Vue app + initial props embedded.
   - Subsequent loads: JSON only, Vue re-renders the page component with new props.
4. User clicks an Inertia `<Link>` to `/app/products/42/edit` → Inertia fetches JSON → swaps page component.
5. Form submission via `useForm().put('/app/products/42')` → Laravel validates via `UpdateProductRequest` → updates model → redirect to index with `->with('success', 'Product updated')`.
6. Redirect triggers Inertia to fetch the index page. `HandleInertiaRequests` puts `flash.success` into shared props. Vue's flash watcher emits a sonner toast.

### TypeScript model contract

`resources/js/types/models.ts` is the single source of truth for shapes flowing from backend to frontend. Backend `App\Http\Resources\*` classes manually mirror these shapes. Codegen is deferred — kept in sync by hand for now.

Field names match the existing DB schema (see migrations under `database/migrations/`):

```ts
export interface Product {
  id: number
  name: string
  sku: string
  description: string | null
  selling_price: number
  cost_price: number
  stock_quantity: number
  min_stock_level: number
  category_id: number
  supplier_id: number
  category: Category | null
  supplier: Supplier | null
  brand: string | null
  model: string | null
  image: string | null
  is_active: boolean
}

export interface SaleItem {
  id: number
  sale_id: number
  product_id: number | null
  item_name: string
  item_sku: string | null
  item_description: string | null
  quantity: number
  unit_price: number
  discount_amount: number
  total_price: number
}

export type PaymentMethod = 'cash' | 'card' | 'mobile_money' | 'bank_transfer'
export type SaleStatus = 'pending' | 'completed' | 'cancelled' | 'refunded'

export interface Sale {
  id: number
  sale_number: string
  customer_name: string | null
  customer_phone: string | null
  customer_email: string | null
  payment_method: PaymentMethod
  status: SaleStatus
  subtotal: number
  tax_amount: number
  discount_amount: number
  total_amount: number
  notes: string | null
  user_id: number | null
  user: { id: number; name: string } | null
  items: SaleItem[]
  created_at: string
}

// ...Category, Supplier, Service, ServiceRequest, StockMovement, User
```

### POS-specific flow

1. Cashier opens `/app/pos` → `PosController@index` returns `{ products, categories }` for the whole catalog (acceptable size — single shop, ≤ few thousand SKUs). Cached for 5 minutes server-side.
2. Vue stores products in a reactive ref. Search/filter happens client-side — no roundtrip per keystroke.
3. Cart lives in a Vue ref scoped to the POS page (in-memory only — refreshing the tab clears it).
4. Cashier clicks "Checkout" → `useForm().post('/app/pos/checkout', { items, customer, payment_method })`.
5. `PosController@checkout` wraps in `DB::transaction`. For each line: `Product::lockForUpdate()->findOrFail($productId)`, verify `stock_quantity >= quantity`, decrement `stock_quantity`, create `StockMovement` (type `out`). Create `Sales` (generate `sale_number`, set `status = 'completed'`, set `user_id = current user`) + `SalesItems` (snapshot `item_name`/`item_sku`/`item_description` from product at sale time). On any insufficient stock → throw `InsufficientStockException`.
6. Transaction commits → return `redirect()->route('sales.show', $sale)->with('print', true)`.
7. `Sales/Show.vue` reads `page.props.flash.print` and calls `window.print()` once on mount.

### Shared global props

`HandleInertiaRequests::share()` returns on every request:

```php
return [
    'auth' => ['user' => $request->user()?->only(['id','name','email','role'])],
    'flash' => [
        'success' => fn () => $request->session()->get('success'),
        'error' => fn () => $request->session()->get('error'),
        'print' => fn () => $request->session()->get('print'),
    ],
    'app' => ['name' => config('app.name'), 'version' => config('app.version', '0.1.0')],
];
```

---

## 7. Error Handling

**Note on column names:** Spec uses real DB names (`selling_price`, `cost_price`, `stock_quantity`, `min_stock_level`, `total_amount`, `total_price`, etc.) — controllers and resources MUST use these. No renaming via accessors; keep one source of truth.

| Scenario | Handling |
|---|---|
| Validation failures | `FormRequest` returns 422 with field errors. Inertia's `useForm.errors` exposes them reactively. shadcn `Input` shows inline `errors.field_name` below each control. |
| Authorization (403) | `Gate::authorize()` / Policies in controllers throw `AuthorizationException`. Custom handler renders `Errors/403.vue` with link to dashboard. |
| Not found (404) | Route-model binding failure → custom handler renders `Errors/404.vue`. |
| Server error (500) | Production: `Errors/500.vue` with generic message + report-issue mailto. Dev: Laravel Ignition kept on. |
| Inertia request failure (network) | `router.on('error', handler)` in `app.ts` shows sonner toast: "Connection lost — please retry." |
| Stock oversell race | `PosController@checkout` uses `DB::transaction` + `lockForUpdate` per product. On `InsufficientStockException`, return Inertia redirect with `flash.error`, cart preserved client-side so cashier can adjust. |
| Flash messages | All controllers use `->with('success', ...)` / `->with('error', ...)`. Shared via Inertia middleware. Toast on arrival. |
| CSRF expiry (419) | Inertia auto-redirects to login when 419 detected. No silent failure. |

---

## 8. Testing

### Backend (Pest, with `RefreshDatabase`)

| Suite | Tests |
|---|---|
| Feature: Auth | Login redirects admin to `/app/dashboard`, cashier to `/app/pos`. Logout invalidates session. |
| Feature: Authorization | Cashier blocked (`assertForbidden`) from `/app/users`, `/app/suppliers`, `/app/reports`. Admin gets 200. |
| Feature: Dashboard | Admin sees stats card values matching seeded data. |
| Feature: POS Checkout | Successful sale decrements stock. Concurrent checkout (run two requests in parallel via `parallel()` helper or DB-level transaction simulation) — second request fails with `InsufficientStockException` when only 1 in stock. Receipt page sets `flash.print = true`. |
| Feature: Products CRUD | Create/update/delete via Inertia POST/PUT/DELETE. `assertInertia(fn (Assert $page) => $page->component('Products/Index'))`. |
| Feature: Other resources CRUD | One feature test per resource covering at least index + store + update + destroy + 403 for cashier on admin-only resources. |
| Unit: Policies | `ProductPolicy::update($cashier, $product)` returns false. `SalePolicy::view($cashier, $ownSale)` returns true; `SalePolicy::view($cashier, $otherSale)` returns false. |

Factories: one per model, used in every feature test setup.

Coverage target: 70%+ on controllers + policies.

### Frontend (Vitest + `@vue/test-utils` + `happy-dom`)

| Subject | Tests |
|---|---|
| `DataTable.vue` | Renders provided rows. Sort emit fires with column. Pagination emit fires with page. Filter slot content renders. |
| `POS/Index.vue` | Adding a product creates a cart line. Increasing qty updates line total. Removing line works. Checkout button disabled when cart empty. Successful checkout (mocked) clears cart. |
| `useForm` composable | Submitting with mocked Inertia router emits success toast. Validation errors populate `form.errors`. |
| `useAuth` composable | `isAdmin` is true when `role === 'admin'`. |

Skipped: shadcn primitives (upstream-tested), trivial display components.

Coverage target: 50%+ on composables + page components.

### End-to-end (Playwright — already in the stack via `chrome-devtools-mcp`)

One smoke flow per phase, run before merging:

- Phase 0: navigate `/login` → submit → land on `/app/dashboard`.
- Phase 1: login as cashier → `/app/pos` → add product → checkout → receipt shown.
- Phase 2: per resource, list page + one row edit + save.
- Phase 3: visit each public page, assert hero copy + nav.

### TDD discipline

Follow `superpowers:test-driven-development`. For every new controller action and policy method, write the failing feature test first, then implement. For POS checkout specifically, the race-condition test is written before the controller logic — this is where bugs hide.

### CI

GitHub Actions: lint, `vue-tsc --noEmit`, `phpunit` (or pest), `vitest run`, `vite build`. Block merge on red. Set up during Phase 0.

---

## 9. Risks & Mitigations

| Risk | Mitigation |
|---|---|
| Custom-built tables fall short of Filament's batteries-included experience (column toggle, bulk actions, advanced filters) | Build `DataTable.vue` incrementally — start with what each page actually needs, not "all of Filament's features." YAGNI. Accept some regression vs Filament in exchange for design freedom. |
| Stock oversell under concurrent checkout | `DB::transaction` + `lockForUpdate` per row + dedicated race-condition test in Phase 1. |
| Filament and Inertia sharing the same Sanctum session causes conflicts | Both use Laravel's standard session guard — no special handling needed. Verified by Phase 0 acceptance test that confirms both `/admin/login` and `/login` work against the same `users` table. |
| TypeScript types drift from backend `Resource` shapes | Manual sync for now; add `spatie/laravel-typescript-transformer` in a future iteration if drift causes bugs. Feature tests with `assertInertia` catch backend-side shape regressions. |
| Receipt printing inconsistent across browsers | Use `window.print()` with print-targeted CSS (`@media print`). Test in Chrome (primary) before Phase 1 acceptance. Defer thermal-printer integration. |
| Migration drags on, brother stays on ugly Filament for months | Phase 1 (POS) ships first and is the highest-pain item. Even if Phase 2 takes months, brother has the branded POS in front of customers within weeks. |
| Removing Filament in Phase 4 breaks something used silently (e.g. Filament's notification system, file uploads) | Audit `app/Filament/` and `composer.json` carefully before removal. Run full test suite + manual smoke before deleting. |

---

## 10. Open Questions

- Receipt format: pure browser print (HTML + `@media print` CSS) versus generating a thermal-printer ESC/POS payload — defer to Phase 1 implementation, browser print is the assumed default.
- Image storage for products: keep current setup (whatever Filament uses — likely `storage/app/public`) versus migrate to S3 — defer, keep current.
- Whether to keep `App\Filament\Pages\CustomReports` data logic in-place and have the new Vue page call it, or to extract to a service class — decide during Phase 2 when porting Reports.

---

## 11. Definition of Done

- All Filament Resources have working Vue equivalents at `/app/*`.
- All Filament Pages (Dashboard, Profile, Reports, Custom Reports) have Vue equivalents.
- Public marketing pages render via Inertia.
- `app/Filament/` directory removed. `composer.json` no longer lists `filament/filament`.
- All tests pass: backend feature tests ≥70% coverage on controllers + policies; frontend tests ≥50% on composables + key page components; one e2e smoke per phase green.
- Brother uses `/app/pos` daily without falling back to `/admin`.
- Spec, plan, and CHANGELOG entry committed.
