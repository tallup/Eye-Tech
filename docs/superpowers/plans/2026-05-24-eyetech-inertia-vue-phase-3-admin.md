# EyeTech Inertia + Vue — Phase 3: Finish Admin Migration — Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: superpowers:subagent-driven-development to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Spec:** `docs/superpowers/specs/2026-05-24-eyetech-inertia-vue-phase-3-design.md`
**Prior plan:** `docs/superpowers/plans/2026-05-24-eyetech-inertia-vue-phase-2-inventory.md` (`phase-2-complete` work shipped).

**Goal:** Port the remaining Filament admin pieces (Services, Service Requests, Users, Profile, Reports, Custom Reports) to Inertia + Vue at `/app/...` so the brother no longer needs `/admin` for daily work.

**Pattern:** Mirror Phase 2 exactly — `App\Http\Controllers\App\*Controller`, FormRequests, Resources, Policies, paginated `DataTable.vue` + `FormDialog.vue` for inline; dedicated `Edit.vue` / `Show.vue` for larger forms.

**TDD discipline:** Each task = write failing test → implement → green → commit. See `superpowers:test-driven-development`.

**Pint note:** Editing files that only change imports loses them via PostToolUse pint. Use Write (full file) — not Edit — when changing imports only. (Recorded in `eyetech/phase-2-task3-products-crud` memory.)

**Inertia float strict-compare note:** `assertInertia->where()` is `assertSame`-based. PHP `json_encode(100.0)` → `"100"` → int on decode. Tests should cast expected numerics via `(float)` and TestCase already re-registers the Inertia macro with `JSON_PRESERVE_ZERO_FRACTION`. See `eyetech/inertia-assertsame-float-fix`.

---

## Task 1: Services CRUD

**Files:**
- Create: `app/Http/Controllers/App/ServiceController.php`
- Create: `app/Http/Requests/StoreServiceRequest.php`, `UpdateServiceRequest.php`
- Create: `app/Http/Resources/ServiceResource.php`
- Create: `app/Policies/ServicePolicy.php`
- Modify: `app/Providers/AppServiceProvider.php` (register policy)
- Modify: `routes/web.php`
- Create: `resources/js/Pages/Services/Index.vue`
- Modify: `resources/js/types/models.ts` (add `Service`)
- Create: `tests/Feature/App/Services/ServicesCrudTest.php`

- [ ] **Step 1: Write failing test**

`tests/Feature/App/Services/ServicesCrudTest.php`:

```php
<?php

namespace Tests\Feature\App\Services;

use App\Models\Service;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class ServicesCrudTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'cashier', 'guard_name' => 'web']);
    }

    public function test_admin_can_view_services_index(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $admin->syncRoles(['admin']);

        Service::factory()->count(3)->create();

        $this->actingAs($admin)
            ->withoutVite()
            ->get('/app/services')
            ->assertOk()
            ->assertInertia(fn ($p) => $p->component('Services/Index')->has('services.data', 3));
    }

    public function test_cashier_blocked_from_services(): void
    {
        $cashier = User::factory()->create(['role' => 'cashier']);
        $cashier->syncRoles(['cashier']);

        $this->actingAs($cashier)->get('/app/services')->assertForbidden();
    }

    public function test_admin_can_create_service(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $admin->syncRoles(['admin']);

        $this->actingAs($admin)
            ->post('/app/services', [
                'name' => 'Screen Replacement',
                'description' => 'Replace cracked screen',
                'price' => 250.00,
                'estimated_duration' => 60,
                'category' => 'repair',
                'is_active' => true,
                'is_featured' => true,
            ])
            ->assertRedirect('/app/services');

        $this->assertDatabaseHas('services', [
            'name' => 'Screen Replacement',
            'slug' => 'screen-replacement',
            'is_featured' => true,
        ]);
    }

    public function test_admin_can_update_service(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $admin->syncRoles(['admin']);
        $service = Service::factory()->create(['name' => 'Old Name']);

        $this->actingAs($admin)
            ->put("/app/services/{$service->id}", [
                'name' => 'New Name',
                'slug' => $service->slug,
                'description' => $service->description,
                'price' => $service->price,
                'is_active' => true,
                'is_featured' => false,
            ])
            ->assertRedirect('/app/services');

        $this->assertEquals('New Name', $service->fresh()->name);
    }

    public function test_admin_can_delete_service(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $admin->syncRoles(['admin']);
        $service = Service::factory()->create();

        $this->actingAs($admin)
            ->delete("/app/services/{$service->id}")
            ->assertRedirect('/app/services');

        $this->assertDatabaseMissing('services', ['id' => $service->id]);
    }
}
```

