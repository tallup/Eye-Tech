# EyeTech Inertia + Vue — Phase 0: Foundation Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Stand up the Inertia + Vue 3 + TypeScript + Tailwind 4 + shadcn-vue + Sanctum + roles foundation alongside the existing Filament panel. End state: shared login at `/login` works, role-based redirect lands admin on `/app/dashboard` (a minimal placeholder page) and cashier on `/app/pos` (placeholder page), and Filament at `/admin` continues working unchanged.

**Architecture:** Parallel-routes migration. Filament stays at `/admin`. New Vue app lives at `/app`. Both share the same `users` table and Laravel session guard. No code is removed; everything is additive. Spec: `docs/superpowers/specs/2026-05-23-eyetech-inertia-vue-design.md`.

**Tech Stack:** Laravel 12 (PHP 8.4), Inertia.js, Vue 3, TypeScript 5, Vite 7, Tailwind CSS 4, shadcn-vue, lucide-vue-next, spatie/laravel-permission, sonner. Tests: PHPUnit 11 (backend), Vitest + @vue/test-utils + happy-dom (frontend).

---

## File Structure

### New backend files

| Path | Responsibility |
|---|---|
| `database/migrations/2026_05_23_200000_add_role_to_users_table.php` | Adds `role` column to `users` |
| `database/migrations/2026_05_23_200001_create_permission_tables.php` | Stub or actual spatie/permission tables (published via artisan) |
| `database/seeders/RolesSeeder.php` | Seeds `admin` and `cashier` roles; promotes existing seeded admin user |
| `app/Http/Middleware/HandleInertiaRequests.php` | Shares `auth.user`, `flash`, `app` global props |
| `app/Http/Controllers/Auth/LoginController.php` | Show + handle login |
| `app/Http/Controllers/Auth/LogoutController.php` | Logout |
| `app/Http/Controllers/App/DashboardController.php` | `/app/dashboard` placeholder |
| `app/Http/Controllers/App/PosController.php` | `/app/pos` placeholder |
| `app/Http/Requests/Auth/LoginRequest.php` | Login validation |
| `resources/views/app.blade.php` | Inertia root HTML template |

### New frontend files

| Path | Responsibility |
|---|---|
| `resources/js/app.ts` | Inertia client bootstrap |
| `resources/js/ssr.ts` | (Stub; SSR disabled for now — file omitted) |
| `resources/js/Pages/Auth/Login.vue` | Login form |
| `resources/js/Pages/Dashboard.vue` | Placeholder admin dashboard |
| `resources/js/Pages/POS/Index.vue` | Placeholder POS page |
| `resources/js/Pages/Errors/403.vue` | Friendly 403 |
| `resources/js/Pages/Errors/404.vue` | Friendly 404 |
| `resources/js/Pages/Errors/500.vue` | Friendly 500 |
| `resources/js/Layouts/AppLayout.vue` | Admin shell |
| `resources/js/Layouts/PosLayout.vue` | POS shell (full-screen) |
| `resources/js/Layouts/GuestLayout.vue` | Centered card layout for login |
| `resources/js/components/ui/button/Button.vue` (+ index.ts) | shadcn Button |
| `resources/js/components/ui/input/Input.vue` (+ index.ts) | shadcn Input |
| `resources/js/components/ui/label/Label.vue` (+ index.ts) | shadcn Label |
| `resources/js/components/ui/card/Card.vue` etc. | shadcn Card subcomponents |
| `resources/js/composables/useAuth.ts` | Current user + role checks |
| `resources/js/composables/useForm.ts` | Inertia useForm + toast wrapper |
| `resources/js/types/models.ts` | Domain TS types (User, Role) |
| `resources/js/types/inertia.d.ts` | PageProps shape |
| `resources/js/lib/utils.ts` | `cn()` helper |
| `resources/css/app.css` | Tailwind 4 entry + shadcn CSS vars |
| `tsconfig.json` | TS config |
| `vite.config.ts` | (Replaces or extends current `vite.config.js`) |
| `vitest.config.ts` | Vitest config |
| `components.json` | shadcn-vue config |

### New test files

| Path | Responsibility |
|---|---|
| `tests/Feature/Auth/LoginTest.php` | Login flow + role redirect |
| `tests/Feature/Auth/LogoutTest.php` | Logout |
| `tests/Feature/App/DashboardTest.php` | Admin can access; cashier blocked |
| `tests/Feature/App/PosPlaceholderTest.php` | Both roles reach POS |
| `tests/Feature/CoexistenceTest.php` | `/admin/login` (Filament) still 200; `/login` returns Inertia |
| `resources/js/__tests__/useAuth.spec.ts` | `isAdmin`/`isCashier` logic |

### Modified files

| Path | Change |
|---|---|
| `composer.json` | Add `inertiajs/inertia-laravel`, `laravel/sanctum`, `spatie/laravel-permission` |
| `package.json` | Add Vue, Inertia, TS, shadcn deps, sonner, lucide-vue-next |
| `bootstrap/app.php` | Register `HandleInertiaRequests` middleware in web group; map exceptions to Inertia error pages |
| `routes/web.php` | Add `/login`, `/logout`, `/app/...` route group; preserve existing routes |
| `app/Models/User.php` | Add `HasRoles` trait; `role` accessor; cast `role` |
| `vite.config.js` | Renamed/upgraded to `vite.config.ts` with Vue plugin + TS path aliases |
| `tailwind.config.js` (if present) or app.css | Add shadcn CSS vars + dark-mode-friendly tokens |
| `database/seeders/AdminUserSeeder.php` | Assign `admin` role after creation |
| `phpunit.xml` | (No change — SQLite in-memory already configured) |

### Existing files NOT touched

- `app/Filament/**` — Phase 4 work only
- `app/Providers/Filament/AdminPanelProvider.php` — untouched
- All current models, migrations (except `users`), seeders (except `AdminUserSeeder`)
- All public Blade views (Phase 3)

---

## Task Decomposition

Each task is a logical commit. Within a task, steps are 2–5 minutes each.

### Task 1: Install backend packages

**Files:**
- Modify: `composer.json`
- Create: `config/sanctum.php` (via vendor:publish)
- Create: `config/permission.php` (via vendor:publish)
- Create: `database/migrations/2026_05_23_200001_create_permission_tables.php` (via vendor:publish)

- [ ] **Step 1: Install Inertia, Sanctum, spatie/permission**

Run:
```bash
composer require inertiajs/inertia-laravel laravel/sanctum spatie/laravel-permission
```

Expected: three packages installed, `composer.json` updated, no errors.

- [ ] **Step 2: Publish Sanctum config + migrations**

Run:
```bash
php artisan vendor:publish --provider="Laravel\Sanctum\SanctumServiceProvider"
```

Expected: `config/sanctum.php` created, migration for `personal_access_tokens` table added.

- [ ] **Step 3: Publish spatie/permission**

Run:
```bash
php artisan vendor:publish --provider="Spatie\Permission\PermissionServiceProvider"
```

