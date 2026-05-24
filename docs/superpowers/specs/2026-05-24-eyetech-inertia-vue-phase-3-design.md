# EyeTech Inertia + Vue — Phase 3: Finish Admin Migration — Design Spec

**Date:** 2026-05-24
**Status:** Approved (proceeding without per-question approval per user no-stop directive)
**Owner:** Taal
**Scope:** Port the remaining Filament admin pieces to Inertia + Vue at `/app/...` so the brother no longer needs `/admin` for daily work. Phase 4 then removes Filament. Public site Inertia port moves to Phase 5.

**Parent spec:** `docs/superpowers/specs/2026-05-23-eyetech-inertia-vue-design.md`
**Prior phases:** Phase 0 (Inertia/Vue/login/roles), Phase 1 (POS + Sales), Phase 2 (Products, Categories, Suppliers, Stock Movements, Dashboard).

---

## 1. Goals

- Replace Filament for: **Services**, **Service Requests**, **Users**, **Profile**, **Reports**, **Custom Reports**.
- Same patterns established in Phase 2 — controllers under `App\Http\Controllers\App`, FormRequests, Resources, Policies, paginated `DataTable.vue`, `FormDialog.vue` for inline edits, dedicated `Edit.vue` page where the form is larger.
- Admin can perform every daily task without opening `/admin`.

## 2. Non-Goals

- No removal of Filament — that is Phase 4.
- No public marketing site work — that is Phase 5.
- No new business logic; behaviour matches what Filament currently does.
- No new packages.

## 3. Scope Matrix

| Resource | Routes | UI pattern | Authz | Notes |
|---|---|---|---|---|
| Services | `index, store, update, destroy` at `/app/services` | DataTable + `FormDialog` (small form) | admin-only | Expose `is_featured` toggle missing from Filament form |
| Service Requests | `index, show, store, update, destroy` at `/app/service-requests` | DataTable + dedicated `Show.vue` with status workflow buttons | admin full; cashier index-only read | Status enum: pending → in_progress → completed → cancelled |
| Users | `index, store, update, destroy` at `/app/users` | DataTable + `FormDialog` with role select | admin-only; cannot delete self | Optional password set on create; profile_picture upload deferred to Profile |
| Profile | `edit, update` at `/app/profile` | Single `Edit.vue` page | any authed user (own row only) | Name, email, password (current + new), phone, profile_picture upload |
| Reports | `index` at `/app/reports` | Single `Index.vue` | admin-only | Port `App\Filament\Pages\Reports::getViewData()` shape; date-range filter; reuse 7-day chart from Dashboard, top products + staff (5 each) |
| Custom Reports | `index` at `/app/reports/custom` | Single `Custom.vue` | admin-only | Port `App\Filament\Pages\CustomReports::getViewData()` shape; 30-day chart, top products + staff (10 each), service-request KPIs |

## 4. File Structure

### Backend (new)

```
app/Http/Controllers/App/
  ServiceController.php
  ServiceRequestController.php
  UserController.php
  ProfileController.php
  ReportController.php

app/Http/Requests/
  StoreServiceRequest.php
  UpdateServiceRequest.php
  StoreServiceRequestRequest.php           # service-request store
  UpdateServiceRequestRequest.php
  StoreUserRequest.php
  UpdateUserRequest.php
  UpdateProfileRequest.php

app/Http/Resources/
  ServiceResource.php
  ServiceRequestResource.php
  UserResource.php                          # public-safe (no password hash)

app/Policies/
  ServicePolicy.php                         # admin-only
  ServiceRequestPolicy.php                  # admin full; cashier view-any only
  UserPolicy.php                            # admin only; deny delete-self
```

### Backend (modified)

```
app/Providers/AppServiceProvider.php        # register the four new policies
routes/web.php                              # add Phase 3 routes (admin-gated except /app/profile + /app/service-requests index)
```

### Frontend (new)