- [ ] **Step 2: Verify `ServiceFactory` exists; create if missing**

```bash
ls database/factories/ServiceFactory.php
```

If missing:
```php
<?php
namespace Database\Factories;
use App\Models\Service;
use Illuminate\Database\Eloquent\Factories\Factory;
class ServiceFactory extends Factory
{
    protected $model = Service::class;
    public function definition(): array
    {
        $name = $this->faker->unique()->words(3, true);
        return [
            'name' => $name,
            'slug' => \Illuminate\Support\Str::slug($name).'-'.$this->faker->unique()->numberBetween(1, 9999),
            'description' => $this->faker->sentence(),
            'price' => $this->faker->randomFloat(2, 50, 500),
            'estimated_duration' => $this->faker->numberBetween(15, 240),
            'category' => $this->faker->randomElement(['repair', 'unlock', 'install']),
            'is_active' => true,
            'is_featured' => false,
        ];
    }
}
```

- [ ] **Step 3: Policy + register**

```bash
php artisan make:policy ServicePolicy --model=Service
```

```php
public function viewAny(User $user): bool { return $user->role === 'admin'; }
public function view(User $user, Service $service): bool { return $user->role === 'admin'; }
public function create(User $user): bool { return $user->role === 'admin'; }
public function update(User $user, Service $service): bool { return $user->role === 'admin'; }
public function delete(User $user, Service $service): bool { return $user->role === 'admin'; }
```

In `AppServiceProvider@boot`: `Gate::policy(Service::class, ServicePolicy::class);`

- [ ] **Step 4: FormRequests**

`StoreServiceRequest`:
```php
protected function prepareForValidation(): void
{
    if (! $this->slug && $this->name) {
        $this->merge(['slug' => \Illuminate\Support\Str::slug($this->name)]);
    }
}

public function authorize(): bool { return $this->user()->can('create', \App\Models\Service::class); }

public function rules(): array
{
    return [
        'name' => ['required', 'string', 'max:255'],
        'slug' => ['required', 'string', 'max:255', 'unique:services,slug'],
        'description' => ['required', 'string'],
        'price' => ['required', 'numeric', 'min:0'],
        'estimated_duration' => ['nullable', 'integer', 'min:0'],
        'category' => ['nullable', 'string', 'max:255'],
        'is_active' => ['boolean'],
        'is_featured' => ['boolean'],
    ];
}
```

`UpdateServiceRequest`: same shape; slug rule = `['required', 'string', 'max:255', 'unique:services,slug,'.$this->route('service')->id]`.

- [ ] **Step 5: ServiceResource**

```php
return [
    'id' => $this->id,
    'name' => $this->name,
    'slug' => $this->slug,
    'description' => $this->description,
    'price' => (float) $this->price,
    'estimated_duration' => $this->estimated_duration,
    'category' => $this->category,
    'is_active' => (bool) $this->is_active,
    'is_featured' => (bool) $this->is_featured,
    'created_at' => $this->created_at?->toIso8601String(),
];
```

- [ ] **Step 6: Controller** — index, store, update, destroy. Mirror `SupplierController`. Eager load nothing; paginate 20.

- [ ] **Step 7: Routes** — inside the existing `/app` admin group:

```php
use App\Http\Controllers\App\ServiceController;
Route::get('/services', [ServiceController::class, 'index'])->name('services.index');
Route::post('/services', [ServiceController::class, 'store'])->name('services.store');
Route::put('/services/{service}', [ServiceController::class, 'update'])->name('services.update');
Route::delete('/services/{service}', [ServiceController::class, 'destroy'])->name('services.destroy');
```

- [ ] **Step 8: Vue page** — `resources/js/Pages/Services/Index.vue`. Mirror `Suppliers/Index.vue`. Form fields: name, description (textarea), price, estimated_duration, category, is_active, is_featured (toggles). Slug auto-derived server-side — don't expose unless edit.

- [ ] **Step 9: Extend `models.ts`** with `Service` interface matching ServiceResource shape.

- [ ] **Step 10: Run tests + build**

```bash
php artisan test --filter=ServicesCrudTest
npm run build
```

Both green.

- [ ] **Step 11: Commit**

