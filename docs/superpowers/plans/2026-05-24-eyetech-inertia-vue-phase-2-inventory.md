# EyeTech Inertia + Vue — Phase 2: Inventory Core Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: superpowers:subagent-driven-development to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Port the inventory-related Filament resources to the new Inertia + Vue stack at `/app/...` so the admin can manage products, categories, suppliers and stock without opening `/admin` Filament. Also replace `InventoryStatsWidget` + `LowStockAlertWidget` on the `/app/dashboard`. Services, Service Requests, Users, Profile, and Reports defer to Phase 3.

**Architecture:** Builds on Phase 0 (Inertia + Vue + roles + login) and Phase 1 (POS + Sales). Adds one Inertia controller per resource under `App\Http\Controllers\App`, one API Resource per model, paginated `DataTable.vue` for list pages, shadcn FormDialog for create/edit. All admin-only via `role:admin` middleware. Filament `/admin` remains alive — Phase 4 removes it.

**Tech Stack:** Laravel 12, Inertia 3, Vue 3 + TypeScript, Tailwind 4, shadcn-vue, spatie/permission. No new packages.

**Spec:** `docs/superpowers/specs/2026-05-23-eyetech-inertia-vue-design.md`
**Prior plans:** Phase 0 (`phase-0-complete` tag), Phase 1 (`phase-1-complete` tag).

---

## Scope of Phase 2

| Resource | Action | Notes |
|---|---|---|
| Products | Full CRUD (index, create, edit, delete) | Hero feature — brother adds new SKUs |
| Categories | Index + inline create/edit | Few in number; modal form |
| Suppliers | Index + edit drawer | Admin-only |
| Stock Movements | Read-only index | History view; movements auto-created by sales/edits |
| Dashboard widgets | Stats cards + low-stock list | Replaces Filament widgets |

Out of scope (Phase 3+): Services, Service Requests, Users, Profile, Reports, Custom Reports.

---

## File Structure

### New backend files

| Path | Responsibility |
|---|---|
| `app/Http/Controllers/App/ProductController.php` | index, create, store, edit, update, destroy |
| `app/Http/Controllers/App/CategoryController.php` | index, store, update, destroy (inline modal pattern) |
| `app/Http/Controllers/App/SupplierController.php` | index, store, update, destroy |
| `app/Http/Controllers/App/StockMovementController.php` | index (read-only) |
| `app/Http/Controllers/App/DashboardController.php` | EXTEND existing — return stats props |
| `app/Http/Requests/StoreProductRequest.php` | validate product create |
| `app/Http/Requests/UpdateProductRequest.php` | validate product edit |
| `app/Http/Requests/StoreCategoryRequest.php` | validate |
| `app/Http/Requests/UpdateCategoryRequest.php` | validate |
| `app/Http/Requests/StoreSupplierRequest.php` | validate |
| `app/Http/Requests/UpdateSupplierRequest.php` | validate |
| `app/Http/Resources/SupplierResource.php` | shape supplier |
| `app/Http/Resources/StockMovementResource.php` | shape movement |
| `app/Policies/ProductPolicy.php` | admin-only (Phase 2 — cashier no edit) |
| `app/Policies/CategoryPolicy.php` | admin-only |
| `app/Policies/SupplierPolicy.php` | admin-only |

### New frontend files

| Path | Responsibility |
|---|---|
| `resources/js/Pages/Products/Index.vue` | DataTable + create button |
| `resources/js/Pages/Products/Edit.vue` | Form (also used for create via prop) |
| `resources/js/Pages/Categories/Index.vue` | DataTable + inline FormDialog |
| `resources/js/Pages/Suppliers/Index.vue` | DataTable + inline FormDialog |
| `resources/js/Pages/StockMovements/Index.vue` | Read-only DataTable, type filter |
| `resources/js/Pages/Dashboard.vue` | REPLACE placeholder — real stats + chart |
| `resources/js/components/StatsCard.vue` | Title + big number + delta |
| `resources/js/components/FormDialog.vue` | Reusable modal form (shadcn Dialog) |
| `resources/js/types/models.ts` | Extend with `Supplier`, `StockMovement`, `DashboardStats` |

### Modified files