Expected: `config/permission.php` created, `create_permission_tables` migration added.

- [ ] **Step 4: Verify composer.json contents**

Open `composer.json` and confirm `require` section now contains:
```json
"inertiajs/inertia-laravel": "^2.0",
"laravel/sanctum": "^4.0",
"spatie/laravel-permission": "^6.0"
```

Adjust version constraints to whatever composer resolved (do not downgrade).

- [ ] **Step 5: Commit**

```bash
git add composer.json composer.lock config/sanctum.php config/permission.php database/migrations/
git commit -m "feat(phase-0): install Inertia, Sanctum, spatie/permission"
```

---

### Task 2: Install frontend packages

**Files:**
- Modify: `package.json`
- Create: `package-lock.json` updates

- [ ] **Step 1: Install Inertia + Vue + TS toolchain**

Run:
```bash
npm install --save-dev vue@^3.5 @inertiajs/vue3@^2 @vitejs/plugin-vue@^5 typescript@^5 vue-tsc@^2 @types/node
```

Expected: packages installed, `package.json` `devDependencies` updated.

- [ ] **Step 2: Install shadcn-vue dependencies**

Run:
```bash
npm install class-variance-authority clsx tailwind-merge tailwindcss-animate lucide-vue-next radix-vue sonner
```

Expected: runtime deps added under `dependencies`.

- [ ] **Step 3: Install Vitest + test deps**

Run:
```bash
npm install --save-dev vitest @vue/test-utils happy-dom @vitest/coverage-v8
```

Expected: test toolchain in `devDependencies`.

- [ ] **Step 4: Verify `package.json`**

Open `package.json`. Confirm scripts section:
```json
"scripts": {
  "build": "vue-tsc --noEmit && vite build",
  "dev": "vite",
  "test": "vitest run",
  "test:watch": "vitest",
  "type-check": "vue-tsc --noEmit"
}
```

Edit if missing.

- [ ] **Step 5: Commit**

```bash
git add package.json package-lock.json
git commit -m "feat(phase-0): install Vue 3, Inertia, TS, shadcn deps, Vitest"
```

---

### Task 3: Vite + TypeScript config

**Files:**
- Delete: `vite.config.js` (if present)
- Create: `vite.config.ts`
- Create: `tsconfig.json`
- Create: `vitest.config.ts`

- [ ] **Step 1: Create `tsconfig.json`**

```json
{
  "compilerOptions": {
    "target": "ES2022",
    "module": "ESNext",
    "moduleResolution": "Bundler",
    "lib": ["ES2022", "DOM", "DOM.Iterable"],
    "strict": true,
    "jsx": "preserve",
    "esModuleInterop": true,
    "skipLibCheck": true,
    "resolveJsonModule": true,
    "isolatedModules": true,
    "noEmit": true,
    "baseUrl": ".",
    "paths": {
      "@/*": ["resources/js/*"]
    },
    "types": ["node", "vite/client"]
  },
  "include": ["resources/js/**/*.ts", "resources/js/**/*.d.ts", "resources/js/**/*.vue"],
  "exclude": ["node_modules", "vendor", "public"]
}
```

- [ ] **Step 2: Replace `vite.config.js` with `vite.config.ts`**

```ts
import { defineConfig } from 'vite'
import laravel from 'laravel-vite-plugin'
import vue from '@vitejs/plugin-vue'
import tailwindcss from '@tailwindcss/vite'
import path from 'path'

export default defineConfig({
  plugins: [
    laravel({
      input: ['resources/css/app.css', 'resources/js/app.ts'],
      refresh: true,
    }),
    vue({
      template: {
        transformAssetUrls: {
          base: null,
          includeAbsolute: false,
        },
      },
    }),
    tailwindcss(),
  ],
  resolve: {
    alias: {
      '@': path.resolve(__dirname, 'resources/js'),
    },
  },
})
```

Then:
```bash
rm vite.config.js
```

- [ ] **Step 3: Create `vitest.config.ts`**

```ts
import { defineConfig } from 'vitest/config'
import vue from '@vitejs/plugin-vue'
import path from 'path'

export default defineConfig({
  plugins: [vue()],
  test: {
    environment: 'happy-dom',
    globals: true,
    include: ['resources/js/**/*.spec.ts'],
  },
  resolve: {
    alias: {
      '@': path.resolve(__dirname, 'resources/js'),
    },
  },
})
```

- [ ] **Step 4: Run type-check to verify config wires up**

Run:
```bash
npx vue-tsc --noEmit
```

Expected: no errors (no `.ts` source yet — passes trivially).

- [ ] **Step 5: Commit**

```bash
git add tsconfig.json vite.config.ts vitest.config.ts
git rm vite.config.js
git commit -m "feat(phase-0): Vite + TS + Vitest config"
```

---

### Task 4: shadcn-vue scaffold

**Files:**
- Create: `components.json`
- Create: `resources/js/lib/utils.ts`
- Create: `resources/css/app.css` (replace existing)
- Create: `resources/js/components/ui/button/Button.vue`, `index.ts`
- Create: `resources/js/components/ui/input/Input.vue`, `index.ts`
- Create: `resources/js/components/ui/label/Label.vue`, `index.ts`
- Create: `resources/js/components/ui/card/Card.vue`, `CardHeader.vue`, `CardTitle.vue`, `CardDescription.vue`, `CardContent.vue`, `CardFooter.vue`, `index.ts`

- [ ] **Step 1: Create `components.json` (shadcn-vue config)**

```json
{
  "$schema": "https://shadcn-vue.com/schema.json",
  "style": "default",
  "typescript": true,
  "tailwind": {
    "config": "",
    "css": "resources/css/app.css",
    "baseColor": "neutral",
    "cssVariables": true
  },
  "aliases": {
    "components": "@/components",
    "utils": "@/lib/utils",
    "ui": "@/components/ui",
    "composables": "@/composables"
  }
}
```

- [ ] **Step 2: Create `resources/js/lib/utils.ts`**

```ts
import { type ClassValue, clsx } from 'clsx'
import { twMerge } from 'tailwind-merge'

export function cn(...inputs: ClassValue[]) {
  return twMerge(clsx(inputs))
}
```

- [ ] **Step 3: Replace `resources/css/app.css` with Tailwind 4 + shadcn CSS variables**