```bash
git add app/Http/Controllers/App/ServiceController.php app/Http/Requests/StoreServiceRequest.php app/Http/Requests/UpdateServiceRequest.php app/Http/Resources/ServiceResource.php app/Policies/ServicePolicy.php app/Providers/AppServiceProvider.php database/factories/ServiceFactory.php routes/web.php resources/js/Pages/Services resources/js/types/models.ts tests/Feature/App/Services
git commit -m "feat(phase-3): Services CRUD at /app/services"
```

---

## Task 2: Service Requests (index + show + status workflow)

**Files:**
- Create: `app/Http/Controllers/App/ServiceRequestController.php`
- Create: `app/Http/Requests/StoreServiceRequestRequest.php`, `UpdateServiceRequestRequest.php`
- Create: `app/Http/Resources/ServiceRequestResource.php`
- Create: `app/Policies/ServiceRequestPolicy.php`
- Modify: `app/Providers/AppServiceProvider.php`
- Modify: `routes/web.php`
- Create: `resources/js/Pages/ServiceRequests/Index.vue`, `Show.vue`
- Modify: `resources/js/types/models.ts`
- Create: `tests/Feature/App/ServiceRequests/ServiceRequestsCrudTest.php`

- [ ] **Step 1: Failing test (excerpt — full file mirrors Services pattern):**

```php
public function test_status_transition_to_completed_sets_completed_at(): void
{
    $admin = User::factory()->create(['role' => 'admin']);
    $admin->syncRoles(['admin']);
    $sr = ServiceRequest::factory()->create(['status' => 'in_progress', 'completed_at' => null]);

    $this->actingAs($admin)
        ->put("/app/service-requests/{$sr->id}", array_merge($sr->only([
            'customer_name','customer_phone','customer_email','service_id',
            'device_description','problem_description','estimated_cost','final_cost','notes',
        ]), ['status' => 'completed']))
        ->assertRedirect();

    $this->assertNotNull($sr->fresh()->completed_at);
}

public function test_cashier_can_view_service_requests_but_not_modify(): void
{
    $cashier = User::factory()->create(['role' => 'cashier']);
    $cashier->syncRoles(['cashier']);
    $sr = ServiceRequest::factory()->create();

    $this->actingAs($cashier)->get('/app/service-requests')->assertOk();
    $this->actingAs($cashier)->get("/app/service-requests/{$sr->id}")->assertOk();
    $this->actingAs($cashier)->delete("/app/service-requests/{$sr->id}")->assertForbidden();
}
```

- [ ] **Step 2: Policy** — viewAny + view = admin OR cashier; create/update/delete = admin only.

- [ ] **Step 3: FormRequests** — status `in:pending,in_progress,completed,cancelled`. `service_id` required exists. `request_number` never in rules (model boot generates).

- [ ] **Step 4: Resource** with `whenLoaded('service')` shape `{id, name}`.

- [ ] **Step 5: Controller** — `update()` checks if incoming status === 'completed' and the model wasn't completed → set `completed_at = now()` server-side, regardless of request payload. Validated data goes through `fill()`.

- [ ] **Step 6: Routes** — split: `index|show` outside admin gate (inside `auth` group, after `/app/sales`), `store|update|destroy` inside the existing `role:admin` group.

- [ ] **Step 7: Vue Index.vue** — DataTable: customer, phone, service.name, status (badge), created_at; row click → Show.

- [ ] **Step 8: Vue Show.vue** — Read-only details + admin-only buttons: "Mark in-progress", "Mark completed", "Cancel". Each button posts to `update` with new status. Buttons hidden via `useAuth().isAdmin`. Status badge color via map: pending=amber, in_progress=blue, completed=green, cancelled=red.

- [ ] **Step 9: ServiceRequestFactory** — create if missing. Use `Service::factory()->create()->id` for `service_id`. Set defaults: status pending, request_number auto via model boot.

- [ ] **Step 10: Tests + build green. Commit.**

```
feat(phase-3): Service Requests at /app/service-requests with status workflow
```

---

## Task 3: Users management

**Files:**
- Create: `app/Http/Controllers/App/UserController.php`
- Create: `app/Http/Requests/StoreUserRequest.php`, `UpdateUserRequest.php`
- Create: `app/Http/Resources/UserResource.php` (id, name, email, role, phone, profile_picture, created_at — no password)
- Create: `app/Policies/UserPolicy.php`
- Modify: `app/Providers/AppServiceProvider.php`
- Modify: `routes/web.php`
- Create: `resources/js/Pages/Users/Index.vue`
- Modify: `resources/js/types/models.ts` (extend User)
- Create: `tests/Feature/App/Users/UsersCrudTest.php`