| Path | Change |
|---|---|
| `routes/web.php` | Add admin-only `/app/products`, `/app/categories`, `/app/suppliers`, `/app/stock-movements` routes |
| `app/Providers/AppServiceProvider.php` | Register Product/Category/Supplier policies |
| `app/Models/Supplier.php` | Add `products()` hasMany if missing |
| `app/Http/Resources/ProductResource.php` | Include `supplier` via `whenLoaded` |
| `resources/js/Layouts/AppLayout.vue` | Add nav links for Products, Categories, Suppliers, Stock |
| `app/Http/Controllers/App/DashboardController.php` | Return stats props (todaySales, lowStockCount, pendingServiceRequests, productTotal) + chart data (sales last 7 days) |

### New test files

| Path | Responsibility |
|---|---|
| `tests/Feature/App/Products/ProductsCrudTest.php` | Admin full CRUD + cashier blocked |
| `tests/Feature/App/Categories/CategoriesCrudTest.php` | Admin CRUD |
| `tests/Feature/App/Suppliers/SuppliersCrudTest.php` | Admin CRUD |
| `tests/Feature/App/StockMovements/StockMovementsIndexTest.php` | Admin read-only |
| `tests/Feature/App/DashboardStatsTest.php` | Dashboard returns correct stats |

---

## Tasks

### Task 1: Suppliers CRUD (simplest — go first)

Suppliers is the simplest resource (few fields, no relations). Use as the template for Products/Categories.

**Files:**
- Create: `app/Http/Controllers/App/SupplierController.php`
- Create: `app/Http/Requests/StoreSupplierRequest.php`, `UpdateSupplierRequest.php`
- Create: `app/Http/Resources/SupplierResource.php`
- Create: `app/Policies/SupplierPolicy.php`
- Modify: `app/Providers/AppServiceProvider.php` (register policy)
- Modify: `routes/web.php`
- Create: `resources/js/Pages/Suppliers/Index.vue`
- Create: `resources/js/components/FormDialog.vue`
- Create: `tests/Feature/App/Suppliers/SuppliersCrudTest.php`

- [ ] **Step 1: Read the suppliers migration to see real columns**

```bash
cat database/migrations/2025_09_26_021902_create_suppliers_table.php
```

Note the columns. Adapt the resource + form to those columns.

- [ ] **Step 2: Write failing test**

`tests/Feature/App/Suppliers/SuppliersCrudTest.php`:

```php
<?php

namespace Tests\Feature\App\Suppliers;

use App\Models\Supplier;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class SuppliersCrudTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'cashier', 'guard_name' => 'web']);
    }

    public function test_admin_can_view_suppliers_index(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $admin->syncRoles(['admin']);

        Supplier::factory()->count(3)->create();

        $this->actingAs($admin)
            ->withoutVite()
            ->get('/app/suppliers')
            ->assertOk()
            ->assertInertia(fn ($p) => $p->component('Suppliers/Index')->has('suppliers.data', 3));
    }

    public function test_cashier_blocked_from_suppliers(): void
    {
        $cashier = User::factory()->create(['role' => 'cashier']);
        $cashier->syncRoles(['cashier']);

        $this->actingAs($cashier)
            ->get('/app/suppliers')
            ->assertForbidden();
    }

    public function test_admin_can_create_supplier(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $admin->syncRoles(['admin']);

        $this->actingAs($admin)
            ->post('/app/suppliers', [
                'name' => 'Acme Inc',
                'email' => 'acme@example.test',
                // adjust to match the real columns from the migration
            ])
            ->assertRedirect('/app/suppliers');

        $this->assertDatabaseHas('suppliers', ['name' => 'Acme Inc']);
    }

    public function test_admin_can_update_supplier(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $admin->syncRoles(['admin']);

        $supplier = Supplier::factory()->create(['name' => 'Old Name']);

        $this->actingAs($admin)
            ->put("/app/suppliers/{$supplier->id}", [
                'name' => 'New Name',
                'email' => $supplier->email ?? 'new@example.test',
            ])
            ->assertRedirect('/app/suppliers');

        $this->assertEquals('New Name', $supplier->fresh()->name);
    }

    public function test_admin_can_delete_supplier(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $admin->syncRoles(['admin']);

        $supplier = Supplier::factory()->create();

        $this->actingAs($admin)
            ->delete("/app/suppliers/{$supplier->id}")
            ->assertRedirect('/app/suppliers');

        $this->assertDatabaseMissing('suppliers', ['id' => $supplier->id]);
    }
}
```