```
resources/js/Pages/
  Services/Index.vue
  ServiceRequests/Index.vue
  ServiceRequests/Show.vue
  Users/Index.vue
  Profile/Edit.vue
  Reports/Index.vue
  Reports/Custom.vue

resources/js/types/models.ts                # extend with Service, ServiceRequest, ReportPayload, CustomReportPayload (and User fields if missing)
```

### Frontend (modified)

```
resources/js/Layouts/AppLayout.vue          # add nav links: Services, Service Requests, Users, Reports; user-menu link to Profile
```

### Tests (new)

```
tests/Feature/App/Services/ServicesCrudTest.php
tests/Feature/App/ServiceRequests/ServiceRequestsCrudTest.php
tests/Feature/App/Users/UsersCrudTest.php
tests/Feature/App/Profile/ProfileUpdateTest.php
tests/Feature/App/Reports/ReportsTest.php
tests/Feature/App/Reports/CustomReportsTest.php
```

## 5. Authorization

All policies follow the role-string check used in Phase 2:

```php
public function viewAny(User $user): bool { return $user->role === 'admin'; }
```

Exceptions:
- `ServiceRequestPolicy::viewAny` → both admin and cashier (view-only for cashier).
- `ServiceRequestPolicy::view` → both admin and cashier; cashier still cannot create/update/delete.
- `UserPolicy::delete` → `$user->role === 'admin' && $user->id !== $target->id`.
- `Profile` uses no policy — controller enforces `Auth::user()` updates only its own row.

`role:admin` middleware applied at the route-group level for: services, users, reports, custom-reports, service-requests `store|update|destroy`. Service-requests `index|show` outside the admin gate (cashier read).

## 6. Validation Rules

Inline rules per FormRequest. Highlights:

- **StoreUserRequest:** `name` required string ≤255, `email` required email unique, `password` required min 8 confirmed, `role` required in `['admin','cashier']`, `phone` nullable.
- **UpdateUserRequest:** `email` unique except current, `password` nullable min 8 confirmed (skip update when blank).
- **UpdateProfileRequest:** `name`, `email` (unique except self), `phone` nullable, `profile_picture` nullable image ≤2MB, `current_password` required when `password` present, `password` nullable min 8 confirmed.
- **StoreServiceRequest:** `name` required, `slug` required unique (auto-generate from name in `prepareForValidation`), `description` required string, `price` required numeric min 0, `estimated_duration` nullable integer, `category` nullable, `is_active` boolean, `is_featured` boolean.
- **UpdateServiceRequest:** same shape, slug unique except current.
- **StoreServiceRequestRequest:** `customer_name`, `customer_phone` required, `customer_email` nullable email, `service_id` required exists, `device_description`, `problem_description` required, `status` in `['pending','in_progress','completed','cancelled']` (default pending if absent), `estimated_cost`, `final_cost` nullable numeric, `notes` nullable. `request_number` auto-generated by model boot — never client-supplied.
- **UpdateServiceRequestRequest:** same; when status transitions to `completed`, set `completed_at = now()` server-side.

## 7. Reports Data Shapes

### `/app/reports` payload (mirror `Reports::getViewData`)

```ts
{
  range: { from: string; to: string };           // ISO dates
  totals: { sales_count: number; revenue: number; avg_order: number };
  growth: { sales_pct: number; revenue_pct: number };  // vs prior equal-length window
  daily_chart: { date: string; total: number }[];      // 7 entries
  top_products: { id: number; name: string; sku: string; qty: number; revenue: number }[];  // 5
  top_staff: { id: number; name: string; sales_count: number; revenue: number }[];          // 5
  sales_by_status: Record<string, number>;
  sales_by_payment: Record<string, number>;
}
```

### `/app/reports/custom` payload (mirror `CustomReports::getViewData`)