- [ ] **Step 1: Failing tests** — include:

```php
public function test_admin_cannot_delete_self(): void
{
    $admin = User::factory()->create(['role' => 'admin']);
    $admin->syncRoles(['admin']);

    $this->actingAs($admin)
        ->delete("/app/users/{$admin->id}")
        ->assertForbidden();
}

public function test_admin_can_create_cashier_with_password(): void
{
    $admin = User::factory()->create(['role' => 'admin']);
    $admin->syncRoles(['admin']);

    $this->actingAs($admin)
        ->post('/app/users', [
            'name' => 'New Cashier',
            'email' => 'cashier@eyetech.test',
            'role' => 'cashier',
            'password' => 'secret123',
            'password_confirmation' => 'secret123',
        ])
        ->assertRedirect('/app/users');

    $u = User::where('email', 'cashier@eyetech.test')->firstOrFail();
    $this->assertTrue($u->hasRole('cashier'));
    $this->assertEquals('cashier', $u->role);
}
```

- [ ] **Step 2: Policy** — viewAny/view/create/update = admin; delete = admin AND `$target->id !== $user->id`.

- [ ] **Step 3: FormRequests** — see spec §6.

- [ ] **Step 4: Controller** —

```php
public function store(StoreUserRequest $request): RedirectResponse
{
    $data = $request->validated();
    $data['password'] = bcrypt($data['password']);
    $user = User::create($data);
    $user->syncRoles([$data['role']]);
    return redirect()->route('users.index')->with('success', 'User created');
}

public function update(UpdateUserRequest $request, User $user): RedirectResponse
{
    $data = $request->validated();
    if (!empty($data['password'])) {
        $data['password'] = bcrypt($data['password']);
    } else {
        unset($data['password']);
    }
    $user->update($data);
    $user->syncRoles([$data['role']]);
    return redirect()->route('users.index')->with('success', 'User updated');
}
```

- [ ] **Step 5: Routes** — admin group: `index|store|update|destroy`. No `show`/`edit` GET pages — inline `FormDialog`.

- [ ] **Step 6: Vue Index.vue** — DataTable + FormDialog. Hide Delete button for own row (`v-if="row.id !== auth.user.id"`).

- [ ] **Step 7: Tests + build green. Commit.**

```
feat(phase-3): Users management at /app/users
```

---

## Task 4: Profile

**Files:**
- Create: `app/Http/Controllers/App/ProfileController.php` (edit + update)
- Create: `app/Http/Requests/UpdateProfileRequest.php`
- Modify: `routes/web.php`
- Create: `resources/js/Pages/Profile/Edit.vue`
- Create: `tests/Feature/App/Profile/ProfileUpdateTest.php`

- [ ] **Step 1: Failing tests** — include:

```php
public function test_authenticated_user_updates_own_name(): void
{
    $u = User::factory()->create(['name' => 'Old']);
    $this->actingAs($u)
        ->put('/app/profile', ['name' => 'New', 'email' => $u->email])
        ->assertRedirect('/app/profile');
    $this->assertEquals('New', $u->fresh()->name);
}

public function test_password_change_requires_current_password(): void
{
    $u = User::factory()->create(['password' => bcrypt('correct')]);
    $this->actingAs($u)
        ->put('/app/profile', [
            'name' => $u->name, 'email' => $u->email,
            'current_password' => 'wrong',
            'password' => 'newpass1',
            'password_confirmation' => 'newpass1',
        ])
        ->assertSessionHasErrors('current_password');
}
```

- [ ] **Step 2: FormRequest** —

```php
public function authorize(): bool { return $this->user() !== null; }

public function rules(): array
{
    $user = $this->user();
    return [
        'name' => ['required', 'string', 'max:255'],
        'email' => ['required', 'email', 'max:255', 'unique:users,email,'.$user->id],
        'phone' => ['nullable', 'string', 'max:255'],
        'profile_picture' => ['nullable', 'image', 'max:2048'],
        'current_password' => ['required_with:password', 'current_password'],
        'password' => ['nullable', 'string', 'min:8', 'confirmed'],
    ];
}
```

- [ ] **Step 3: Controller** —