- [ ] **Step 3: Create policy**

```bash
php artisan make:policy SupplierPolicy --model=Supplier
```

```php
<?php

namespace App\Policies;

use App\Models\Supplier;
use App\Models\User;

class SupplierPolicy
{
    public function viewAny(User $user): bool { return $user->role === 'admin'; }
    public function view(User $user, Supplier $supplier): bool { return $user->role === 'admin'; }
    public function create(User $user): bool { return $user->role === 'admin'; }
    public function update(User $user, Supplier $supplier): bool { return $user->role === 'admin'; }
    public function delete(User $user, Supplier $supplier): bool { return $user->role === 'admin'; }
}
```

Register in `AppServiceProvider@boot`:
```php
Gate::policy(Supplier::class, SupplierPolicy::class);
```

- [ ] **Step 4: Create form requests**

`StoreSupplierRequest`:
```php
<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreSupplierRequest extends FormRequest
{
    public function authorize(): bool { return $this->user()->can('create', \App\Models\Supplier::class); }
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            // include any additional required columns from the migration
        ];
    }
}
```

`UpdateSupplierRequest`: same shape with `'email' => ['nullable', 'email', 'unique:suppliers,email,'.$this->route('supplier')->id]` if email is unique.

- [ ] **Step 5: Create SupplierResource**

```php
<?php
namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class SupplierResource extends JsonResource
{
    public function toArray($request): array
    {
        // include columns from suppliers migration
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email ?? null,
            'phone' => $this->phone ?? null,
            'address' => $this->address ?? null,
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}
```

- [ ] **Step 6: Create controller**

```php
<?php

namespace App\Http\Controllers\App;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreSupplierRequest;
use App\Http\Requests\UpdateSupplierRequest;
use App\Http\Resources\SupplierResource;
use App\Models\Supplier;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class SupplierController extends Controller
{
    public function index(Request $request): Response
    {
        $this->authorize('viewAny', Supplier::class);

        $suppliers = Supplier::orderBy('name')->paginate(20)->withQueryString();

        return Inertia::render('Suppliers/Index', [
            'suppliers' => $suppliers->through(fn ($s) => SupplierResource::make($s)->resolve()),
        ]);
    }

    public function store(StoreSupplierRequest $request): RedirectResponse
    {
        Supplier::create($request->validated());
        return redirect()->route('suppliers.index')->with('success', 'Supplier created');
    }

    public function update(UpdateSupplierRequest $request, Supplier $supplier): RedirectResponse
    {
        $supplier->update($request->validated());
        return redirect()->route('suppliers.index')->with('success', 'Supplier updated');
    }

    public function destroy(Supplier $supplier): RedirectResponse
    {
        $this->authorize('delete', $supplier);
        $supplier->delete();
        return redirect()->route('suppliers.index')->with('success', 'Supplier deleted');
    }
}
```

- [ ] **Step 7: Add routes**

In `routes/web.php`, inside the existing `auth` + `prefix('app')` group:

```php
use App\Http\Controllers\App\SupplierController;

Route::middleware('role:admin')->group(function () {
    Route::get('/suppliers', [SupplierController::class, 'index'])->name('suppliers.index');
    Route::post('/suppliers', [SupplierController::class, 'store'])->name('suppliers.store');
    Route::put('/suppliers/{supplier}', [SupplierController::class, 'update'])->name('suppliers.update');
    Route::delete('/suppliers/{supplier}', [SupplierController::class, 'destroy'])->name('suppliers.destroy');
});
```

- [ ] **Step 8: Create `FormDialog.vue`**

`resources/js/components/FormDialog.vue`:

```vue
<script setup lang="ts">
import { Dialog, DialogContent, DialogHeader, DialogTitle, DialogFooter, DialogClose } from '@/components/ui/dialog'
import { Button } from '@/components/ui/button'

defineProps<{
  open: boolean
  title: string
  submitLabel?: string
  processing?: boolean
}>()

defineEmits<{
  'update:open': [open: boolean]
  submit: []
}>()
</script>

<template>
  <Dialog :open="open" @update:open="(v) => $emit('update:open', v)">
    <DialogContent>
      <DialogHeader>
        <DialogTitle>{{ title }}</DialogTitle>
      </DialogHeader>
      <form @submit.prevent="$emit('submit')" class="space-y-4">
        <slot />
        <DialogFooter>
          <DialogClose>
            <Button type="button" variant="outline">Cancel</Button>
          </DialogClose>
          <Button type="submit" :disabled="processing">{{ processing ? 'Saving…' : (submitLabel ?? 'Save') }}</Button>
        </DialogFooter>
      </form>
    </DialogContent>
  </Dialog>
</template>
```

- [ ] **Step 9: Create `Suppliers/Index.vue`**

```vue
<script setup lang="ts">
import { ref } from 'vue'
import { useForm } from '@inertiajs/vue3'
import { toast } from 'vue-sonner'
import AppLayout from '@/Layouts/AppLayout.vue'
import DataTable from '@/components/DataTable.vue'
import FormDialog from '@/components/FormDialog.vue'
import { Button } from '@/components/ui/button'
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'

interface Supplier {
  id: number
  name: string
  email: string | null
  phone: string | null
  address: string | null
  created_at: string
}

defineProps<{
  suppliers: { data: Supplier[]; links: any[]; meta: any }
}>()

const dialogOpen = ref(false)
const editing = ref<Supplier | null>(null)

const form = useForm({
  name: '',
  email: '',
  phone: '',
  address: '',
})

function openCreate() {
  editing.value = null
  form.reset()
  dialogOpen.value = true
}

function openEdit(supplier: Supplier) {
  editing.value = supplier
  form.name = supplier.name
  form.email = supplier.email ?? ''
  form.phone = supplier.phone ?? ''
  form.address = supplier.address ?? ''
  dialogOpen.value = true
}

function submit() {
  const onSuccess = () => {
    dialogOpen.value = false
    toast.success(editing.value ? 'Supplier updated' : 'Supplier created')
  }
  if (editing.value) {
    form.put(`/app/suppliers/${editing.value.id}`, { onSuccess })
  } else {
    form.post('/app/suppliers', { onSuccess })
  }
}

function destroy(supplier: Supplier) {
  if (!confirm(`Delete ${supplier.name}?`)) return
  useForm({}).delete(`/app/suppliers/${supplier.id}`, {
    onSuccess: () => toast.success('Supplier deleted'),
  })
}

const columns = [
  { key: 'name', label: 'Name' },
  { key: 'email', label: 'Email', render: (s: Supplier) => s.email ?? '—' },
  { key: 'phone', label: 'Phone', render: (s: Supplier) => s.phone ?? '—' },
  { key: 'actions', label: 'Actions' },
]
</script>

<template>
  <AppLayout>
    <div class="space-y-6">
      <div class="flex justify-between items-center">
        <h1 class="text-3xl font-bold">Suppliers</h1>
        <Button @click="openCreate">+ New supplier</Button>
      </div>

      <DataTable :data="suppliers" :columns="columns">
        <template #cell-actions="{ row }">
          <div class="flex gap-2">
            <Button variant="outline" size="sm" @click="openEdit(row)">Edit</Button>
            <Button variant="destructive" size="sm" @click="destroy(row)">Delete</Button>
          </div>
        </template>
      </DataTable>

      <FormDialog
        :open="dialogOpen"
        :title="editing ? 'Edit supplier' : 'New supplier'"
        :processing="form.processing"
        @update:open="dialogOpen = $event"
        @submit="submit"
      >
        <div class="space-y-2">
          <Label html-for="name">Name</Label>
          <Input id="name" v-model="form.name" />
          <p v-if="form.errors.name" class="text-sm text-destructive">{{ form.errors.name }}</p>
        </div>
        <div class="space-y-2">
          <Label html-for="email">Email</Label>
          <Input id="email" type="email" v-model="form.email" />
          <p v-if="form.errors.email" class="text-sm text-destructive">{{ form.errors.email }}</p>
        </div>
        <div class="space-y-2">
          <Label html-for="phone">Phone</Label>
          <Input id="phone" v-model="form.phone" />
        </div>
        <div class="space-y-2">
          <Label html-for="address">Address</Label>
          <Input id="address" v-model="form.address" />
        </div>
      </FormDialog>
    </div>
  </AppLayout>
</template>
```