```css
@import "tailwindcss";

@theme {
  --color-background: hsl(0 0% 100%);
  --color-foreground: hsl(0 0% 3.9%);
  --color-card: hsl(0 0% 100%);
  --color-card-foreground: hsl(0 0% 3.9%);
  --color-popover: hsl(0 0% 100%);
  --color-popover-foreground: hsl(0 0% 3.9%);
  --color-primary: hsl(0 72% 51%);          /* EyeTech red */
  --color-primary-foreground: hsl(0 0% 98%);
  --color-secondary: hsl(0 0% 96%);
  --color-secondary-foreground: hsl(0 0% 9%);
  --color-muted: hsl(0 0% 96%);
  --color-muted-foreground: hsl(0 0% 45%);
  --color-accent: hsl(0 0% 96%);
  --color-accent-foreground: hsl(0 0% 9%);
  --color-destructive: hsl(0 84% 60%);
  --color-destructive-foreground: hsl(0 0% 98%);
  --color-border: hsl(0 0% 90%);
  --color-input: hsl(0 0% 90%);
  --color-ring: hsl(0 72% 51%);
  --radius: 0.5rem;
}

@layer base {
  * { @apply border-border; }
  body { @apply bg-background text-foreground antialiased; font-family: 'Inter', system-ui, sans-serif; }
}
```

- [ ] **Step 4: Create Button component**

`resources/js/components/ui/button/Button.vue`:

```vue
<script setup lang="ts">
import { cva, type VariantProps } from 'class-variance-authority'
import { cn } from '@/lib/utils'

const buttonVariants = cva(
  'inline-flex items-center justify-center gap-2 whitespace-nowrap rounded-md text-sm font-medium transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring disabled:pointer-events-none disabled:opacity-50',
  {
    variants: {
      variant: {
        default: 'bg-primary text-primary-foreground hover:bg-primary/90',
        destructive: 'bg-destructive text-destructive-foreground hover:bg-destructive/90',
        outline: 'border border-input bg-background hover:bg-accent hover:text-accent-foreground',
        secondary: 'bg-secondary text-secondary-foreground hover:bg-secondary/80',
        ghost: 'hover:bg-accent hover:text-accent-foreground',
        link: 'text-primary underline-offset-4 hover:underline',
      },
      size: {
        default: 'h-10 px-4 py-2',
        sm: 'h-9 rounded-md px-3',
        lg: 'h-11 rounded-md px-8',
        icon: 'h-10 w-10',
      },
    },
    defaultVariants: { variant: 'default', size: 'default' },
  },
)

export type ButtonVariants = VariantProps<typeof buttonVariants>

const props = defineProps<{
  variant?: ButtonVariants['variant']
  size?: ButtonVariants['size']
  type?: 'button' | 'submit' | 'reset'
  disabled?: boolean
}>()
</script>

<template>
  <button
    :type="type ?? 'button'"
    :disabled="disabled"
    :class="cn(buttonVariants({ variant, size }))"
  >
    <slot />
  </button>
</template>
```

`resources/js/components/ui/button/index.ts`:

```ts
export { default as Button } from './Button.vue'
```

- [ ] **Step 5: Create Input component**

`resources/js/components/ui/input/Input.vue`:

```vue
<script setup lang="ts">
import { cn } from '@/lib/utils'

const props = defineProps<{
  modelValue?: string | number
  type?: string
  placeholder?: string
  disabled?: boolean
  id?: string
  name?: string
  autocomplete?: string
}>()

defineEmits<{ 'update:modelValue': [value: string] }>()
</script>

<template>
  <input
    :id="id"
    :name="name"
    :type="type ?? 'text'"
    :placeholder="placeholder"
    :disabled="disabled"
    :autocomplete="autocomplete"
    :value="modelValue"
    @input="$emit('update:modelValue', ($event.target as HTMLInputElement).value)"
    :class="cn('flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring disabled:cursor-not-allowed disabled:opacity-50')"
  />
</template>
```

`resources/js/components/ui/input/index.ts`:

```ts
export { default as Input } from './Input.vue'
```

- [ ] **Step 6: Create Label component**

`resources/js/components/ui/label/Label.vue`:

```vue
<script setup lang="ts">
import { cn } from '@/lib/utils'

defineProps<{ for?: string }>()
</script>

<template>
  <label
    :for="for"
    :class="cn('text-sm font-medium leading-none peer-disabled:cursor-not-allowed peer-disabled:opacity-70')"
  >
    <slot />
  </label>
</template>
```

`resources/js/components/ui/label/index.ts`:

```ts
export { default as Label } from './Label.vue'
```

- [ ] **Step 7: Create Card components**

`resources/js/components/ui/card/Card.vue`:

```vue
<script setup lang="ts">
import { cn } from '@/lib/utils'
</script>

<template>
  <div :class="cn('rounded-lg border bg-card text-card-foreground shadow-sm')">
    <slot />
  </div>
</template>
```

`resources/js/components/ui/card/CardHeader.vue`:

```vue
<template>
  <div class="flex flex-col space-y-1.5 p-6">
    <slot />
  </div>
</template>
```

`resources/js/components/ui/card/CardTitle.vue`:

```vue
<template>
  <h3 class="text-2xl font-semibold leading-none tracking-tight">
    <slot />
  </h3>
</template>
```

`resources/js/components/ui/card/CardDescription.vue`:

```vue
<template>
  <p class="text-sm text-muted-foreground">
    <slot />
  </p>
</template>
```

`resources/js/components/ui/card/CardContent.vue`:

```vue
<template>
  <div class="p-6 pt-0">
    <slot />
  </div>
</template>
```

`resources/js/components/ui/card/CardFooter.vue`:

```vue
<template>
  <div class="flex items-center p-6 pt-0">
    <slot />
  </div>
</template>
```

`resources/js/components/ui/card/index.ts`:

```ts
export { default as Card } from './Card.vue'
export { default as CardHeader } from './CardHeader.vue'
export { default as CardTitle } from './CardTitle.vue'
export { default as CardDescription } from './CardDescription.vue'
export { default as CardContent } from './CardContent.vue'
export { default as CardFooter } from './CardFooter.vue'
```

- [ ] **Step 8: Commit**

```bash
git add components.json resources/js/lib resources/js/components resources/css/app.css
git commit -m "feat(phase-0): shadcn-vue base components (Button, Input, Label, Card)"
```

---

### Task 5: Inertia root template + app entry

**Files:**
- Create: `resources/views/app.blade.php`
- Create: `resources/js/types/inertia.d.ts`
- Create: `resources/js/types/models.ts`
- Create: `resources/js/app.ts`

- [ ] **Step 1: Create `resources/views/app.blade.php`**

```blade
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title inertia>{{ config('app.name', 'EyeTech') }}</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('images/favicon.ico') }}">
    @routes
    @vite(['resources/css/app.css', 'resources/js/app.ts'])
    @inertiaHead
</head>
<body>
    @inertia
</body>
</html>
```

> **Note:** `@routes` is from Ziggy if installed — if not, remove that line. We are NOT installing Ziggy in this plan; remove the `@routes` directive now:

Final version (no Ziggy):

```blade
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title inertia>{{ config('app.name', 'EyeTech') }}</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('images/favicon.ico') }}">
    @vite(['resources/css/app.css', 'resources/js/app.ts'])
    @inertiaHead
</head>
<body>
    @inertia
</body>
</html>
```

- [ ] **Step 2: Create `resources/js/types/models.ts`**