```php
public function edit(Request $request): Response
{
    return Inertia::render('Profile/Edit', [
        'user' => UserResource::make($request->user())->resolve(),
    ]);
}

public function update(UpdateProfileRequest $request): RedirectResponse
{
    $user = $request->user();
    $data = $request->validated();

    if ($request->hasFile('profile_picture')) {
        $data['profile_picture'] = $request->file('profile_picture')->store('avatars', 'public');
    } else {
        unset($data['profile_picture']);
    }

    if (!empty($data['password'])) {
        $data['password'] = bcrypt($data['password']);
    } else {
        unset($data['password']);
    }
    unset($data['current_password']);

    $user->update($data);
    return redirect()->route('profile.edit')->with('success', 'Profile updated');
}
```

- [ ] **Step 4: Routes** — inside `auth` group, no role gate:

```php
Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
```

- [ ] **Step 5: Vue Edit.vue** — three sections: profile info, change password (collapsed), avatar upload. Use `forceFormData: true` + `_method: 'put'` for the multipart submit.

- [ ] **Step 6: Tests + build green. Commit.**

```
feat(phase-3): Profile page at /app/profile
```

---

## Task 5: Reports + Custom Reports

**Files:**
- Create: `app/Services/ReportService.php`
- Create: `app/Http/Controllers/App/ReportController.php` (index + custom)
- Modify: `routes/web.php`
- Create: `resources/js/Pages/Reports/Index.vue`, `Custom.vue`
- Modify: `resources/js/types/models.ts` (add `ReportPayload`, `CustomReportPayload`)
- Create: `tests/Feature/App/Reports/ReportsTest.php`, `CustomReportsTest.php`

- [ ] **Step 1: Recon — open the Filament pages and port their queries verbatim**

```bash
cat app/Filament/Pages/Reports.php
cat app/Filament/Pages/CustomReports.php
```

Extract every query into `ReportService` methods. Pass `(Carbon $from, Carbon $to, int $topLimit, int $chartDays)` so one service serves both reports.

- [ ] **Step 2: Failing tests** — for each report, seed sales + service requests + low-stock product, hit endpoint, assert payload shape + a few values.

```php
public function test_reports_returns_totals_for_date_range(): void
{
    $admin = User::factory()->create(['role' => 'admin']);
    $admin->syncRoles(['admin']);

    Sales::factory()->create([
        'user_id' => $admin->id,
        'status' => 'completed',
        'total_amount' => 100,
        'created_at' => now()->subDay(),
    ]);

    $this->actingAs($admin)
        ->withoutVite()
        ->get('/app/reports?from='.now()->subDays(7)->toDateString().'&to='.now()->toDateString())
        ->assertOk()
        ->assertInertia(fn ($p) => $p
            ->component('Reports/Index')
            ->where('totals.sales_count', 1)
            ->where('totals.revenue', 100.0)
            ->has('top_products')
            ->has('daily_chart', 7)
        );
}
```

- [ ] **Step 3: `ReportService`** — public methods:
  - `totals(Carbon $from, Carbon $to): array`
  - `growth(Carbon $from, Carbon $to): array` (vs prior equal-length window)
  - `dailyChart(Carbon $from, Carbon $to): array`
  - `topProducts(Carbon $from, Carbon $to, int $limit): array`
  - `topStaff(Carbon $from, Carbon $to, int $limit): array`
  - `salesByStatus(Carbon $from, Carbon $to): array`
  - `salesByPayment(Carbon $from, Carbon $to): array`
  - `serviceRequestKpis(): array`
  - `inventoryKpis(): array`

- [ ] **Step 4: Controller** —

```php
public function index(Request $request, ReportService $svc): Response
{
    $this->authorize('viewAny', User::class); // admin only — reuse UserPolicy
    [$from, $to] = $this->range($request);
    return Inertia::render('Reports/Index', [
        'range' => ['from' => $from->toDateString(), 'to' => $to->toDateString()],
        'totals' => $svc->totals($from, $to),
        'growth' => $svc->growth($from, $to),
        'daily_chart' => $svc->dailyChart($from, $to),  // controller asks for 7-day window upstream
        'top_products' => $svc->topProducts($from, $to, 5),
        'top_staff' => $svc->topStaff($from, $to, 5),
        'sales_by_status' => $svc->salesByStatus($from, $to),
        'sales_by_payment' => $svc->salesByPayment($from, $to),
    ]);
}

public function custom(Request $request, ReportService $svc): Response
{
    $this->authorize('viewAny', User::class);
    [$from, $to] = $this->range($request, 30);
    return Inertia::render('Reports/Custom', [
        'range' => ['from' => $from->toDateString(), 'to' => $to->toDateString()],
        'totals' => $svc->totals($from, $to),
        'growth' => $svc->growth($from, $to),
        'daily_chart' => $svc->dailyChart($from, $to),
        'top_products' => $svc->topProducts($from, $to, 10),
        'top_staff' => $svc->topStaff($from, $to, 10),
        'sales_by_status' => $svc->salesByStatus($from, $to),
        'sales_by_payment' => $svc->salesByPayment($from, $to),
        'service_requests' => $svc->serviceRequestKpis(),
        'inventory' => $svc->inventoryKpis(),
    ]);
}

private function range(Request $request, int $defaultDays = 7): array
{
    $from = $request->filled('from') ? \Carbon\Carbon::parse($request->input('from'))->startOfDay() : today()->subDays($defaultDays);
    $to = $request->filled('to') ? \Carbon\Carbon::parse($request->input('to'))->endOfDay() : today()->endOfDay();
    return [$from, $to];
}
```