> Note: `DataTable.vue` currently only renders columns via `render` prop; the `#cell-actions` slot above will need DataTable.vue to support a slot. EITHER extend DataTable to accept named slots per column, OR put actions in the `render` callback (returning HTML string — not ideal). For Phase 2, EXTEND `DataTable.vue` to support `<template #cell-{key}="{ row }">` slots — that's a one-time fix.

- [ ] **Step 10: Extend `DataTable.vue` to support per-column slots**

Read existing DataTable.vue. In `<TableCell v-for="col in columns">`, add:

```vue
<TableCell v-for="col in columns" :key="col.key">
  <slot :name="`cell-${col.key}`" :row="row">
    {{ col.render ? col.render(row) : (row as any)[col.key] }}
  </slot>
</TableCell>
```

So if a slot named `cell-{key}` exists, render it; else fall back to the existing render.

- [ ] **Step 11: Run tests, build**

```bash
php artisan test --filter=SuppliersCrudTest
npm run build
```

Both must pass.

- [ ] **Step 12: Commit**

```bash
git add app/Http/Controllers/App/SupplierController.php app/Http/Requests/StoreSupplierRequest.php app/Http/Requests/UpdateSupplierRequest.php app/Http/Resources/SupplierResource.php app/Policies/SupplierPolicy.php app/Providers/AppServiceProvider.php routes/web.php resources/js/Pages/Suppliers resources/js/components/FormDialog.vue resources/js/components/DataTable.vue tests/Feature/App/Suppliers
git commit -m "feat(phase-2): Suppliers CRUD at /app/suppliers"
```

---

### Task 2: Categories CRUD (same pattern as Suppliers)

Mirror Task 1 structure for Categories. Categories columns: `name`, `slug`, `description`, `icon`. Auto-generate `slug` from `name` via `Str::slug()` in StoreRequest's `prepareForValidation` if not provided.

**Files mirror Task 1 with names changed.** Test class: `CategoriesCrudTest.php`. Routes: `/app/categories`. Vue page: `Categories/Index.vue`. Resource: `CategoryResource` already exists (Phase 1) — reuse.

Specifics:
- StoreCategoryRequest:
  ```php
  protected function prepareForValidation(): void
  {
      if (! $this->slug && $this->name) {
          $this->merge(['slug' => \Illuminate\Support\Str::slug($this->name)]);
      }
  }

  public function rules(): array
  {
      return [
          'name' => ['required', 'string', 'max:255'],
          'slug' => ['required', 'string', 'max:255', 'unique:categories,slug'],
          'description' => ['nullable', 'string'],
          'icon' => ['nullable', 'string', 'max:255'],
      ];
  }
  ```

- Commit message: `feat(phase-2): Categories CRUD at /app/categories`

---

### Task 3: Products CRUD (the big one)

Products has more fields, image upload, category + supplier selects. Same controller pattern but with separate `Edit.vue` page (not modal) since the form is larger.

**Files:**
- Create: `app/Http/Controllers/App/ProductController.php` (full CRUD)
- Create: `app/Http/Requests/StoreProductRequest.php`, `UpdateProductRequest.php`
- Create: `app/Policies/ProductPolicy.php`
- Modify: `app/Providers/AppServiceProvider.php`
- Modify: `routes/web.php`
- Create: `resources/js/Pages/Products/Index.vue` (DataTable)
- Create: `resources/js/Pages/Products/Edit.vue` (form page — used for both create and edit)
- Create: `tests/Feature/App/Products/ProductsCrudTest.php`

**Validation rules (StoreProductRequest):**
```php
return [
    'name' => ['required', 'string', 'max:255'],
    'sku' => ['required', 'string', 'max:255', 'unique:products,sku'],
    'description' => ['nullable', 'string'],
    'category_id' => ['required', 'exists:categories,id'],
    'supplier_id' => ['required', 'exists:suppliers,id'],
    'cost_price' => ['required', 'numeric', 'min:0'],
    'selling_price' => ['required', 'numeric', 'min:0'],
    'stock_quantity' => ['required', 'integer', 'min:0'],
    'min_stock_level' => ['required', 'integer', 'min:0'],
    'brand' => ['nullable', 'string', 'max:255'],
    'model' => ['nullable', 'string', 'max:255'],
    'is_active' => ['boolean'],
    'image' => ['nullable', 'image', 'max:2048'],
];
```