```ts
export type Role = 'admin' | 'cashier'

export interface User {
  id: number
  name: string
  email: string
  role: Role
}
```

- [ ] **Step 3: Create `resources/js/types/inertia.d.ts`**

```ts
import type { User } from './models'

declare module '@inertiajs/core' {
  interface PageProps {
    auth: { user: User | null }
    flash: {
      success: string | null
      error: string | null
      print: boolean | null
    }
    app: { name: string; version: string }
  }
}

export {}
```

- [ ] **Step 4: Create `resources/js/app.ts`**

```ts
import { createApp, h } from 'vue'
import { createInertiaApp, router } from '@inertiajs/vue3'
import { Toaster, toast } from 'sonner'
import '../css/app.css'

createInertiaApp({
  title: (title) => `${title} — EyeTech`,
  resolve: (name) => {
    const pages = import.meta.glob('./Pages/**/*.vue', { eager: false })
    return pages[`./Pages/${name}.vue`]() as Promise<any>
  },
  setup({ el, App, props, plugin }) {
    const app = createApp({ render: () => h(App, props) })
    app.use(plugin)
    app.component('Toaster', Toaster)
    app.mount(el)
  },
  progress: { color: 'hsl(0 72% 51%)' },
})

router.on('error', () => {
  toast.error('Connection lost — please retry.')
})
```

- [ ] **Step 5: Commit**

```bash
git add resources/views/app.blade.php resources/js/types resources/js/app.ts
git commit -m "feat(phase-0): Inertia root template and app entry"
```

---

### Task 6: Inertia server middleware + bootstrap wiring

**Files:**
- Create: `app/Http/Middleware/HandleInertiaRequests.php`
- Modify: `bootstrap/app.php`

- [ ] **Step 1: Create middleware**

`app/Http/Middleware/HandleInertiaRequests.php`:

```php
<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    protected $rootView = 'app';

    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    public function share(Request $request): array
    {
        return array_merge(parent::share($request), [
            'auth' => [
                'user' => fn () => $request->user()?->only(['id', 'name', 'email', 'role']),
            ],
            'flash' => [
                'success' => fn () => $request->session()->get('success'),
                'error' => fn () => $request->session()->get('error'),
                'print' => fn () => $request->session()->get('print'),
            ],
            'app' => [
                'name' => config('app.name', 'EyeTech'),
                'version' => '0.1.0',
            ],
        ]);
    }
}
```

- [ ] **Step 2: Wire middleware in `bootstrap/app.php`**

Replace entire file:

```php
<?php

use App\Http\Middleware\HandleInertiaRequests;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->web(append: [
            HandleInertiaRequests::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
```

- [ ] **Step 3: Commit**

```bash
git add app/Http/Middleware/HandleInertiaRequests.php bootstrap/app.php
git commit -m "feat(phase-0): wire Inertia middleware"
```

---

### Task 7: Roles migration + spatie integration

**Files:**
- Modify: `app/Models/User.php`
- Create: `database/migrations/2026_05_23_200000_add_role_to_users_table.php`
- Create: `database/seeders/RolesSeeder.php`
- Modify: `database/seeders/DatabaseSeeder.php`
- Modify: `database/seeders/AdminUserSeeder.php`

- [ ] **Step 1: Create migration for `role` column**

```bash
php artisan make:migration add_role_to_users_table --table=users
```

Edit the generated file (timestamp will be current); replace `up()` and `down()`:

```php
public function up(): void
{
    Schema::table('users', function (Blueprint $table) {
        $table->enum('role', ['admin', 'cashier'])->default('cashier')->after('email');
    });
}

public function down(): void
{
    Schema::table('users', function (Blueprint $table) {
        $table->dropColumn('role');
    });
}
```

- [ ] **Step 2: Add `HasRoles` to `User` model**

Open `app/Models/User.php`. Add at top of imports:

```php
use Spatie\Permission\Traits\HasRoles;
```

Add trait to class and update `$fillable` + `$casts`:

```php
class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles;

    protected $fillable = [
        'name', 'email', 'password', 'role',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'role' => 'string',
        ];
    }
}
```

> Preserve any existing fields in `$fillable` — merge, do not replace blindly. Re-read the file first.

- [ ] **Step 3: Create `RolesSeeder`**

```bash
php artisan make:seeder RolesSeeder
```

Edit the generated file:

```php
<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RolesSeeder extends Seeder
{
    public function run(): void
    {
        Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'cashier', 'guard_name' => 'web']);
    }
}
```

- [ ] **Step 4: Modify `AdminUserSeeder` to assign role**

Replace contents of `database/seeders/AdminUserSeeder.php`:

```php
<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::updateOrCreate(
            ['email' => 'admin@eyetech.com'],
            [
                'name' => 'EyeTech Admin',
                'password' => Hash::make('admin123'),
                'role' => 'admin',
                'email_verified_at' => now(),
            ],
        );

        $admin->syncRoles(['admin']);
    }
}
```

- [ ] **Step 5: Ensure `DatabaseSeeder` calls `RolesSeeder` BEFORE `AdminUserSeeder`**

Read `database/seeders/DatabaseSeeder.php`. The `call` array must list `RolesSeeder::class` before `AdminUserSeeder::class`. Add if missing:

```php
$this->call([
    RolesSeeder::class,
    AdminUserSeeder::class,
    // ...existing seeders preserved
]);
```

- [ ] **Step 6: Run migrations + seed**

```bash
php artisan migrate
php artisan db:seed --class=RolesSeeder
php artisan db:seed --class=AdminUserSeeder
```

Expected: migrations applied, no errors, admin user has `role = 'admin'` AND `model_has_roles` row.

- [ ] **Step 7: Verify in DB**

```bash
php artisan tinker --execute="echo App\Models\User::where('email','admin@eyetech.com')->first()->role; echo ' / '; print_r(App\Models\User::where('email','admin@eyetech.com')->first()->getRoleNames()->toArray());"
```

Expected output contains `admin` twice.

- [ ] **Step 8: Commit**

```bash
git add database/migrations app/Models/User.php database/seeders
git commit -m "feat(phase-0): add roles via spatie/permission + role column on users"
```

---

### Task 8: Login route + controller (TDD)

**Files:**
- Create: `app/Http/Controllers/Auth/LoginController.php`
- Create: `app/Http/Controllers/Auth/LogoutController.php`
- Create: `app/Http/Requests/Auth/LoginRequest.php`
- Modify: `routes/web.php`
- Create: `tests/Feature/Auth/LoginTest.php`
- Create: `tests/Feature/Auth/LogoutTest.php`

- [ ] **Step 1: Write failing login test**

`tests/Feature/Auth/LoginTest.php`:

```php
<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'cashier', 'guard_name' => 'web']);
    }

    public function test_login_page_renders_inertia_component(): void
    {
        $this->get('/login')
            ->assertOk()
            ->assertInertia(fn ($p) => $p->component('Auth/Login'));
    }

    public function test_admin_logs_in_and_is_redirected_to_dashboard(): void
    {
        $admin = User::factory()->create([
            'email' => 'admin@example.test',
            'password' => Hash::make('secret123'),
            'role' => 'admin',
        ]);
        $admin->syncRoles(['admin']);

        $this->post('/login', [
            'email' => 'admin@example.test',
            'password' => 'secret123',
        ])
            ->assertRedirect('/app/dashboard');

        $this->assertAuthenticatedAs($admin);
    }

    public function test_cashier_logs_in_and_is_redirected_to_pos(): void
    {
        $cashier = User::factory()->create([
            'email' => 'cashier@example.test',
            'password' => Hash::make('secret123'),
            'role' => 'cashier',
        ]);
        $cashier->syncRoles(['cashier']);

        $this->post('/login', [
            'email' => 'cashier@example.test',
            'password' => 'secret123',
        ])
            ->assertRedirect('/app/pos');
    }

    public function test_invalid_credentials_return_with_error(): void
    {
        User::factory()->create([
            'email' => 'foo@bar.test',
            'password' => Hash::make('right-password'),
        ]);

        $this->from('/login')
            ->post('/login', ['email' => 'foo@bar.test', 'password' => 'wrong'])
            ->assertRedirect('/login')
            ->assertSessionHasErrors('email');

        $this->assertGuest();
    }
}
```

- [ ] **Step 2: Add Inertia testing helper to base TestCase**

Open `tests/TestCase.php`. If `Inertia\Testing\AssertableInertia` is not already importable, no change needed — the `assertInertia` macro is provided by inertiajs/inertia-laravel. Verify by running tests next step.

- [ ] **Step 3: Run failing test**

```bash
php artisan test --filter=LoginTest
```

Expected: tests fail — `/login` route does not exist (404).

- [ ] **Step 4: Create `LoginRequest`**

```bash
php artisan make:request Auth/LoginRequest
```

Edit:

```php
<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
            'remember' => ['boolean'],
        ];
    }
}
```

- [ ] **Step 5: Create `LoginController`**

`app/Http/Controllers/Auth/LoginController.php`:

```php
<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class LoginController extends Controller
{
    public function create(): Response
    {
        return Inertia::render('Auth/Login');
    }

    public function store(LoginRequest $request): RedirectResponse
    {
        $credentials = $request->only('email', 'password');

        if (! Auth::attempt($credentials, $request->boolean('remember'))) {
            return back()->withErrors(['email' => 'These credentials do not match our records.']);
        }

        $request->session()->regenerate();

        $user = $request->user();
        $target = $user->role === 'admin' ? '/app/dashboard' : '/app/pos';

        return redirect()->intended($target);
    }
}
```

- [ ] **Step 6: Create `LogoutController`**

`app/Http/Controllers/Auth/LogoutController.php`:

```php
<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LogoutController extends Controller
{
    public function __invoke(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }
}
```

- [ ] **Step 7: Add routes in `routes/web.php`**

Read the file first. Append (do not replace existing routes):

```php
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\LogoutController;

Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'create'])->name('login');
    Route::post('/login', [LoginController::class, 'store']);
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', LogoutController::class)->name('logout');
});
```

- [ ] **Step 8: Create minimal `Pages/Auth/Login.vue` so the Inertia component-name assertion passes**

`resources/js/Pages/Auth/Login.vue`:

```vue
<script setup lang="ts">
import { useForm } from '@inertiajs/vue3'
import { Button } from '@/components/ui/button'
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'
import { Card, CardHeader, CardTitle, CardDescription, CardContent } from '@/components/ui/card'

const form = useForm({
  email: '',
  password: '',
  remember: false,
})

const submit = () => {
  form.post('/login', { onFinish: () => form.reset('password') })
}
</script>

<template>
  <div class="min-h-screen flex items-center justify-center bg-muted/40 px-4">
    <Card class="w-full max-w-md">
      <CardHeader>
        <CardTitle>Sign in</CardTitle>
        <CardDescription>EyeTech admin & POS</CardDescription>
      </CardHeader>
      <CardContent>
        <form @submit.prevent="submit" class="space-y-4">
          <div class="space-y-2">
            <Label for="email">Email</Label>
            <Input
              id="email"
              type="email"
              autocomplete="email"
              v-model="form.email"
              :disabled="form.processing"
            />
            <p v-if="form.errors.email" class="text-sm text-destructive">{{ form.errors.email }}</p>
          </div>
          <div class="space-y-2">
            <Label for="password">Password</Label>
            <Input
              id="password"
              type="password"
              autocomplete="current-password"
              v-model="form.password"
              :disabled="form.processing"
            />
            <p v-if="form.errors.password" class="text-sm text-destructive">{{ form.errors.password }}</p>
          </div>
          <Button type="submit" :disabled="form.processing" class="w-full">
            Sign in
          </Button>
        </form>
      </CardContent>
    </Card>
  </div>
</template>
```

- [ ] **Step 9: Run tests**

```bash
php artisan test --filter=LoginTest
```

Expected: all 4 tests pass. If the redirect-to-/app/dashboard test fails because that route 404s, that's a routing chain issue — we add the dashboard placeholder route in Task 9; for now, also add throwaway routes so the redirect target exists:

Append to `routes/web.php`:

```php
Route::middleware('auth')->prefix('app')->group(function () {
    Route::get('/dashboard', fn () => 'placeholder')->name('dashboard');
    Route::get('/pos', fn () => 'placeholder')->name('pos');
});
```

These get replaced in Task 9. Re-run tests — should pass.

- [ ] **Step 10: Write + run logout test**

`tests/Feature/Auth/LogoutTest.php`:

```php
<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LogoutTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_logout(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user)
            ->post('/logout')
            ->assertRedirect('/login');

        $this->assertGuest();
    }
}
```

Run:
```bash
php artisan test --filter=LogoutTest
```

Expected: passes.

- [ ] **Step 11: Commit**

```bash
git add app/Http/Controllers/Auth app/Http/Requests/Auth routes/web.php resources/js/Pages/Auth tests/Feature/Auth
git commit -m "feat(phase-0): shared login with role-based redirect (TDD)"
```

---

### Task 9: Dashboard + POS placeholder pages (TDD)

**Files:**
- Create: `app/Http/Controllers/App/DashboardController.php`
- Create: `app/Http/Controllers/App/PosController.php`
- Modify: `routes/web.php` (replace placeholder routes from Task 8)
- Create: `resources/js/Layouts/AppLayout.vue`
- Create: `resources/js/Layouts/PosLayout.vue`
- Create: `resources/js/Pages/Dashboard.vue`
- Create: `resources/js/Pages/POS/Index.vue`
- Create: `tests/Feature/App/DashboardTest.php`
- Create: `tests/Feature/App/PosPlaceholderTest.php`

- [ ] **Step 1: Write failing tests**

`tests/Feature/App/DashboardTest.php`:

```php
<?php

namespace Tests\Feature\App;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'cashier', 'guard_name' => 'web']);
    }

    public function test_admin_sees_dashboard(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $admin->syncRoles(['admin']);

        $this->actingAs($admin)
            ->get('/app/dashboard')
            ->assertOk()
            ->assertInertia(fn ($p) => $p->component('Dashboard'));
    }

    public function test_cashier_blocked_from_dashboard(): void
    {
        $cashier = User::factory()->create(['role' => 'cashier']);
        $cashier->syncRoles(['cashier']);

        $this->actingAs($cashier)
            ->get('/app/dashboard')
            ->assertForbidden();
    }

    public function test_guest_redirected_to_login(): void
    {
        $this->get('/app/dashboard')
            ->assertRedirect('/login');
    }
}
```

`tests/Feature/App/PosPlaceholderTest.php`:

```php
<?php

namespace Tests\Feature\App;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class PosPlaceholderTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'cashier', 'guard_name' => 'web']);
    }

    public function test_admin_can_reach_pos(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $admin->syncRoles(['admin']);

        $this->actingAs($admin)
            ->get('/app/pos')
            ->assertOk()
            ->assertInertia(fn ($p) => $p->component('POS/Index'));
    }

    public function test_cashier_can_reach_pos(): void
    {
        $cashier = User::factory()->create(['role' => 'cashier']);
        $cashier->syncRoles(['cashier']);

        $this->actingAs($cashier)
            ->get('/app/pos')
            ->assertOk()
            ->assertInertia(fn ($p) => $p->component('POS/Index'));
    }
}
```

- [ ] **Step 2: Run tests, expect failure**

```bash
php artisan test --filter='App\\\\(DashboardTest|PosPlaceholderTest)'
```

Expected: routes return placeholder string, not Inertia — fail.

- [ ] **Step 3: Create `DashboardController`**

```php
<?php

namespace App\Http\Controllers\App;

use App\Http\Controllers\Controller;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function __invoke(): Response
    {
        return Inertia::render('Dashboard');
    }
}
```

- [ ] **Step 4: Create `PosController`**

```php
<?php

namespace App\Http\Controllers\App;

use App\Http\Controllers\Controller;
use Inertia\Inertia;
use Inertia\Response;

class PosController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('POS/Index');
    }
}
```

- [ ] **Step 5: Replace placeholder routes in `routes/web.php`**

Find the placeholder block added in Task 8:

```php
Route::middleware('auth')->prefix('app')->group(function () {
    Route::get('/dashboard', fn () => 'placeholder')->name('dashboard');
    Route::get('/pos', fn () => 'placeholder')->name('pos');
});
```

Replace with:

```php
use App\Http\Controllers\App\DashboardController;
use App\Http\Controllers\App\PosController;

Route::middleware('auth')->prefix('app')->group(function () {
    Route::middleware('role:admin')->group(function () {
        Route::get('/dashboard', DashboardController::class)->name('dashboard');
    });
    Route::get('/pos', [PosController::class, 'index'])->name('pos');
});
```

- [ ] **Step 6: Register `role` middleware alias**

Modify `bootstrap/app.php` middleware block to register spatie's role middleware:

```php
->withMiddleware(function (Middleware $middleware): void {
    $middleware->web(append: [
        HandleInertiaRequests::class,
    ]);
    $middleware->alias([
        'role' => \Spatie\Permission\Middleware\RoleMiddleware::class,
    ]);
})
```

- [ ] **Step 7: Create `AppLayout.vue` (minimal)**

`resources/js/Layouts/AppLayout.vue`:

```vue
<script setup lang="ts">
import { computed } from 'vue'
import { Link, usePage, router } from '@inertiajs/vue3'
import { Toaster } from 'sonner'
import { Button } from '@/components/ui/button'
import type { User } from '@/types/models'

const page = usePage()
const user = computed(() => (page.props.auth as { user: User | null }).user)
const isAdmin = computed(() => user.value?.role === 'admin')

const logout = () => router.post('/logout')
</script>

<template>
  <div class="min-h-screen bg-muted/40">
    <header class="bg-background border-b">
      <div class="container mx-auto flex items-center justify-between px-4 py-3">
        <Link href="/app/dashboard" class="font-semibold">EyeTech</Link>
        <nav class="flex items-center gap-4 text-sm">
          <Link v-if="isAdmin" href="/app/dashboard" class="hover:underline">Dashboard</Link>
          <Link href="/app/pos" class="hover:underline">POS</Link>
          <span class="text-muted-foreground">{{ user?.name }}</span>
          <Button variant="outline" size="sm" @click="logout">Sign out</Button>
        </nav>
      </div>
    </header>
    <main class="container mx-auto px-4 py-8">
      <slot />
    </main>
    <Toaster position="top-right" richColors />
  </div>
</template>
```

- [ ] **Step 8: Create `PosLayout.vue` (minimal)**

`resources/js/Layouts/PosLayout.vue`:

```vue
<script setup lang="ts">
import { computed } from 'vue'
import { usePage, router } from '@inertiajs/vue3'
import { Toaster } from 'sonner'
import { Button } from '@/components/ui/button'
import type { User } from '@/types/models'

const page = usePage()
const user = computed(() => (page.props.auth as { user: User | null }).user)

const logout = () => router.post('/logout')
</script>

<template>
  <div class="min-h-screen bg-background flex flex-col">
    <header class="bg-primary text-primary-foreground px-6 py-3 flex items-center justify-between">
      <h1 class="font-semibold">EyeTech POS</h1>
      <div class="flex items-center gap-3 text-sm">
        <span>{{ user?.name }}</span>
        <Button variant="secondary" size="sm" @click="logout">Sign out</Button>
      </div>
    </header>
    <main class="flex-1 p-6">
      <slot />
    </main>
    <Toaster position="top-right" richColors />
  </div>
</template>
```

- [ ] **Step 9: Create placeholder pages**

`resources/js/Pages/Dashboard.vue`:

```vue
<script setup lang="ts">
import AppLayout from '@/Layouts/AppLayout.vue'
import { Card, CardHeader, CardTitle, CardContent } from '@/components/ui/card'
</script>

<template>
  <AppLayout>
    <div class="space-y-6">
      <h1 class="text-3xl font-bold">Dashboard</h1>
      <Card>
        <CardHeader>
          <CardTitle>Welcome to the new EyeTech admin</CardTitle>
        </CardHeader>
        <CardContent>
          <p class="text-muted-foreground">Stats and charts coming in Phase 2.</p>
        </CardContent>
      </Card>
    </div>
  </AppLayout>
</template>
```

`resources/js/Pages/POS/Index.vue`:

```vue
<script setup lang="ts">
import PosLayout from '@/Layouts/PosLayout.vue'
import { Card, CardHeader, CardTitle, CardContent } from '@/components/ui/card'
</script>

<template>
  <PosLayout>
    <Card>
      <CardHeader>
        <CardTitle>POS</CardTitle>
      </CardHeader>
      <CardContent>
        <p class="text-muted-foreground">Phase 1 ships the full POS here.</p>
      </CardContent>
    </Card>
  </PosLayout>
</template>
```