```ts
{
  range: { from: string; to: string };
  totals: { /* totalSales, totalRevenue, today*, week*, month*, avg */ };
  growth: { sales_pct: number; revenue_pct: number };
  daily_chart: { date: string; total: number }[];      // 30 entries
  top_products: { ... }[];                              // 10
  top_staff: { ... }[];                                 // 10
  sales_by_status: Record<string, number>;
  sales_by_payment: Record<string, number>;
  service_requests: {
    total: number; pending: number; in_progress: number; completed: number;
    conversion_rate: number;                            // (sales / requests) * 100
  };
  inventory: { total: number; active: number; low_stock: number; out_of_stock: number };
}
```

Both controllers accept optional `?from=YYYY-MM-DD&to=YYYY-MM-DD` query string; default `from = today-30`, `to = today`.

Query logic mirrors existing Filament page code — extract into a single `App\Services\ReportService` to avoid duplication between the two pages (Reports = 7-day, top-5; CustomReports = 30-day, top-10 — same service, different parameters).

## 8. Vue Component Notes

- **Status badges (ServiceRequests):** map status → color: `pending`=amber, `in_progress`=blue, `completed`=green, `cancelled`=red. Match existing `getStatusColorAttribute` semantics.
- **Reports chart:** reuse the inline SVG bar chart from `Dashboard.vue` — no charting library.
- **Profile picture upload:** form posts multipart via `useForm({ ... }).post('/app/profile', { forceFormData: true, method: 'put', _method: 'put' })` pattern (Inertia file-upload requires POST with method spoofing for PUT routes).
- **User-self-delete guard:** `Users/Index.vue` hides Delete button when `row.id === auth.user.id`. Backend policy is source of truth.

## 9. Testing

Pest, `RefreshDatabase`. Each suite mirrors `tests/Feature/App/Suppliers/SuppliersCrudTest.php`:

- Per resource: admin index, admin store, admin update, admin destroy, cashier forbidden (where admin-only).
- Service requests: admin create/update/destroy + status transition sets `completed_at`; cashier can index + show; cashier blocked from store/update/destroy.
- Users: admin cannot delete self (`assertForbidden` when deleting own row); password optional on update.
- Profile: user updates own row; current_password required when changing password; cannot change another user's row via tampered route (single canonical `/app/profile` — no `{id}` parameter).
- Reports: admin gets 200 with payload shape; cashier 403; date-range filter narrows totals.

Coverage target: 70%+ on the new controllers + policies (project standard from Phase 0 spec).

## 10. Risks

| Risk | Mitigation |
|---|---|
| Filament's UserResource may have hidden field manipulation (e.g. assigning Spatie roles separately) | Recon confirmed UserForm carries no role field today; our `UserController::store` will `User::create($data)` + `$user->syncRoles([$data['role']])`. Existing `role` enum column is also set via fillable — double-source-of-truth flagged here, kept consistent in the controller. Phase 4 cleanup may collapse this. |
| Custom Reports query logic large | Extracted to `ReportService` once, two thin controllers. |
| Inertia file upload with PUT requires method spoofing | Use the `forceFormData + _method: 'put'` pattern; documented in Vue page comments. |
| Cashier accidentally given access to admin reports | Backend policy + middleware enforce; frontend `useAuth().isAdmin` only hides nav. |

## 11. Definition of Done

- All six resources reachable under `/app/*`, all tests green (`php artisan test --filter='App\\\\(Services|ServiceRequests|Users|Profile|Reports)'`).
- `npx vue-tsc --noEmit` clean.
- `npm run build` succeeds.
- AppLayout nav lists all admin links + a Profile entry in the user menu.
- Manual smoke: admin can create+edit+delete a service, file a service request and mark it completed, add a user, change own profile, view both Reports pages with seeded sales.
- Tag: `phase-3-complete`.
- Filament `/admin` still works (Phase 4 will remove it).

---

**Phase 4 (next):** Strip `app/Filament/`, drop `filament/filament` from composer, delete `/admin` routes.

**Phase 5 (after):** Port public marketing site (`/`, `/about`, `/services`, `/contact`) to Inertia + Vue under `PublicLayout.vue`.