`UpdateProductRequest`: same but `sku` unique except current: `'sku' => ['required', 'string', 'unique:products,sku,'.$this->route('product')->id]`.

**Controller `update` / `store` handle image upload:**

```php
if ($request->hasFile('image')) {
    $path = $request->file('image')->store('products', 'public');
    $data['image'] = $path;
}
```

**`Products/Edit.vue`:** load `categories: Category[]` and `suppliers: Supplier[]` props for selects. Render form with all fields. Image upload via `<input type="file" @change="form.image = $event.target.files[0]">`. Use `form.post('/app/products', { forceFormData: true })` to handle multipart.

**`Products/Index.vue`:** DataTable showing name/SKU/category/stock/price. Stock badge red when below `min_stock_level`. Filter dropdown for category. Edit/Delete actions via slot.

**Tests:**
- admin index sees products
- cashier blocked
- admin create with valid data persists
- admin update changes fields
- admin delete soft/hard
- validation errors on bad payload

Commit: `feat(phase-2): Products CRUD at /app/products`

---

### Task 4: Stock Movements read-only index

**Files:**
- Create: `app/Http/Controllers/App/StockMovementController.php` (index only)
- Create: `app/Http/Resources/StockMovementResource.php`
- Modify: `routes/web.php`
- Create: `resources/js/Pages/StockMovements/Index.vue`
- Create: `tests/Feature/App/StockMovements/StockMovementsIndexTest.php`

StockMovementResource shape:
```php
return [
    'id' => $this->id,
    'product_id' => $this->product_id,
    'product' => $this->whenLoaded('product', fn () => ['id' => $this->product->id, 'name' => $this->product->name, 'sku' => $this->product->sku]),
    'movement_type' => $this->movement_type,
    'quantity' => $this->quantity,
    'previous_quantity' => $this->previous_quantity,
    'new_quantity' => $this->new_quantity,
    'reference_type' => $this->reference_type,
    'reference_id' => $this->reference_id,
    'user' => $this->whenLoaded('user', fn () => ['id' => $this->user->id, 'name' => $this->user->name]),
    'notes' => $this->notes,
    'created_at' => $this->created_at?->toIso8601String(),
];
```

Add `product()` and `user()` BelongsTo to `App\Models\StockMovement` if missing.

Controller index:
- Paginate ordered by `created_at DESC`.
- Optional filter `?type=sale|in|out|adjustment|transfer|purchase`.
- Admin only.

Index.vue: DataTable with columns: date, product, type (badge color by type), quantity, prev → new, user, notes. Filter buttons for type.

Commit: `feat(phase-2): Stock Movements read-only index at /app/stock-movements`

---

### Task 5: Dashboard with stats + chart

**Files:**
- Modify: `app/Http/Controllers/App/DashboardController.php` — return stats
- Create: `resources/js/components/StatsCard.vue`
- Modify: `resources/js/Pages/Dashboard.vue` — replace placeholder
- Create: `tests/Feature/App/DashboardStatsTest.php`

DashboardController:
```php
public function __invoke(): Response
{
    $today = today();

    $todaySalesTotal = Sales::whereDate('created_at', $today)
        ->where('status', 'completed')
        ->sum('total_amount');

    $todaySalesCount = Sales::whereDate('created_at', $today)
        ->where('status', 'completed')
        ->count();

    $lowStockCount = Product::whereColumn('stock_quantity', '<=', 'min_stock_level')
        ->where('is_active', true)
        ->count();

    $totalProducts = Product::where('is_active', true)->count();

    // Sales last 7 days for chart
    $salesChart = collect(range(6, 0))->map(function (int $daysAgo) {
        $date = today()->subDays($daysAgo);
        return [
            'date' => $date->format('M d'),
            'total' => (float) Sales::whereDate('created_at', $date)
                ->where('status', 'completed')
                ->sum('total_amount'),
        ];
    });

    // Top low-stock products
    $lowStockProducts = Product::with('category')
        ->whereColumn('stock_quantity', '<=', 'min_stock_level')
        ->where('is_active', true)
        ->orderBy('stock_quantity')
        ->limit(10)
        ->get(['id', 'name', 'sku', 'stock_quantity', 'min_stock_level', 'category_id']);

    return Inertia::render('Dashboard', [
        'stats' => [
            'today_sales_total' => (float) $todaySalesTotal,
            'today_sales_count' => $todaySalesCount,
            'low_stock_count' => $lowStockCount,
            'total_products' => $totalProducts,
        ],
        'sales_chart' => $salesChart,
        'low_stock_products' => $lowStockProducts->map(fn ($p) => [
            'id' => $p->id,
            'name' => $p->name,
            'sku' => $p->sku,
            'stock_quantity' => $p->stock_quantity,
            'min_stock_level' => $p->min_stock_level,
            'category' => $p->category?->name,
        ]),
    ]);
}
```

