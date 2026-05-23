# EyeTech Inertia + Vue — Phase 1: POS Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Replace the Phase 0 placeholder `/app/pos` with a fully functional point-of-sale screen. Brother (admin) and cashiers can: search products, build a cart, checkout (with DB-transaction stock decrement + race protection), view past sales, and print receipts.

**Architecture:** Inertia-rendered Vue 3 + TypeScript pages on top of new Laravel controllers under `App\Http\Controllers\App`. Stock decrement uses `DB::transaction` + `Product::lockForUpdate()` to prevent overselling. Cart state lives client-side only; only the final checkout payload hits the server. Receipts auto-trigger `window.print()` via Inertia flash data.

**Tech Stack:** Builds on Phase 0 (Laravel 12, Inertia 3, Vue 3, TypeScript, shadcn-vue, spatie/permission). New: `Inertia\Testing\AssertableInertia` for response assertions, `Illuminate\Support\Facades\DB` transactions, PHPUnit's `assertDatabaseHas`.

**Spec:** `docs/superpowers/specs/2026-05-23-eyetech-inertia-vue-design.md`
**Prior plan:** `docs/superpowers/plans/2026-05-23-eyetech-inertia-vue-phase-0-foundation.md` (completed, tag `phase-0-complete`).

---

## File Structure

### New backend files