Note: reuse `UserPolicy::viewAny` since we already gate the route via `role:admin` middleware — the inline authorize is belt-and-braces. Alternative: drop the inline `$this->authorize` and rely only on the route middleware.

- [ ] **Step 5: Routes** — admin group:

```php
Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
Route::get('/reports/custom', [ReportController::class, 'custom'])->name('reports.custom');
```

- [ ] **Step 6: Vue pages** — date-range inputs (two `<input type="date">`), submit reloads via `router.get('/app/reports', { from, to }, { preserveState: false })`. Reuse the inline SVG bar chart from `Dashboard.vue`. StatsCard grid + tables for top products/staff.

- [ ] **Step 7: Tests + build green. Commit (one commit per report file):**

```
feat(phase-3): Reports page at /app/reports
feat(phase-3): Custom Reports page at /app/reports/custom
```

---

## Task 6: AppLayout nav + final smoke

- [ ] **Step 1: Add nav links** to `resources/js/Layouts/AppLayout.vue` after existing admin links:

```vue
<Link v-if="isAdmin" href="/app/services" class="hover:underline">Services</Link>
<Link href="/app/service-requests" class="hover:underline">Service Requests</Link>
<Link v-if="isAdmin" href="/app/users" class="hover:underline">Users</Link>
<Link v-if="isAdmin" href="/app/reports" class="hover:underline">Reports</Link>
```

User-menu dropdown gets a `Profile` link → `/app/profile`.

- [ ] **Step 2: vue-tsc + build + full test suite**

```bash
npx vue-tsc --noEmit
npm run build
php artisan test
```

All green.

- [ ] **Step 3: Manual smoke (admin user)**
  - Create a service, edit it, delete it.
  - File a service request, mark it completed, verify `completed_at` is set in the list.
  - Add a new cashier user, verify they can log in.
  - Update own profile name, change password.
  - Open Reports — date filter narrows totals.
  - Open Custom Reports — service-request KPIs render.

- [ ] **Step 4: Tag**

```bash
git tag phase-3-complete -m "Phase 3: Services, Service Requests, Users, Profile, Reports, Custom Reports"
```

- [ ] **Step 5: Store memory**

After phase complete:
```
key: eyetech/phase-3-complete
namespace: eyetech
value: Phase 3 done. Services + ServiceRequests + Users + Profile + Reports + CustomReports all at /app. ReportService consolidates query logic. UserPolicy gates delete-self. Commit range: <first-sha>..<last-sha>. Tag: phase-3-complete. Next: Phase 4 = strip Filament.
```

---

## Self-Review

**Spec coverage:**
- Services CRUD ✓ (Task 1)
- Service Requests + status workflow ✓ (Task 2)
- Users + delete-self guard ✓ (Task 3)
- Profile + password change ✓ (Task 4)
- Reports + Custom Reports ✓ (Task 5)
- Nav update ✓ (Task 6)
- Admin-only policies ✓
- Cashier read-only on service requests ✓

**Placeholder scan:** none.

**Type consistency:** Service, ServiceRequest, ReportPayload, CustomReportPayload added to models.ts.

**Risks tracked:** spec §10.

**Order rationale:** Services first (smallest, mirrors Suppliers exactly). Then ServiceRequests (extends pattern with Show page + status). Then Users (delete-self edge case). Then Profile (self-only, no policy). Reports last (largest, depends on existing Sales/Product/ServiceRequest factories already covered by earlier phases).

---

**End of Phase 3 Plan.** Phase 4 plan (Filament removal) to be written after Phase 3 ships and brother confirms daily usage on `/app` only.