- [ ] **Step 10: Run tests**

```bash
php artisan test --filter='App\\\\(DashboardTest|PosPlaceholderTest)'
```

Expected: all 5 tests pass.

- [ ] **Step 11: Commit**

```bash
git add app/Http/Controllers/App routes/web.php bootstrap/app.php resources/js/Layouts resources/js/Pages tests/Feature/App
git commit -m "feat(phase-0): /app/dashboard (admin) + /app/pos placeholder pages"
```

---

### Task 10: Coexistence test — Filament still works

**Files:**
- Create: `tests/Feature/CoexistenceTest.php`

- [ ] **Step 1: Write the test**

```php
<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class CoexistenceTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
    }

    public function test_filament_login_page_still_renders(): void
    {
        $this->get('/admin/login')->assertOk();
    }

    public function test_inertia_login_page_renders(): void
    {
        $this->get('/login')
            ->assertOk()
            ->assertInertia(fn ($p) => $p->component('Auth/Login'));
    }

    public function test_filament_admin_redirects_unauthed(): void
    {
        $this->get('/admin')->assertRedirect();
    }

    public function test_both_panels_share_same_users_table(): void
    {
        $admin = User::factory()->create([
            'email' => 'shared@eyetech.test',
            'role' => 'admin',
        ]);
        $admin->syncRoles(['admin']);

        // Auth via shared guard — Filament reads same session
        $this->actingAs($admin)
            ->get('/admin')
            ->assertOk();
    }
}
```

- [ ] **Step 2: Run**

```bash
php artisan test --filter=CoexistenceTest
```

Expected: all pass. If `/admin` redirects to login when authed with `admin` role, check Filament panel's authorization — admin user has role but may need Filament's `canAccessPanel` to return true. If Filament 4 requires explicit grant, add this method to `User` model (do not break existing):

```php
public function canAccessPanel(\Filament\Panel $panel): bool
{
    return $this->role === 'admin';
}
```

Re-run.

- [ ] **Step 3: Commit**

```bash
git add tests/Feature/CoexistenceTest.php app/Models/User.php
git commit -m "test(phase-0): verify Filament and Inertia coexist"
```

---

### Task 11: Composables + frontend tests

**Files:**
- Create: `resources/js/composables/useAuth.ts`
- Create: `resources/js/composables/useForm.ts`
- Create: `resources/js/__tests__/useAuth.spec.ts`

- [ ] **Step 1: Write failing Vitest spec**

`resources/js/__tests__/useAuth.spec.ts`:

```ts
import { describe, expect, it, vi } from 'vitest'
import { useAuth } from '@/composables/useAuth'

vi.mock('@inertiajs/vue3', () => ({
  usePage: vi.fn(),
}))

import { usePage } from '@inertiajs/vue3'

describe('useAuth', () => {
  it('returns isAdmin=true when user role is admin', () => {
    ;(usePage as any).mockReturnValue({
      props: { auth: { user: { id: 1, name: 'A', email: 'a@x', role: 'admin' } } },
    })
    const { user, isAdmin, isCashier } = useAuth()
    expect(user.value?.role).toBe('admin')
    expect(isAdmin.value).toBe(true)
    expect(isCashier.value).toBe(false)
  })

  it('returns isCashier=true when user role is cashier', () => {
    ;(usePage as any).mockReturnValue({
      props: { auth: { user: { id: 2, name: 'B', email: 'b@x', role: 'cashier' } } },
    })
    const { isAdmin, isCashier } = useAuth()
    expect(isAdmin.value).toBe(false)
    expect(isCashier.value).toBe(true)
  })

  it('returns null user when unauthenticated', () => {
    ;(usePage as any).mockReturnValue({ props: { auth: { user: null } } })
    const { user, isAdmin } = useAuth()
    expect(user.value).toBeNull()
    expect(isAdmin.value).toBe(false)
  })
})
```

- [ ] **Step 2: Run test, expect failure**

```bash
npm test -- useAuth
```

Expected: module not found.

- [ ] **Step 3: Create `useAuth`**

`resources/js/composables/useAuth.ts`:

```ts
import { computed } from 'vue'
import { usePage } from '@inertiajs/vue3'
import type { User } from '@/types/models'

export function useAuth() {
  const page = usePage()
  const user = computed<User | null>(() => (page.props.auth as { user: User | null }).user)
  const isAdmin = computed(() => user.value?.role === 'admin')
  const isCashier = computed(() => user.value?.role === 'cashier')
  return { user, isAdmin, isCashier }
}
```

- [ ] **Step 4: Run test, expect pass**

```bash
npm test -- useAuth
```

Expected: 3 passing.

- [ ] **Step 5: Create `useForm` wrapper**

`resources/js/composables/useForm.ts`:

```ts
import { useForm as inertiaUseForm } from '@inertiajs/vue3'
import { toast } from 'sonner'

type FormDataType = Record<string, any>

export function useForm<T extends FormDataType>(initial: T) {
  return inertiaUseForm(initial as any) as ReturnType<typeof inertiaUseForm<T>> & {
    submitWithToast: (method: 'post' | 'put' | 'patch' | 'delete', url: string, successMessage?: string) => void
  }
}

// Helper users will reach for: a quick wrapper that toasts on success
export function submitWithToast<T extends FormDataType>(
  form: ReturnType<typeof inertiaUseForm<T>>,
  method: 'post' | 'put' | 'patch' | 'delete',
  url: string,
  successMessage = 'Saved',
) {
  form[method](url, {
    onSuccess: () => toast.success(successMessage),
    onError: () => toast.error('Please check the form for errors.'),
  })
}
```

> Note: this composable is intentionally thin — pages can still call `form.post()` directly. `submitWithToast` is the opinionated helper.

- [ ] **Step 6: Commit**

```bash
git add resources/js/composables resources/js/__tests__
git commit -m "feat(phase-0): useAuth + useForm composables with tests"
```

---

### Task 12: Error pages

**Files:**
- Create: `resources/js/Pages/Errors/403.vue`
- Create: `resources/js/Pages/Errors/404.vue`
- Create: `resources/js/Pages/Errors/500.vue`
- Modify: `bootstrap/app.php` to map exceptions to Inertia

- [ ] **Step 1: Create `403.vue`**

```vue
<script setup lang="ts">
import { Link } from '@inertiajs/vue3'
import { Button } from '@/components/ui/button'
</script>

<template>
  <div class="min-h-screen flex flex-col items-center justify-center px-4">
    <h1 class="text-6xl font-bold">403</h1>
    <p class="text-lg text-muted-foreground mt-2">You don't have access to this page.</p>
    <Link href="/" class="mt-6">
      <Button>Go home</Button>
    </Link>
  </div>
</template>
```

- [ ] **Step 2: Create `404.vue`** (same shape, swap text)