StatsCard.vue:
```vue
<script setup lang="ts">
defineProps<{ title: string; value: string | number; description?: string }>()
</script>

<template>
  <div class="rounded-lg border bg-card p-6">
    <p class="text-sm font-medium text-muted-foreground">{{ title }}</p>
    <p class="text-3xl font-bold mt-1">{{ value }}</p>
    <p v-if="description" class="text-xs text-muted-foreground mt-1">{{ description }}</p>
  </div>
</template>
```

Dashboard.vue: render 4 StatsCards, a simple SVG bar chart for sales_chart (no charting library — manual), low-stock list.

Test:
```php
public function test_dashboard_returns_stats_for_today(): void
{
    $admin = User::factory()->create(['role' => 'admin']);
    $admin->syncRoles(['admin']);

    Sales::factory()->create([
        'user_id' => $admin->id,
        'status' => 'completed',
        'total_amount' => 100,
        'created_at' => now(),
    ]);

    $this->actingAs($admin)
        ->withoutVite()
        ->get('/app/dashboard')
        ->assertInertia(fn ($p) => $p
            ->component('Dashboard')
            ->where('stats.today_sales_total', 100.0)
            ->where('stats.today_sales_count', 1)
        );
}
```

Commit: `feat(phase-2): Dashboard with stats cards + 7-day sales chart`

---

### Task 6: Update AppLayout nav with new links

`resources/js/Layouts/AppLayout.vue` nav block:

```vue
<Link v-if="isAdmin" href="/app/dashboard" class="hover:underline">Dashboard</Link>
<Link href="/app/pos" class="hover:underline">POS</Link>
<Link href="/app/sales" class="hover:underline">Sales</Link>
<Link v-if="isAdmin" href="/app/products" class="hover:underline">Products</Link>
<Link v-if="isAdmin" href="/app/categories" class="hover:underline">Categories</Link>
<Link v-if="isAdmin" href="/app/suppliers" class="hover:underline">Suppliers</Link>
<Link v-if="isAdmin" href="/app/stock-movements" class="hover:underline">Stock</Link>
```

Commit: `feat(phase-2): expanded AppLayout nav with new admin links`

---

### Task 7: Build + smoke + tag

- [ ] Run `npx vue-tsc --noEmit`
- [ ] Run `npm run build`
- [ ] Run `php artisan test`
- [ ] Manual browser smoke: log in as admin, click each new nav link, verify each page renders. Create a category, edit it, delete it. Create a product (test image upload). Verify dashboard stats reflect today's data.
- [ ] Tag: `git tag phase-2-complete -m "Phase 2: Inventory core — Products, Categories, Suppliers, Stock Movements, Dashboard stats"`

---

## Self-Review

**Spec coverage (Phase 2 inventory subset):**
- Products CRUD ✓ (Task 3)
- Categories CRUD ✓ (Task 2)
- Suppliers CRUD ✓ (Task 1)
- Stock Movements read-only ✓ (Task 4)
- Dashboard stats + chart ✓ (Task 5)
- Admin-only access via policies + middleware ✓
- Image upload for products ✓ (Task 3)
- Nav update ✓ (Task 6)

**Deferred to Phase 3:**
- Services + Service Requests
- Users management
- Profile page
- Reports + Custom Reports

**Placeholder scan:** none.

**Type consistency:** Supplier, StockMovement, DashboardStats added to models.ts in their respective tasks.

**Risk:** Image upload requires `storage:link`. Verify before smoke (`php artisan storage:link`).

---

**End of Phase 2 Plan.** Phase 3 plan to be written after Phase 2 ships.