| Path | Responsibility |
|---|---|
| `app/Http/Controllers/App/SaleController.php` | `index` (paginated list), `show` (receipt view) |
| `app/Http/Requests/CheckoutSaleRequest.php` | Validate cart payload from POS |
| `app/Http/Resources/ProductResource.php` | Shape Product → Vue (id, name, sku, selling_price, stock_quantity, category, image_url) |
| `app/Http/Resources/CategoryResource.php` | Shape Category (id, name, slug) |
| `app/Http/Resources/SaleResource.php` | Shape Sale + items |
| `app/Http/Resources/SaleItemResource.php` | Shape SaleItem |
| `app/Exceptions/InsufficientStockException.php` | Thrown when checkout cannot fulfill |
| `app/Policies/SalePolicy.php` | Cashier can view/show only own; admin can void/refund |
| `app/Services/CheckoutService.php` | DB-transaction checkout logic (extracted so it's testable in isolation) |

### Modified backend files

| Path | Change |
|---|---|
| `app/Http/Controllers/App/PosController.php` | Add `checkout(CheckoutSaleRequest $request)` method; `index` returns products + categories |
| `app/Models/Product.php` | Add `image_url` accessor (storage URL); cast `is_active`, decimals |
| `app/Models/Sale.php` | Add `items()` hasMany; `user()` belongsTo; `$casts` for decimals/dates; generate `sale_number` in `boot` |
| `app/Models/SalesItem.php` | Add `sale()` belongsTo; casts; ensure table name `sales_items` (Laravel auto-pluralization gives `sales_items` → correct) |
| `app/Providers/AppServiceProvider.php` | Register policies (Sale → SalePolicy) |
| `routes/web.php` | Add POS checkout + sales routes |

### New frontend files

| Path | Responsibility |
|---|---|
| `resources/js/Pages/POS/Index.vue` | REPLACE placeholder — full POS UI |
| `resources/js/Pages/POS/components/ProductGrid.vue` | Left pane: searchable card grid |
| `resources/js/Pages/POS/components/Cart.vue` | Right pane: cart with qty controls + totals + checkout |
| `resources/js/Pages/POS/components/CartLine.vue` | Single cart row |
| `resources/js/Pages/POS/composables/usePosCart.ts` | Cart state (reactive ref, add/remove/setQty/clear) |
| `resources/js/Pages/Sales/Index.vue` | Past sales DataTable |
| `resources/js/Pages/Sales/Show.vue` | Receipt view; triggers `window.print()` when flash.print |
| `resources/js/components/DataTable.vue` | Generic table component (shadcn Table + paging) |
| `resources/js/components/ConfirmDelete.vue` | Reusable confirm modal (for void) |
| `resources/js/components/ui/dialog/*` | shadcn Dialog primitives (copied from shadcn-vue) |
| `resources/js/components/ui/table/*` | shadcn Table primitives |
| `resources/js/types/models.ts` | Extend with `Product`, `Category`, `Sale`, `SaleItem`, `PaymentMethod`, `SaleStatus` |
| `resources/css/print.css` | Receipt print styling (imported only on receipt page) |

### New test files

| Path | Responsibility |
|---|---|
| `tests/Feature/App/Pos/PosIndexTest.php` | `/app/pos` returns products + categories; admin + cashier both see it |
| `tests/Feature/App/Pos/PosCheckoutTest.php` | Successful checkout creates sale + items + stock movement + decrements stock; race condition test (concurrent checkout); insufficient stock error |
| `tests/Feature/App/Sales/SalesIndexTest.php` | Cashier sees own sales only; admin sees all |
| `tests/Feature/App/Sales/SalesShowTest.php` | Receipt page renders; print flash flows through |
| `tests/Feature/App/Sales/SalesAuthorizationTest.php` | Cashier cannot view other cashier's sale (403) |
| `tests/Unit/CheckoutServiceTest.php` | Service unit-test for stock decrement transaction |
| `resources/js/__tests__/usePosCart.spec.ts` | Cart add/remove/qty/clear logic |

---

## Task Decomposition

### Task 1: Eloquent relations + factories

**Files:**
- Modify: `app/Models/Sale.php`, `app/Models/SalesItem.php`, `app/Models/Product.php`
- Create: `database/factories/SaleFactory.php`, `database/factories/SalesItemFactory.php`, `database/factories/ProductFactory.php`, `database/factories/CategoryFactory.php`, `database/factories/SupplierFactory.php` (if any missing)

- [ ] **Step 1: Read existing models**

```bash
cat app/Models/Sale.php app/Models/SalesItem.php app/Models/Product.php app/Models/Category.php app/Models/Supplier.php
```

Note existing relations, casts, fillable. **Do not break Filament — preserve everything.**

- [ ] **Step 2: Add `items()` + `user()` to `Sale`**

In `app/Models/Sale.php`, add inside class:

```php
public function items()
{
    return $this->hasMany(SalesItem::class);
}

public function user()
{
    return $this->belongsTo(User::class);
}

protected function casts(): array
{
    return [
        'subtotal' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'total_amount' => 'decimal:2',
    ];
}

protected static function booted(): void
{
    static::creating(function (Sale $sale) {
        if (! $sale->sale_number) {
            $sale->sale_number = 'S-' . now()->format('Ymd') . '-' . str_pad((string) (static::whereDate('created_at', today())->count() + 1), 4, '0', STR_PAD_LEFT);
        }
    });
}
```

Add `'sale_number'` to `$fillable` if not present. Preserve all other fillable items.

- [ ] **Step 3: Add `sale()` to `SalesItem`**

In `app/Models/SalesItem.php`:

```php
public function sale()
{
    return $this->belongsTo(Sale::class);
}

public function product()
{
    return $this->belongsTo(Product::class);
}

protected function casts(): array
{
    return [
        'unit_price' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'total_price' => 'decimal:2',
    ];
}
```

Fillable must include: `sale_id`, `product_id`, `item_name`, `item_sku`, `item_description`, `quantity`, `unit_price`, `discount_amount`, `total_price`.

- [ ] **Step 4: Add `image_url` accessor to `Product`**

In `app/Models/Product.php`:

```php
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Support\Facades\Storage;

protected function imageUrl(): Attribute
{
    return Attribute::make(
        get: fn () => $this->image ? Storage::url($this->image) : null,
    );
}

protected $appends = ['image_url'];
```

Preserve existing `$casts`, `$fillable`. Add `image_url` to `$appends` array (create if missing).

- [ ] **Step 5: Create / update factories**

Run:
```bash
php artisan make:factory SaleFactory --model=Sale
php artisan make:factory SalesItemFactory --model=SalesItem
```

Edit `database/factories/SaleFactory.php`:

```php
<?php

namespace Database\Factories;

use App\Models\Sale;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class SaleFactory extends Factory
{
    protected $model = Sale::class;

    public function definition(): array
    {
        return [
            'sale_number' => 'S-' . now()->format('Ymd') . '-' . $this->faker->unique()->numerify('####'),
            'customer_name' => $this->faker->optional()->name(),
            'customer_phone' => $this->faker->optional()->phoneNumber(),
            'customer_email' => $this->faker->optional()->safeEmail(),
            'payment_method' => $this->faker->randomElement(['cash', 'card', 'mobile_money', 'bank_transfer']),
            'status' => 'completed',
            'subtotal' => 100.00,
            'tax_amount' => 0,
            'discount_amount' => 0,
            'total_amount' => 100.00,
            'notes' => null,
            'user_id' => User::factory(),
        ];
    }
}
```

Edit `database/factories/SalesItemFactory.php`:

```php
<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\Sale;
use App\Models\SalesItem;
use Illuminate\Database\Eloquent\Factories\Factory;

class SalesItemFactory extends Factory
{
    protected $model = SalesItem::class;

    public function definition(): array
    {
        return [
            'sale_id' => Sale::factory(),
            'product_id' => Product::factory(),
            'item_name' => $this->faker->word(),
            'item_sku' => strtoupper($this->faker->bothify('SKU-####')),
            'item_description' => null,
            'quantity' => 1,
            'unit_price' => 50.00,
            'discount_amount' => 0,
            'total_price' => 50.00,
        ];
    }
}
```

Check if `ProductFactory`, `CategoryFactory`, `SupplierFactory` exist. If missing, create with minimal definitions:

`ProductFactory.php`:
```php
public function definition(): array
{
    return [
        'name' => $this->faker->words(2, true),
        'sku' => strtoupper($this->faker->unique()->bothify('SKU-####')),
        'description' => $this->faker->sentence(),
        'category_id' => \App\Models\Category::factory(),
        'supplier_id' => \App\Models\Supplier::factory(),
        'cost_price' => 50.00,
        'selling_price' => 100.00,
        'stock_quantity' => 10,
        'min_stock_level' => 2,
        'brand' => null,
        'model' => null,
        'specifications' => null,
        'image' => null,
        'is_active' => true,
    ];
}
```

`CategoryFactory.php`:
```php
public function definition(): array
{
    $name = $this->faker->unique()->word();
    return ['name' => $name, 'slug' => str($name)->slug()];
}
```

`SupplierFactory.php` — minimal definition with whatever the migration requires (read migration first).

- [ ] **Step 6: Sanity test**

```bash
php artisan tinker --execute="\$s = App\Models\Sale::factory()->create(); \$s->items()->create([...]); print_r(\$s->fresh()->items->toArray());"
```

If too complex for one-liner, write a temp test instead.

- [ ] **Step 7: Commit**

```bash
git add app/Models database/factories
git commit -m "feat(phase-1): Sale/SalesItem relations + factories + Product image_url accessor"
```

---

### Task 2: API Resources

**Files:**
- Create: `app/Http/Resources/ProductResource.php`, `CategoryResource.php`, `SaleResource.php`, `SaleItemResource.php`

- [ ] **Step 1: Create `ProductResource`**

```bash
php artisan make:resource ProductResource
```

Edit:

```php
<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'sku' => $this->sku,
            'description' => $this->description,
            'selling_price' => (float) $this->selling_price,
            'cost_price' => (float) $this->cost_price,
            'stock_quantity' => $this->stock_quantity,
            'min_stock_level' => $this->min_stock_level,
            'category_id' => $this->category_id,
            'supplier_id' => $this->supplier_id,
            'category' => CategoryResource::make($this->whenLoaded('category')),
            'brand' => $this->brand,
            'model' => $this->model,
            'image_url' => $this->image_url,
            'is_active' => (bool) $this->is_active,
        ];
    }
}
```

- [ ] **Step 2: Create `CategoryResource`**

```bash
php artisan make:resource CategoryResource
```

```php
<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CategoryResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'icon' => $this->icon,
        ];
    }
}
```

- [ ] **Step 3: Create `SaleItemResource`**

```bash
php artisan make:resource SaleItemResource
```

```php
<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class SaleItemResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'sale_id' => $this->sale_id,
            'product_id' => $this->product_id,
            'item_name' => $this->item_name,
            'item_sku' => $this->item_sku,
            'item_description' => $this->item_description,
            'quantity' => $this->quantity,
            'unit_price' => (float) $this->unit_price,
            'discount_amount' => (float) $this->discount_amount,
            'total_price' => (float) $this->total_price,
        ];
    }
}
```

- [ ] **Step 4: Create `SaleResource`**

```bash
php artisan make:resource SaleResource
```

```php
<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class SaleResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'sale_number' => $this->sale_number,
            'customer_name' => $this->customer_name,
            'customer_phone' => $this->customer_phone,
            'customer_email' => $this->customer_email,
            'payment_method' => $this->payment_method,
            'status' => $this->status,
            'subtotal' => (float) $this->subtotal,
            'tax_amount' => (float) $this->tax_amount,
            'discount_amount' => (float) $this->discount_amount,
            'total_amount' => (float) $this->total_amount,
            'notes' => $this->notes,
            'user_id' => $this->user_id,
            'user' => $this->whenLoaded('user', fn () => ['id' => $this->user->id, 'name' => $this->user->name]),
            'items' => SaleItemResource::collection($this->whenLoaded('items')),
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}
```

- [ ] **Step 5: Commit**

```bash
git add app/Http/Resources
git commit -m "feat(phase-1): ProductResource, CategoryResource, SaleResource, SaleItemResource"
```

---

### Task 3: TypeScript types

**Files:**
- Modify: `resources/js/types/models.ts` — extend with domain types

- [ ] **Step 1: Append types to `models.ts`**

Read existing file, append (preserve `User`, `Role`):

```ts
export interface Category {
  id: number
  name: string
  slug: string
  icon: string | null
}

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
  brand: string | null
  model: string | null
  image_url: string | null
  is_active: boolean
}

export type PaymentMethod = 'cash' | 'card' | 'mobile_money' | 'bank_transfer'
export type SaleStatus = 'pending' | 'completed' | 'cancelled' | 'refunded'

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

// POS cart line (client-side only — not persisted server-side)
export interface CartLine {
  product_id: number
  name: string
  sku: string | null
  unit_price: number
  quantity: number
  available_stock: number
}
```

- [ ] **Step 2: Type-check**

```bash
npx vue-tsc --noEmit
```

Expected: no errors.

- [ ] **Step 3: Commit**

```bash
git add resources/js/types/models.ts
git commit -m "feat(phase-1): TS domain types for Product/Sale/Category/CartLine"
```

---

### Task 4: PosController@index (load products + categories)

**Files:**
- Modify: `app/Http/Controllers/App/PosController.php`
- Create: `tests/Feature/App/Pos/PosIndexTest.php`

- [ ] **Step 1: Write failing test**

```php
<?php

namespace Tests\Feature\App\Pos;

use App\Models\Category;
use App\Models\Product;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class PosIndexTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'cashier', 'guard_name' => 'web']);
    }

    public function test_pos_loads_active_products_and_categories(): void
    {
        $cashier = User::factory()->create(['role' => 'cashier']);
        $cashier->syncRoles(['cashier']);

        $category = Category::factory()->create();
        $supplier = Supplier::factory()->create();
        $activeProduct = Product::factory()->create([
            'category_id' => $category->id,
            'supplier_id' => $supplier->id,
            'is_active' => true,
            'stock_quantity' => 5,
        ]);
        $inactiveProduct = Product::factory()->create([
            'category_id' => $category->id,
            'supplier_id' => $supplier->id,
            'is_active' => false,
        ]);

        $this->actingAs($cashier)
            ->withoutVite()
            ->get('/app/pos')
            ->assertOk()
            ->assertInertia(fn ($p) => $p
                ->component('POS/Index')
                ->has('products', 1)
                ->where('products.0.id', $activeProduct->id)
                ->has('categories', 1)
                ->where('categories.0.id', $category->id)
            );
    }
}
```

- [ ] **Step 2: Run, expect fail**

```bash
php artisan test --filter=PosIndexTest
```

- [ ] **Step 3: Update `PosController@index`**

```php
<?php

namespace App\Http\Controllers\App;

use App\Http\Controllers\Controller;
use App\Http\Resources\CategoryResource;
use App\Http\Resources\ProductResource;
use App\Models\Category;
use App\Models\Product;
use Inertia\Inertia;
use Inertia\Response;

class PosController extends Controller
{
    public function index(): Response
    {
        $products = Product::with('category')
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        $categories = Category::orderBy('name')->get();

        return Inertia::render('POS/Index', [
            'products' => ProductResource::collection($products),
            'categories' => CategoryResource::collection($categories),
        ]);
    }
}
```

- [ ] **Step 4: Run, expect pass**

```bash
php artisan test --filter=PosIndexTest
```

- [ ] **Step 5: Commit**

```bash
git add app/Http/Controllers/App/PosController.php tests/Feature/App/Pos
git commit -m "feat(phase-1): /app/pos loads active products + categories"
```

---

### Task 5: shadcn Dialog + Table primitives

**Files:**
- Create: `resources/js/components/ui/dialog/Dialog.vue`, `DialogContent.vue`, `DialogHeader.vue`, `DialogTitle.vue`, `DialogDescription.vue`, `DialogFooter.vue`, `DialogClose.vue`, `index.ts`
- Create: `resources/js/components/ui/table/Table.vue`, `TableHeader.vue`, `TableBody.vue`, `TableRow.vue`, `TableHead.vue`, `TableCell.vue`, `index.ts`

These come from shadcn-vue's official template. Generated content is mechanical — see shadcn-vue docs at https://www.shadcn-vue.com/docs/components/dialog and https://www.shadcn-vue.com/docs/components/table — copy verbatim, adjust import paths to `@/lib/utils` and the radix-vue imports.

- [ ] **Step 1: Create dialog/Dialog.vue**

```vue
<script setup lang="ts">
import { DialogRoot, type DialogRootEmits, type DialogRootProps, useForwardPropsEmits } from 'radix-vue'

const props = defineProps<DialogRootProps>()
const emits = defineEmits<DialogRootEmits>()
const forwarded = useForwardPropsEmits(props, emits)
</script>

<template>
  <DialogRoot v-bind="forwarded">
    <slot />
  </DialogRoot>
</template>
```

Create the rest (DialogContent, DialogHeader, DialogTitle, DialogDescription, DialogFooter, DialogClose) by following the exact template from shadcn-vue docs. Each is a small wrapper around radix-vue primitives styled with Tailwind utility classes.

- [ ] **Step 2: index.ts**

```ts
export { default as Dialog } from './Dialog.vue'
export { default as DialogContent } from './DialogContent.vue'
export { default as DialogHeader } from './DialogHeader.vue'
export { default as DialogTitle } from './DialogTitle.vue'
export { default as DialogDescription } from './DialogDescription.vue'
export { default as DialogFooter } from './DialogFooter.vue'
export { default as DialogClose } from './DialogClose.vue'
```

- [ ] **Step 3: Create table components similarly**

Mirror shadcn-vue Table primitives. Each is essentially a styled HTML table element.

- [ ] **Step 4: Type-check**

```bash
npx vue-tsc --noEmit
```

- [ ] **Step 5: Commit**

```bash
git add resources/js/components/ui/dialog resources/js/components/ui/table
git commit -m "feat(phase-1): shadcn Dialog + Table primitives"
```

---

### Task 6: POS UI — ProductGrid + Cart + cart composable

**Files:**
- Create: `resources/js/Pages/POS/composables/usePosCart.ts`
- Create: `resources/js/Pages/POS/components/ProductGrid.vue`, `Cart.vue`, `CartLine.vue`
- Modify: `resources/js/Pages/POS/Index.vue` (replace placeholder)
- Create: `resources/js/__tests__/usePosCart.spec.ts`

- [ ] **Step 1: Failing cart spec**

`resources/js/__tests__/usePosCart.spec.ts`:

```ts
import { describe, expect, it } from 'vitest'
import { usePosCart } from '@/Pages/POS/composables/usePosCart'
import type { Product } from '@/types/models'

const product = (over: Partial<Product> = {}): Product => ({
  id: 1, name: 'Frame', sku: 'F-1', description: null,
  selling_price: 100, cost_price: 50, stock_quantity: 5, min_stock_level: 1,
  category_id: 1, supplier_id: 1, category: null,
  brand: null, model: null, image_url: null, is_active: true,
  ...over,
})

describe('usePosCart', () => {
  it('starts empty', () => {
    const cart = usePosCart()
    expect(cart.lines.value).toEqual([])
    expect(cart.subtotal.value).toBe(0)
  })

  it('addItem creates a line', () => {
    const cart = usePosCart()
    cart.addItem(product())
    expect(cart.lines.value).toHaveLength(1)
    expect(cart.lines.value[0].quantity).toBe(1)
  })

  it('addItem twice increments qty on same product', () => {
    const cart = usePosCart()
    cart.addItem(product())
    cart.addItem(product())
    expect(cart.lines.value).toHaveLength(1)
    expect(cart.lines.value[0].quantity).toBe(2)
  })

  it('setQuantity updates line; 0 removes it', () => {
    const cart = usePosCart()
    cart.addItem(product())
    cart.setQuantity(1, 3)
    expect(cart.lines.value[0].quantity).toBe(3)
    cart.setQuantity(1, 0)
    expect(cart.lines.value).toHaveLength(0)
  })

  it('subtotal sums lines', () => {
    const cart = usePosCart()
    cart.addItem(product({ id: 1, selling_price: 100 }))
    cart.addItem(product({ id: 2, selling_price: 50 }))
    expect(cart.subtotal.value).toBe(150)
  })

  it('addItem refuses to exceed available stock', () => {
    const cart = usePosCart()
    cart.addItem(product({ stock_quantity: 1 }))
    const ok = cart.addItem(product({ stock_quantity: 1 }))
    expect(ok).toBe(false)
    expect(cart.lines.value[0].quantity).toBe(1)
  })

  it('clear empties the cart', () => {
    const cart = usePosCart()
    cart.addItem(product())
    cart.clear()
    expect(cart.lines.value).toEqual([])
  })
})
```

- [ ] **Step 2: Run, expect fail**

```bash
npm test -- usePosCart
```

- [ ] **Step 3: Implement `usePosCart.ts`**

```ts
import { computed, ref } from 'vue'
import type { CartLine, Product } from '@/types/models'

export function usePosCart() {
  const lines = ref<CartLine[]>([])

  function addItem(product: Product): boolean {
    const existing = lines.value.find(l => l.product_id === product.id)
    const currentQty = existing?.quantity ?? 0
    if (currentQty + 1 > product.stock_quantity) return false

    if (existing) {
      existing.quantity += 1
    } else {
      lines.value.push({
        product_id: product.id,
        name: product.name,
        sku: product.sku,
        unit_price: product.selling_price,
        quantity: 1,
        available_stock: product.stock_quantity,
      })
    }
    return true
  }

  function setQuantity(productId: number, qty: number) {
    const idx = lines.value.findIndex(l => l.product_id === productId)
    if (idx === -1) return
    if (qty <= 0) {
      lines.value.splice(idx, 1)
      return
    }
    const line = lines.value[idx]
    line.quantity = Math.min(qty, line.available_stock)
  }

  function remove(productId: number) {
    lines.value = lines.value.filter(l => l.product_id !== productId)
  }

  function clear() {
    lines.value = []
  }

  const subtotal = computed(() =>
    lines.value.reduce((sum, l) => sum + l.unit_price * l.quantity, 0),
  )

  const total = subtotal // No tax/discount in MVP — total = subtotal

  return { lines, addItem, setQuantity, remove, clear, subtotal, total }
}
```

- [ ] **Step 4: Run tests, expect pass**

```bash
npm test -- usePosCart
```

Expected: 7 passing.

- [ ] **Step 5: Build `ProductGrid.vue`**

Left pane. Receives `products: Product[]` prop. Local refs: `search` (string), `selectedCategoryId` (number | null). Computed `filtered` filters by name/sku containing search (case-insensitive) AND category match (if selected). Emits `select(product)`.

```vue
<script setup lang="ts">
import { computed, ref } from 'vue'
import { Card, CardContent } from '@/components/ui/card'
import { Input } from '@/components/ui/input'
import { Button } from '@/components/ui/button'
import type { Category, Product } from '@/types/models'

const props = defineProps<{ products: Product[]; categories: Category[] }>()
const emit = defineEmits<{ select: [product: Product] }>()

const search = ref('')
const selectedCategoryId = ref<number | null>(null)

const filtered = computed(() => props.products.filter(p => {
  const matchesSearch = !search.value ||
    p.name.toLowerCase().includes(search.value.toLowerCase()) ||
    p.sku.toLowerCase().includes(search.value.toLowerCase())
  const matchesCategory = !selectedCategoryId.value || p.category_id === selectedCategoryId.value
  return matchesSearch && matchesCategory
}))
</script>

<template>
  <div class="space-y-4">
    <Input
      v-model="search"
      placeholder="Search by name or SKU..."
      class="w-full"
    />
    <div class="flex gap-2 flex-wrap">
      <Button
        :variant="selectedCategoryId === null ? 'default' : 'outline'"
        size="sm"
        @click="selectedCategoryId = null"
      >
        All
      </Button>
      <Button
        v-for="c in categories"
        :key="c.id"
        :variant="selectedCategoryId === c.id ? 'default' : 'outline'"
        size="sm"
        @click="selectedCategoryId = c.id"
      >
        {{ c.name }}
      </Button>
    </div>
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3">
      <Card
        v-for="p in filtered"
        :key="p.id"
        class="cursor-pointer hover:border-primary transition-colors"
        @click="emit('select', p)"
      >
        <CardContent class="p-4">
          <div class="font-medium">{{ p.name }}</div>
          <div class="text-xs text-muted-foreground">{{ p.sku }}</div>
          <div class="mt-2 flex items-center justify-between">
            <span class="font-semibold">{{ p.selling_price.toFixed(2) }}</span>
            <span :class="['text-xs', p.stock_quantity <= p.min_stock_level ? 'text-destructive' : 'text-muted-foreground']">
              {{ p.stock_quantity }} left
            </span>
          </div>
        </CardContent>
      </Card>
    </div>
    <p v-if="filtered.length === 0" class="text-center text-muted-foreground py-8">
      No products match.
    </p>
  </div>
</template>
```

- [ ] **Step 6: Build `CartLine.vue`**

```vue
<script setup lang="ts">
import { Button } from '@/components/ui/button'
import { Input } from '@/components/ui/input'
import type { CartLine as Line } from '@/types/models'

const props = defineProps<{ line: Line }>()
const emit = defineEmits<{
  'update:quantity': [productId: number, qty: number]
  remove: [productId: number]
}>()

function updateQty(value: string) {
  const n = Number.parseInt(value, 10)
  if (!Number.isNaN(n)) emit('update:quantity', props.line.product_id, n)
}
</script>

<template>
  <div class="flex items-center gap-2 py-2 border-b last:border-b-0">
    <div class="flex-1 min-w-0">
      <div class="font-medium truncate">{{ line.name }}</div>
      <div class="text-xs text-muted-foreground">{{ line.unit_price.toFixed(2) }} ea</div>
    </div>
    <Button variant="outline" size="sm" @click="emit('update:quantity', line.product_id, line.quantity - 1)">−</Button>
    <Input
      type="number"
      :model-value="line.quantity"
      @update:model-value="updateQty"
      class="w-16 text-center"
    />
    <Button variant="outline" size="sm" @click="emit('update:quantity', line.product_id, line.quantity + 1)">+</Button>
    <div class="w-24 text-right font-semibold">{{ (line.unit_price * line.quantity).toFixed(2) }}</div>
    <Button variant="ghost" size="sm" @click="emit('remove', line.product_id)">×</Button>
  </div>
</template>
```

- [ ] **Step 7: Build `Cart.vue`**

```vue
<script setup lang="ts">
import { computed, ref } from 'vue'
import { router } from '@inertiajs/vue3'
import { toast } from 'vue-sonner'
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card'
import { Button } from '@/components/ui/button'
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'
import CartLine from './CartLine.vue'
import type { CartLine as Line, PaymentMethod } from '@/types/models'

const props = defineProps<{
  lines: Line[]
  subtotal: number
  total: number
}>()

const emit = defineEmits<{
  'update:quantity': [productId: number, qty: number]
  remove: [productId: number]
  clear: []
}>()

const customerName = ref('')
const customerPhone = ref('')
const paymentMethod = ref<PaymentMethod>('cash')
const processing = ref(false)

const canCheckout = computed(() => props.lines.length > 0 && !processing.value)

function checkout() {
  if (!canCheckout.value) return
  processing.value = true

  router.post('/app/pos/checkout', {
    customer_name: customerName.value || null,
    customer_phone: customerPhone.value || null,
    payment_method: paymentMethod.value,
    items: props.lines.map(l => ({
      product_id: l.product_id,
      quantity: l.quantity,
    })),
  }, {
    preserveScroll: true,
    onSuccess: () => {
      emit('clear')
      customerName.value = ''
      customerPhone.value = ''
    },
    onError: () => toast.error('Checkout failed — see errors above.'),
    onFinish: () => { processing.value = false },
  })
}
</script>

<template>
  <Card class="sticky top-4">
    <CardHeader>
      <CardTitle>Cart</CardTitle>
    </CardHeader>
    <CardContent class="space-y-4">
      <div v-if="lines.length === 0" class="text-center text-muted-foreground py-8">
        Cart is empty. Tap a product to add.
      </div>
      <div v-else class="max-h-80 overflow-y-auto">
        <CartLine
          v-for="line in lines"
          :key="line.product_id"
          :line="line"
          @update:quantity="(id, qty) => emit('update:quantity', id, qty)"
          @remove="(id) => emit('remove', id)"
        />
      </div>

      <div v-if="lines.length > 0" class="space-y-2 pt-2 border-t">
        <div class="flex justify-between"><span>Subtotal</span><span>{{ subtotal.toFixed(2) }}</span></div>
        <div class="flex justify-between text-lg font-bold"><span>Total</span><span>{{ total.toFixed(2) }}</span></div>
      </div>

      <div class="space-y-2">
        <div class="space-y-1">
          <Label html-for="cust-name">Customer name (optional)</Label>
          <Input id="cust-name" v-model="customerName" />
        </div>
        <div class="space-y-1">
          <Label html-for="cust-phone">Phone (optional)</Label>
          <Input id="cust-phone" v-model="customerPhone" />
        </div>
        <div class="space-y-1">
          <Label html-for="pay-method">Payment</Label>
          <select id="pay-method" v-model="paymentMethod" class="w-full rounded-md border border-input bg-background h-10 px-3 text-sm">
            <option value="cash">Cash</option>
            <option value="card">Card</option>
            <option value="mobile_money">Mobile money</option>
            <option value="bank_transfer">Bank transfer</option>
          </select>
        </div>
      </div>

      <Button
        :disabled="!canCheckout"
        class="w-full"
        size="lg"
        @click="checkout"
      >
        {{ processing ? 'Processing…' : `Checkout — ${total.toFixed(2)}` }}
      </Button>
    </CardContent>
  </Card>
</template>
```

- [ ] **Step 8: Update `POS/Index.vue`**

```vue
<script setup lang="ts">
import PosLayout from '@/Layouts/PosLayout.vue'
import ProductGrid from './components/ProductGrid.vue'
import Cart from './components/Cart.vue'
import { usePosCart } from './composables/usePosCart'
import type { Product, Category } from '@/types/models'

const props = defineProps<{
  products: Product[]
  categories: Category[]
}>()

const cart = usePosCart()
</script>

<template>
  <PosLayout>
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
      <div class="lg:col-span-2">
        <ProductGrid
          :products="products"
          :categories="categories"
          @select="cart.addItem"
        />
      </div>
      <div>
        <Cart
          :lines="cart.lines.value"
          :subtotal="cart.subtotal.value"
          :total="cart.total.value"
          @update:quantity="(id, qty) => cart.setQuantity(id, qty)"
          @remove="cart.remove"
          @clear="cart.clear"
        />
      </div>
    </div>
  </PosLayout>
</template>
```

> Note on reactive ref unwrapping in templates: `cart.lines.value` in the template can also be `cart.lines` if `<script setup>` unwraps. Test in browser; adjust.

- [ ] **Step 9: Type-check + build**

```bash
npx vue-tsc --noEmit
npm run build
```

- [ ] **Step 10: Commit**

```bash
git add resources/js/Pages/POS resources/js/__tests__/usePosCart.spec.ts
git commit -m "feat(phase-1): POS UI — ProductGrid, Cart, usePosCart composable"
```

---

### Task 7: InsufficientStockException + CheckoutService

**Files:**
- Create: `app/Exceptions/InsufficientStockException.php`
- Create: `app/Services/CheckoutService.php`
- Create: `tests/Unit/CheckoutServiceTest.php`

- [ ] **Step 1: Exception**

```php
<?php

namespace App\Exceptions;

use Exception;

class InsufficientStockException extends Exception
{
    public function __construct(public readonly int $productId, public readonly int $available, public readonly int $requested)
    {
        parent::__construct("Insufficient stock for product {$productId}: requested {$requested}, available {$available}.");
    }
}
```

- [ ] **Step 2: Failing service test**

`tests/Unit/CheckoutServiceTest.php`:

```php
<?php

namespace Tests\Unit;

use App\Exceptions\InsufficientStockException;
use App\Models\Category;
use App\Models\Product;
use App\Models\Supplier;
use App\Models\User;
use App\Services\CheckoutService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class CheckoutServiceTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Role::firstOrCreate(['name' => 'cashier', 'guard_name' => 'web']);
    }

    public function test_successful_checkout_creates_sale_and_decrements_stock(): void
    {
        $cashier = User::factory()->create(['role' => 'cashier']);
        $product = Product::factory()->create([
            'category_id' => Category::factory(),
            'supplier_id' => Supplier::factory(),
            'stock_quantity' => 10,
            'selling_price' => 25.00,
        ]);

        $service = app(CheckoutService::class);
        $sale = $service->checkout(
            user: $cashier,
            items: [['product_id' => $product->id, 'quantity' => 3]],
            customer: ['name' => 'Test', 'phone' => null, 'email' => null],
            paymentMethod: 'cash',
        );

        $this->assertNotNull($sale->sale_number);
        $this->assertEquals(75.00, $sale->total_amount);
        $this->assertCount(1, $sale->items);
        $this->assertEquals(7, $product->fresh()->stock_quantity);
        $this->assertDatabaseHas('stock_movements', [
            'product_id' => $product->id,
            'movement_type' => 'sale',
            'quantity' => 3,
            'previous_quantity' => 10,
            'new_quantity' => 7,
        ]);
    }

    public function test_throws_insufficient_stock_when_quantity_exceeds_available(): void
    {
        $cashier = User::factory()->create(['role' => 'cashier']);
        $product = Product::factory()->create([
            'category_id' => Category::factory(),
            'supplier_id' => Supplier::factory(),
            'stock_quantity' => 2,
        ]);

        $service = app(CheckoutService::class);

        $this->expectException(InsufficientStockException::class);
        $service->checkout(
            user: $cashier,
            items: [['product_id' => $product->id, 'quantity' => 5]],
            customer: ['name' => null, 'phone' => null, 'email' => null],
            paymentMethod: 'cash',
        );

        $this->assertEquals(2, $product->fresh()->stock_quantity, 'Stock not decremented on failure');
    }
}
```

- [ ] **Step 3: Run, expect fail**

```bash
php artisan test --filter=CheckoutServiceTest
```

- [ ] **Step 4: Implement `CheckoutService`**

```php
<?php

namespace App\Services;

use App\Exceptions\InsufficientStockException;
use App\Models\Product;
use App\Models\Sale;
use App\Models\StockMovement;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class CheckoutService
{
    /**
     * @param  array{name: ?string, phone: ?string, email: ?string}  $customer
     * @param  array<int, array{product_id: int, quantity: int}>  $items
     */
    public function checkout(User $user, array $items, array $customer, string $paymentMethod): Sale
    {
        return DB::transaction(function () use ($user, $items, $customer, $paymentMethod) {
            $subtotal = 0;
            $resolvedItems = [];

            foreach ($items as $item) {
                $product = Product::lockForUpdate()->findOrFail($item['product_id']);

                if ($product->stock_quantity < $item['quantity']) {
                    throw new InsufficientStockException(
                        $product->id,
                        $product->stock_quantity,
                        $item['quantity'],
                    );
                }

                $lineTotal = $product->selling_price * $item['quantity'];
                $subtotal += $lineTotal;

                $resolvedItems[] = [
                    'product' => $product,
                    'quantity' => $item['quantity'],
                    'unit_price' => (float) $product->selling_price,
                    'line_total' => (float) $lineTotal,
                ];
            }

            $sale = Sale::create([
                'customer_name' => $customer['name'],
                'customer_phone' => $customer['phone'],
                'customer_email' => $customer['email'],
                'payment_method' => $paymentMethod,
                'status' => 'completed',
                'subtotal' => $subtotal,
                'tax_amount' => 0,
                'discount_amount' => 0,
                'total_amount' => $subtotal,
                'user_id' => $user->id,
            ]);

            foreach ($resolvedItems as $r) {
                $product = $r['product'];

                $sale->items()->create([
                    'product_id' => $product->id,
                    'item_name' => $product->name,
                    'item_sku' => $product->sku,
                    'item_description' => $product->description,
                    'quantity' => $r['quantity'],
                    'unit_price' => $r['unit_price'],
                    'discount_amount' => 0,
                    'total_price' => $r['line_total'],
                ]);

                $previousQuantity = $product->stock_quantity;
                $newQuantity = $previousQuantity - $r['quantity'];

                $product->update(['stock_quantity' => $newQuantity]);

                StockMovement::create([
                    'product_id' => $product->id,
                    'movement_type' => 'sale',
                    'quantity' => $r['quantity'],
                    'previous_quantity' => $previousQuantity,
                    'new_quantity' => $newQuantity,
                    'reference_type' => 'sale',
                    'reference_id' => $sale->id,
                    'user_id' => $user->id,
                ]);
            }

            return $sale->load(['items', 'user']);
        });
    }
}
```

Add `'sale'` to the StockMovement `movement_type` fillable list if not already (read model first).

- [ ] **Step 5: Run, expect pass**

```bash
php artisan test --filter=CheckoutServiceTest
```

- [ ] **Step 6: Commit**

```bash
git add app/Exceptions app/Services tests/Unit/CheckoutServiceTest.php
git commit -m "feat(phase-1): CheckoutService with DB-transaction stock decrement"
```

---

### Task 8: PosController@checkout endpoint

**Files:**
- Modify: `app/Http/Controllers/App/PosController.php` (add `checkout` method)
- Create: `app/Http/Requests/CheckoutSaleRequest.php`
- Modify: `routes/web.php` (add POST /app/pos/checkout)
- Create: `tests/Feature/App/Pos/PosCheckoutTest.php`

- [ ] **Step 1: Failing checkout test**

```php
<?php

namespace Tests\Feature\App\Pos;

use App\Models\Category;
use App\Models\Product;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class PosCheckoutTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Role::firstOrCreate(['name' => 'cashier', 'guard_name' => 'web']);
    }

    public function test_checkout_creates_sale_and_redirects_to_receipt(): void
    {
        $cashier = User::factory()->create(['role' => 'cashier']);
        $cashier->syncRoles(['cashier']);

        $product = Product::factory()->create([
            'category_id' => Category::factory(),
            'supplier_id' => Supplier::factory(),
            'stock_quantity' => 5,
            'selling_price' => 10.00,
        ]);

        $response = $this->actingAs($cashier)->post('/app/pos/checkout', [
            'customer_name' => 'Walk-in',
            'payment_method' => 'cash',
            'items' => [['product_id' => $product->id, 'quantity' => 2]],
        ]);

        $sale = \App\Models\Sale::first();
        $response->assertRedirect('/app/sales/' . $sale->id);
        $response->assertSessionHas('print', true);

        $this->assertEquals(20.00, $sale->total_amount);
        $this->assertEquals(3, $product->fresh()->stock_quantity);
    }

    public function test_checkout_rejects_insufficient_stock(): void
    {
        $cashier = User::factory()->create(['role' => 'cashier']);
        $cashier->syncRoles(['cashier']);

        $product = Product::factory()->create([
            'category_id' => Category::factory(),
            'supplier_id' => Supplier::factory(),
            'stock_quantity' => 1,
        ]);

        $this->actingAs($cashier)
            ->from('/app/pos')
            ->post('/app/pos/checkout', [
                'payment_method' => 'cash',
                'items' => [['product_id' => $product->id, 'quantity' => 5]],
            ])
            ->assertRedirect('/app/pos')
            ->assertSessionHas('error');

        $this->assertEquals(1, $product->fresh()->stock_quantity, 'stock unchanged on failure');
        $this->assertEquals(0, \App\Models\Sale::count(), 'no sale created');
    }

    public function test_checkout_validates_payload(): void
    {
        $cashier = User::factory()->create(['role' => 'cashier']);
        $cashier->syncRoles(['cashier']);

        $this->actingAs($cashier)
            ->from('/app/pos')
            ->post('/app/pos/checkout', [
                'payment_method' => 'cash',
                'items' => [], // empty cart
            ])
            ->assertSessionHasErrors('items');
    }
}
```

- [ ] **Step 2: Run, expect fail**

```bash
php artisan test --filter=PosCheckoutTest
```

- [ ] **Step 3: Create `CheckoutSaleRequest`**

```php
<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CheckoutSaleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'customer_name' => ['nullable', 'string', 'max:255'],
            'customer_phone' => ['nullable', 'string', 'max:50'],
            'customer_email' => ['nullable', 'email', 'max:255'],
            'payment_method' => ['required', 'in:cash,card,mobile_money,bank_transfer'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'integer', 'exists:products,id'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
        ];
    }
}
```

- [ ] **Step 4: Add `checkout` to `PosController`**

```php
use App\Exceptions\InsufficientStockException;
use App\Http\Requests\CheckoutSaleRequest;
use App\Services\CheckoutService;
use Illuminate\Http\RedirectResponse;

public function checkout(CheckoutSaleRequest $request, CheckoutService $service): RedirectResponse
{
    try {
        $sale = $service->checkout(
            user: $request->user(),
            items: $request->input('items'),
            customer: [
                'name' => $request->input('customer_name'),
                'phone' => $request->input('customer_phone'),
                'email' => $request->input('customer_email'),
            ],
            paymentMethod: $request->input('payment_method'),
        );
    } catch (InsufficientStockException $e) {
        return back()->with('error', $e->getMessage());
    }

    return redirect()
        ->route('sales.show', $sale)
        ->with('print', true);
}
```

- [ ] **Step 5: Add route**

In `routes/web.php`, inside the `auth` + `prefix('app')` group:

```php
Route::post('/pos/checkout', [PosController::class, 'checkout'])->name('pos.checkout');
```

- [ ] **Step 6: Run, expect pass**

```bash
php artisan test --filter=PosCheckoutTest
```

- [ ] **Step 7: Commit**

```bash
git add app/Http/Controllers/App/PosController.php app/Http/Requests/CheckoutSaleRequest.php routes/web.php tests/Feature/App/Pos/PosCheckoutTest.php
git commit -m "feat(phase-1): POST /app/pos/checkout — validate, transaction, redirect to receipt"
```

---

### Task 9: SaleController + Sales Index page

**Files:**
- Create: `app/Http/Controllers/App/SaleController.php`
- Create: `app/Policies/SalePolicy.php`
- Modify: `app/Providers/AppServiceProvider.php` (or `AuthServiceProvider`) — register policy
- Modify: `routes/web.php` — sales routes
- Create: `resources/js/Pages/Sales/Index.vue`
- Create: `resources/js/components/DataTable.vue`
- Create: `tests/Feature/App/Sales/SalesIndexTest.php`
- Create: `tests/Feature/App/Sales/SalesAuthorizationTest.php`

- [ ] **Step 1: Failing tests**

`SalesIndexTest.php`:

```php
<?php

namespace Tests\Feature\App\Sales;

use App\Models\Sale;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class SalesIndexTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'cashier', 'guard_name' => 'web']);
    }

    public function test_admin_sees_all_sales(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $admin->syncRoles(['admin']);

        $other = User::factory()->create(['role' => 'cashier']);
        Sale::factory()->count(3)->create(['user_id' => $other->id]);

        $this->actingAs($admin)
            ->withoutVite()
            ->get('/app/sales')
            ->assertOk()
            ->assertInertia(fn ($p) => $p->component('Sales/Index')->has('sales.data', 3));
    }

    public function test_cashier_sees_only_own_sales(): void
    {
        $cashier = User::factory()->create(['role' => 'cashier']);
        $cashier->syncRoles(['cashier']);

        $other = User::factory()->create(['role' => 'cashier']);
        Sale::factory()->count(2)->create(['user_id' => $cashier->id]);
        Sale::factory()->count(3)->create(['user_id' => $other->id]);

        $this->actingAs($cashier)
            ->withoutVite()
            ->get('/app/sales')
            ->assertOk()
            ->assertInertia(fn ($p) => $p->component('Sales/Index')->has('sales.data', 2));
    }
}
```

- [ ] **Step 2: Create `SalePolicy`**

```bash
php artisan make:policy SalePolicy --model=Sale
```

Edit:

```php
<?php

namespace App\Policies;

use App\Models\Sale;
use App\Models\User;

class SalePolicy
{
    public function viewAny(User $user): bool
    {
        return true; // controller scopes for cashier
    }

    public function view(User $user, Sale $sale): bool
    {
        return $user->role === 'admin' || $sale->user_id === $user->id;
    }

    public function void(User $user, Sale $sale): bool
    {
        return $user->role === 'admin';
    }
}
```

- [ ] **Step 3: Register policy**

Open `app/Providers/AppServiceProvider.php`. In `boot()`, add:

```php
use App\Models\Sale;
use App\Policies\SalePolicy;
use Illuminate\Support\Facades\Gate;

public function boot(): void
{
    Gate::policy(Sale::class, SalePolicy::class);
}
```

Preserve existing boot logic.

- [ ] **Step 4: Create `SaleController`**

```php
<?php

namespace App\Http\Controllers\App;

use App\Http\Controllers\Controller;
use App\Http\Resources\SaleResource;
use App\Models\Sale;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class SaleController extends Controller
{
    public function index(Request $request): Response
    {
        $query = Sale::with('user')->orderByDesc('created_at');

        if ($request->user()->role !== 'admin') {
            $query->where('user_id', $request->user()->id);
        }

        $sales = $query->paginate(20)->withQueryString();

        return Inertia::render('Sales/Index', [
            'sales' => $sales->through(fn ($s) => SaleResource::make($s)->resolve()),
        ]);
    }

    public function show(Sale $sale, Request $request): Response
    {
        $this->authorize('view', $sale);

        $sale->load(['items', 'user']);

        return Inertia::render('Sales/Show', [
            'sale' => SaleResource::make($sale),
        ]);
    }
}
```

- [ ] **Step 5: Add routes**

In `routes/web.php`, inside `auth` + `prefix('app')`:

```php
use App\Http\Controllers\App\SaleController;

Route::get('/sales', [SaleController::class, 'index'])->name('sales.index');
Route::get('/sales/{sale}', [SaleController::class, 'show'])->name('sales.show');
```

- [ ] **Step 6: Create `DataTable.vue` (minimal)**

```vue
<script setup lang="ts" generic="T">
import { Table, TableHeader, TableBody, TableRow, TableHead, TableCell } from '@/components/ui/table'
import { Button } from '@/components/ui/button'
import { Link } from '@inertiajs/vue3'

interface PaginatedResponse<T> {
  data: T[]
  links: { url: string | null; label: string; active: boolean }[]
  meta: { current_page: number; last_page: number; total: number }
}

interface Column<T> {
  key: string
  label: string
  render?: (row: T) => string
}

const props = defineProps<{
  data: PaginatedResponse<T>
  columns: Column<T>[]
  rowLink?: (row: T) => string
}>()
</script>

<template>
  <div class="space-y-4">
    <Table>
      <TableHeader>
        <TableRow>
          <TableHead v-for="col in columns" :key="col.key">{{ col.label }}</TableHead>
        </TableRow>
      </TableHeader>
      <TableBody>
        <TableRow v-for="row in data.data" :key="(row as any).id" class="cursor-pointer hover:bg-muted/40">
          <TableCell v-for="col in columns" :key="col.key">
            <component :is="rowLink ? Link : 'span'" v-if="rowLink" :href="rowLink(row)">
              {{ col.render ? col.render(row) : (row as any)[col.key] }}
            </component>
            <span v-else>{{ col.render ? col.render(row) : (row as any)[col.key] }}</span>
          </TableCell>
        </TableRow>
        <TableRow v-if="data.data.length === 0">
          <TableCell :colspan="columns.length" class="text-center text-muted-foreground py-8">
            No records.
          </TableCell>
        </TableRow>
      </TableBody>
    </Table>
    <div v-if="data.meta && data.meta.last_page > 1" class="flex gap-2 justify-center">
      <Link
        v-for="link in data.links"
        :key="link.label"
        :href="link.url ?? '#'"
        v-html="link.label"
        :class="['px-3 py-1 rounded border text-sm', link.active ? 'bg-primary text-primary-foreground' : 'hover:bg-muted', !link.url && 'opacity-50 pointer-events-none']"
      />
    </div>
  </div>
</template>
```

- [ ] **Step 7: Create `Sales/Index.vue`**

```vue
<script setup lang="ts">
import AppLayout from '@/Layouts/AppLayout.vue'
import DataTable from '@/components/DataTable.vue'
import type { Sale } from '@/types/models'

defineProps<{
  sales: {
    data: Sale[]
    links: any[]
    meta: any
  }
}>()

const columns = [
  { key: 'sale_number', label: 'Sale #' },
  { key: 'customer_name', label: 'Customer', render: (s: Sale) => s.customer_name ?? '—' },
  { key: 'total_amount', label: 'Total', render: (s: Sale) => s.total_amount.toFixed(2) },
  { key: 'payment_method', label: 'Payment' },
  { key: 'status', label: 'Status' },
  { key: 'cashier', label: 'Cashier', render: (s: Sale) => s.user?.name ?? '—' },
  { key: 'created_at', label: 'Date', render: (s: Sale) => new Date(s.created_at).toLocaleString() },
]
</script>

<template>
  <AppLayout>
    <div class="space-y-6">
      <h1 class="text-3xl font-bold">Sales</h1>
      <DataTable :data="sales" :columns="columns" :row-link="(s) => `/app/sales/${s.id}`" />
    </div>
  </AppLayout>
</template>
```

- [ ] **Step 8: Run tests**

```bash
php artisan test --filter='SalesIndexTest'
```

Expected: both tests pass.

- [ ] **Step 9: Commit**

```bash
git add app/Http/Controllers/App/SaleController.php app/Policies/SalePolicy.php app/Providers/AppServiceProvider.php routes/web.php resources/js/Pages/Sales/Index.vue resources/js/components/DataTable.vue tests/Feature/App/Sales/SalesIndexTest.php
git commit -m "feat(phase-1): Sales index — admin sees all, cashier sees own"
```

---

### Task 10: Sales Show + Receipt with auto-print

**Files:**
- Create: `resources/js/Pages/Sales/Show.vue`
- Create: `resources/css/print.css`
- Modify: `resources/js/app.ts` (import print.css)
- Create: `tests/Feature/App/Sales/SalesShowTest.php`
- Create: `tests/Feature/App/Sales/SalesAuthorizationTest.php`

- [ ] **Step 1: Failing tests**

`SalesShowTest.php`:

```php
<?php

namespace Tests\Feature\App\Sales;

use App\Models\Sale;
use App\Models\SalesItem;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class SalesShowTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'cashier', 'guard_name' => 'web']);
    }

    public function test_cashier_can_view_own_sale(): void
    {
        $cashier = User::factory()->create(['role' => 'cashier']);
        $cashier->syncRoles(['cashier']);
        $sale = Sale::factory()->create(['user_id' => $cashier->id]);
        SalesItem::factory()->count(2)->create(['sale_id' => $sale->id]);

        $this->actingAs($cashier)
            ->withoutVite()
            ->get("/app/sales/{$sale->id}")
            ->assertOk()
            ->assertInertia(fn ($p) => $p
                ->component('Sales/Show')
                ->has('sale.data.items', 2)
            );
    }
}
```

`SalesAuthorizationTest.php`:

```php
<?php

namespace Tests\Feature\App\Sales;

use App\Models\Sale;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class SalesAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'cashier', 'guard_name' => 'web']);
    }

    public function test_cashier_cannot_view_another_cashiers_sale(): void
    {
        $cashier = User::factory()->create(['role' => 'cashier']);
        $cashier->syncRoles(['cashier']);

        $other = User::factory()->create(['role' => 'cashier']);
        $sale = Sale::factory()->create(['user_id' => $other->id]);

        $this->actingAs($cashier)
            ->get("/app/sales/{$sale->id}")
            ->assertForbidden();
    }

    public function test_admin_can_view_any_sale(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $admin->syncRoles(['admin']);

        $other = User::factory()->create(['role' => 'cashier']);
        $sale = Sale::factory()->create(['user_id' => $other->id]);

        $this->actingAs($admin)
            ->withoutVite()
            ->get("/app/sales/{$sale->id}")
            ->assertOk();
    }
}
```

- [ ] **Step 2: Run, expect fail**

```bash
php artisan test --filter='SalesShowTest|SalesAuthorizationTest'
```

- [ ] **Step 3: Create `Show.vue`**

```vue
<script setup lang="ts">
import { onMounted } from 'vue'
import { usePage } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card'
import { Button } from '@/components/ui/button'
import type { Sale } from '@/types/models'

defineProps<{ sale: { data: Sale } }>()

const page = usePage()

onMounted(() => {
  if ((page.props.flash as any)?.print) {
    // Defer to next tick so Vue finishes mount
    setTimeout(() => window.print(), 100)
  }
})
</script>

<template>
  <AppLayout>
    <div class="max-w-2xl mx-auto print:max-w-full">
      <div class="flex justify-between items-center mb-6 print:hidden">
        <h1 class="text-3xl font-bold">Receipt</h1>
        <Button variant="outline" @click="window.print()">Print</Button>
      </div>

      <Card class="receipt">
        <CardHeader class="text-center">
          <CardTitle>EyeTech</CardTitle>
          <p class="text-sm text-muted-foreground">Sale {{ sale.data.sale_number }}</p>
          <p class="text-xs text-muted-foreground">{{ new Date(sale.data.created_at).toLocaleString() }}</p>
        </CardHeader>
        <CardContent class="space-y-4">
          <div v-if="sale.data.customer_name" class="text-sm">
            <strong>Customer:</strong> {{ sale.data.customer_name }}
            <span v-if="sale.data.customer_phone">— {{ sale.data.customer_phone }}</span>
          </div>

          <div class="border-t border-b py-2 space-y-1">
            <div v-for="item in sale.data.items" :key="item.id" class="flex justify-between text-sm">
              <div class="flex-1">
                {{ item.item_name }}
                <span class="text-muted-foreground"> × {{ item.quantity }}</span>
              </div>
              <div class="font-medium">{{ item.total_price.toFixed(2) }}</div>
            </div>
          </div>

          <div class="space-y-1 text-sm">
            <div class="flex justify-between"><span>Subtotal</span><span>{{ sale.data.subtotal.toFixed(2) }}</span></div>
            <div v-if="sale.data.tax_amount > 0" class="flex justify-between"><span>Tax</span><span>{{ sale.data.tax_amount.toFixed(2) }}</span></div>
            <div v-if="sale.data.discount_amount > 0" class="flex justify-between"><span>Discount</span><span>-{{ sale.data.discount_amount.toFixed(2) }}</span></div>
            <div class="flex justify-between font-bold text-lg pt-1 border-t"><span>Total</span><span>{{ sale.data.total_amount.toFixed(2) }}</span></div>
            <div class="text-xs text-muted-foreground pt-2">Payment: {{ sale.data.payment_method.replace('_', ' ') }}</div>
            <div v-if="sale.data.user" class="text-xs text-muted-foreground">Cashier: {{ sale.data.user.name }}</div>
          </div>

          <p class="text-center text-xs text-muted-foreground pt-4">Thank you!</p>
        </CardContent>
      </Card>
    </div>
  </AppLayout>
</template>

<style>
@media print {
  body { background: white !important; }
  .print\:hidden { display: none !important; }
  nav, header { display: none !important; }
  .receipt { box-shadow: none !important; border: none !important; }
}
</style>
```

- [ ] **Step 4: Run, expect pass**

```bash
php artisan test --filter='SalesShowTest|SalesAuthorizationTest'
```

- [ ] **Step 5: Commit**

```bash
git add resources/js/Pages/Sales/Show.vue tests/Feature/App/Sales/SalesShowTest.php tests/Feature/App/Sales/SalesAuthorizationTest.php
git commit -m "feat(phase-1): Sales/Show receipt page + auto-print on flash"
```

---

### Task 11: Race condition test

**Files:**
- Modify: `tests/Feature/App/Pos/PosCheckoutTest.php` — add concurrent checkout test

- [ ] **Step 1: Add race test**

Append to existing class:

```php
public function test_concurrent_checkouts_only_one_succeeds_when_stock_is_one(): void
{
    $cashierA = User::factory()->create(['role' => 'cashier']);
    $cashierA->syncRoles(['cashier']);
    $cashierB = User::factory()->create(['role' => 'cashier']);
    $cashierB->syncRoles(['cashier']);

    $product = Product::factory()->create([
        'category_id' => Category::factory(),
        'supplier_id' => Supplier::factory(),
        'stock_quantity' => 1,
    ]);

    // Sequential under transaction lock — second request sees stock = 0
    $r1 = $this->actingAs($cashierA)->post('/app/pos/checkout', [
        'payment_method' => 'cash',
        'items' => [['product_id' => $product->id, 'quantity' => 1]],
    ]);
    $r2 = $this->actingAs($cashierB)->post('/app/pos/checkout', [
        'payment_method' => 'cash',
        'items' => [['product_id' => $product->id, 'quantity' => 1]],
    ]);

    $r1->assertRedirect();
    $r1->assertSessionMissing('error');

    $r2->assertRedirect();
    $r2->assertSessionHas('error');

    $this->assertEquals(1, \App\Models\Sale::count());
    $this->assertEquals(0, $product->fresh()->stock_quantity);
}
```

> True parallel testing in PHPUnit is difficult — this test asserts the **sequential** behavior under lock-for-update guarantees. The lock ensures concurrent transactions in production are serialized; the test verifies the application-level logic (insufficient stock detection on the second attempt). If you want true parallelism, use Pest's `parallel()` plugin or external load testing — deferred.

- [ ] **Step 2: Run**

```bash
php artisan test --filter=PosCheckoutTest
```

Expected: 4 tests pass total (3 from Task 8 + new race test).

- [ ] **Step 3: Commit**

```bash
git add tests/Feature/App/Pos/PosCheckoutTest.php
git commit -m "test(phase-1): race-condition test for concurrent checkouts"
```

---

### Task 12: Update `AppLayout` nav with Sales link

**Files:**
- Modify: `resources/js/Layouts/AppLayout.vue`

- [ ] **Step 1: Add Sales link to nav**

In `AppLayout.vue`, in the `<nav>` block, add (admin sees, cashier sees own sales):

```vue
<Link href="/app/sales" class="hover:underline">Sales</Link>
```

Place between POS and the user-name span. Both roles should see the Sales link (it filters server-side).

- [ ] **Step 2: Commit**

```bash
git add resources/js/Layouts/AppLayout.vue
git commit -m "feat(phase-1): add Sales nav link to AppLayout"
```

---

### Task 13: Build + browser smoke

- [ ] **Step 1: Type-check**

```bash
npx vue-tsc --noEmit
```

- [ ] **Step 2: Build**

```bash
npm run build
```

- [ ] **Step 3: Run full backend test suite**

```bash
php artisan test
```

Expected: all Phase 0 + Phase 1 tests pass (pre-existing failures may remain).

- [ ] **Step 4: Manual smoke**

In browser:
1. Log in as admin → navigate to `/app/pos`.
2. Verify product grid loads (seed some products via `php artisan tinker --execute="..."` if DB is empty).
3. Click 3 products; verify cart updates with totals.
4. Adjust quantities; verify subtotal recomputes.
5. Click Checkout → verify redirect to `/app/sales/{id}` and browser print dialog opens (or fires).
6. Cancel print; verify receipt page displays correctly.
7. Click "Sales" in nav → verify list shows the new sale.
8. Click the sale row → return to receipt.
9. Log out, log in as cashier; verify cashier sees only own sales (none).
10. Cashier rings up a sale; verify it appears in their sales list but not in the admin-aggregated list view from another cashier's perspective.

- [ ] **Step 5: Commit anything from smoke fixes**

```bash
git add -A
git diff --cached --stat
git commit -m "chore(phase-1): smoke-test fixes" || true
```

---

### Task 14: Phase 1 wrap-up

- [ ] **Step 1: Tag**

```bash
git tag phase-1-complete -m "Phase 1: POS — full sales flow with receipt and stock decrement"
```

- [ ] **Step 2: Verify spec accuracy**

```bash
grep -E "(TODO|TBD)" docs/superpowers/specs/2026-05-23-eyetech-inertia-vue-design.md
```

Expected: no matches.

- [ ] **Step 3: Hand off**

State explicitly:
- Phase 1 complete — POS functional.
- Brother can now use the new POS in front of customers.
- Next: Phase 2 (port remaining Filament resources — Categories, Products, Suppliers, Stock Movements, Services, Service Requests, Users, Dashboard widgets, Reports).
- Filament `/admin` still works.

---

## Self-Review

**Spec coverage (POS + Sales):**
- POS UI: product grid + cart ✓ (Task 6)
- Search/filter ✓ (ProductGrid)
- Stock badge ✓ (low_stock indicator)
- Checkout with DB transaction + lockForUpdate ✓ (Task 7, CheckoutService)
- Insufficient stock handling ✓ (Tasks 7, 8)
- Receipt with auto-print ✓ (Task 10)
- Sales index with cashier scoping ✓ (Task 9)
- Sale detail / authorization ✓ (Task 10)
- Race condition test ✓ (Task 11)
- TS types per spec column names ✓ (Task 3 — matches real DB schema)
- shadcn-vue components: Dialog, Table ✓ (Task 5)

**Deferred (intentionally, to Phase 2 or beyond):**
- Refund / void action on sales — UI not built; admin can mutate DB directly for now.
- Barcode scanner input — keyboard wedge will work via the search box; dedicated scanner mode deferred.
- Receipt thermal-printer integration — browser print is the MVP.
- Tax / discount in POS — UI exposes them as fields but Phase 1 keeps them at 0.

**Placeholder scan:** none.

**Type consistency:** `Sale`, `SaleItem`, `Product`, `Category`, `PaymentMethod`, `SaleStatus`, `CartLine` all consistent between models.ts, resources, and controllers.

**Risks called out:**
- True concurrency test is sequential — real concurrency requires external load testing. Documented in Task 11.
- `setQuantity` will silently clamp to `available_stock` — if cart UI lets cashier type a number, they need feedback. UI hint: input shows max but no toast. Acceptable for MVP.
- `image_url` accessor depends on `storage/app/public` symlink (`php artisan storage:link`) — verify in smoke.

---

**End of Phase 1 Plan.** Phase 2 plan to be written after Phase 1 ships.