```vue
<script setup lang="ts">
import { Link } from '@inertiajs/vue3'
import { Button } from '@/components/ui/button'
</script>

<template>
  <div class="min-h-screen flex flex-col items-center justify-center px-4">
    <h1 class="text-6xl font-bold">404</h1>
    <p class="text-lg text-muted-foreground mt-2">Page not found.</p>
    <Link href="/" class="mt-6">
      <Button>Go home</Button>
    </Link>
  </div>
</template>
```

- [ ] **Step 3: Create `500.vue`**

```vue
<script setup lang="ts">
import { Link } from '@inertiajs/vue3'
import { Button } from '@/components/ui/button'
</script>

<template>
  <div class="min-h-screen flex flex-col items-center justify-center px-4">
    <h1 class="text-6xl font-bold">500</h1>
    <p class="text-lg text-muted-foreground mt-2">Something went wrong on our end.</p>
    <Link href="/" class="mt-6">
      <Button>Go home</Button>
    </Link>
  </div>
</template>
```

- [ ] **Step 4: Wire exception handler in `bootstrap/app.php`**

Replace the `withExceptions` block:

```php
->withExceptions(function (Exceptions $exceptions): void {
    $exceptions->respond(function (\Symfony\Component\HttpFoundation\Response $response, \Throwable $exception, \Illuminate\Http\Request $request) {
        if (! app()->environment(['local', 'testing']) && in_array($response->getStatusCode(), [500, 503, 404, 403])) {
            return \Inertia\Inertia::render("Errors/{$response->getStatusCode()}")
                ->toResponse($request)
                ->setStatusCode($response->getStatusCode());
        }

        if ($response->getStatusCode() === 419) {
            return back()->with('error', 'Session expired — please try again.');
        }

        return $response;
    });
})
```

Note: error pages only render in non-local/testing environments — tests still see actual exceptions for assertions.

- [ ] **Step 5: Commit**

```bash
git add resources/js/Pages/Errors bootstrap/app.php
git commit -m "feat(phase-0): friendly Inertia error pages (403/404/500)"
```

---

### Task 13: Build + smoke

**Files:**
- (No new files. Just verify everything wires.)

- [ ] **Step 1: Type-check**

```bash
npm run type-check
```

Expected: no errors.

- [ ] **Step 2: Production build**

```bash
npm run build
```

Expected: `public/build/manifest.json` generated, no errors.

- [ ] **Step 3: Run full backend test suite**

```bash
php artisan test
```

Expected: all tests pass (including pre-existing if any).

- [ ] **Step 4: Run full frontend test suite**

```bash
npm test
```

Expected: useAuth specs pass.

- [ ] **Step 5: Manual smoke**

Start dev:
```bash
php artisan serve & npm run dev &
```

In browser:
1. Visit `http://127.0.0.1:8000/login` → see shadcn-styled login card.
2. Submit `admin@eyetech.com` / `admin123` → redirect to `/app/dashboard`, see dashboard card.
3. Click "Sign out" → back to `/login`.
4. Log in as a cashier (create via tinker: `User::factory()->create(['email'=>'c@e.test','password'=>Hash::make('c'),'role'=>'cashier'])->syncRoles(['cashier'])`) → redirect to `/app/pos`.
5. Visit `/app/dashboard` as cashier → 403.
6. Visit `/admin/login` → Filament login still works.
7. Log in to Filament with `admin@eyetech.com` / `admin123` → Filament panel still functional.

If all pass, Phase 0 is complete.

Stop background processes:
```bash
jobs -p | xargs -r kill
```

- [ ] **Step 6: Commit any final tweaks discovered during smoke**

```bash
git add -A
git diff --cached --stat
git commit -m "chore(phase-0): smoke-test fixes" || echo "nothing to commit"
```

---

### Task 14: Phase 0 wrap-up

**Files:**
- Create: `docs/superpowers/plans/2026-05-23-eyetech-inertia-vue-phase-0-foundation.md` (this plan — already committed via `git status`)
- Modify: spec or plan if smoke surfaced gaps

- [ ] **Step 1: Verify spec is still accurate**

```bash
grep -E "(TODO|TBD)" docs/superpowers/specs/2026-05-23-eyetech-inertia-vue-design.md
```

Expected: no matches.

- [ ] **Step 2: Tag the milestone**

```bash
git tag phase-0-complete -m "Phase 0: Inertia + Vue + Sanctum + roles foundation"
```

- [ ] **Step 3: Hand off**

State explicitly:
- Phase 0 ships.
- Next: write Phase 1 plan (POS) — separate plan doc, separate execution session.
- Filament `/admin` still works; users can keep using it during Phase 1.

---

## Self-Review

**Spec coverage:**
- Stack additions (Inertia, Sanctum, Vue 3, TS, shadcn-vue, spatie/permission, Vitest): Tasks 1, 2, 3, 4 ✓
- Folder layout: covered across Tasks 4–11 ✓
- Auth flow + role-based redirect: Task 8 ✓
- Coexistence with Filament: Task 10 ✓
- `HandleInertiaRequests` shared props: Task 6 ✓
- Error pages: Task 12 ✓
- TypeScript model contract (User, Role): Task 5 ✓
- TDD for backend (controllers + policies): Tasks 8, 9, 10 ✓
- Frontend tests (Vitest + composables): Task 11 ✓
- shadcn-vue base components: Task 4 ✓
- POS/Dashboard/Reports/full resource pages: deferred to Phase 1+ — explicitly out of scope for Phase 0 ✓
- CI workflow: deferred (spec lists it but for later — noted for Phase 1 plan)

**Gap added:** CI workflow not in this plan. Spec says "Set up during Phase 0." Adding as Task 15.

**Placeholder scan:** None remain.

**Type consistency:** `User`, `Role`, `useAuth`, `useForm`, route names (`/app/dashboard`, `/app/pos`, `/login`, `/logout`), middleware aliases — all consistent across tasks.

---

### Task 15: CI workflow

**Files:**
- Create: `.github/workflows/ci.yml`

- [ ] **Step 1: Create GitHub Actions workflow**

```yaml
name: CI

on:
  push:
    branches: [ main, "feature/**" ]
  pull_request:

jobs:
  backend:
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v4
      - uses: shivammathur/setup-php@v2
        with:
          php-version: '8.4'
          coverage: none
      - run: composer install --prefer-dist --no-progress
      - run: cp .env.example .env || true
      - run: php artisan key:generate
      - run: php artisan test

  frontend:
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v4
      - uses: actions/setup-node@v4
        with:
          node-version: '22'
          cache: npm
      - run: npm ci
      - run: npm run type-check
      - run: npm test
      - run: npm run build
```

- [ ] **Step 2: Commit**

```bash
git add .github/workflows/ci.yml
git commit -m "ci(phase-0): GitHub Actions backend + frontend pipeline"
```

---

**Phase 0 complete.** Next session: write `2026-05-23-eyetech-inertia-vue-phase-1-pos.md` plan.
