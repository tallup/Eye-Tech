# EyeTech Inertia + Vue — Phase 4: Filament Removal

> **For agentic workers:** REQUIRED SUB-SKILL: superpowers:subagent-driven-development to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking. **Do not start Phase 4 until Phase 3 (`feature/inertia-vue-phase-3`) is merged to `main` and tagged `phase-3-complete`.** Phase 3 must deliver Vue equivalents for Users, Services, ServiceRequests, Profile, Reports, and CustomReports — otherwise the brother loses admin capability when Filament dies.

**Goal:** Strip every trace of Filament from the codebase. `/admin` returns 404, `composer.json` no longer requires `filament/filament`, `app/Filament/` is deleted, and the new Inertia/Vue app at `/app/*` is the sole admin surface.

**Architecture:** Pure deletion + small refactors. No new features. One controller (`CustomReportsController`) replaces the one place a Filament Page was instantiated from a web route. User model loses the Filament contract. Bootstrap drops `AdminPanelProvider`. Public Filament assets are deleted. Coexistence test is removed (its premise is gone).

**Tech Stack:** No new packages. Removes: `filament/filament ^4.0`. Removes composer post-autoload script `@php artisan filament:upgrade`.

**Spec:** [`docs/superpowers/specs/2026-05-23-eyetech-inertia-vue-design.md`](../specs/2026-05-23-eyetech-inertia-vue-design.md) (Phase 4 row at line 69; risk at line 480; DoD at lines 494/497)
**Prior plans:** Phase 0 (`phase-0-complete`), Phase 1 (`phase-1-complete`), Phase 2 (`phase-2-complete`), Phase 3 (`phase-3-complete` — required precondition)

---

## Scope of Phase 4

| Target | Action | Notes |
|---|---|---|
| `app/Filament/` (entire tree) | Delete | 8 Resource clusters + 4 Pages + 2 Widgets — total ~54 PHP files |
| `app/Providers/Filament/AdminPanelProvider.php` | Delete | Sole provider, registers `/admin` panel |
| `bootstrap/providers.php` | Edit | Drop `App\Providers\Filament\AdminPanelProvider::class` line |
| `composer.json` | Edit | Remove `filament/filament` from `require`; remove `@php artisan filament:upgrade` from post-autoload-dump |
| `composer.lock` + `vendor/filament/` | Regenerate | `composer update --lock` after composer.json edit |
| `app/Models/User.php` | Edit | Drop `implements FilamentUser`, drop `use ...FilamentUser` import, drop `canAccessPanel()` method |
| `routes/web.php` | Edit | Replace `Filament\Pages\CustomReports` instantiation with `CustomReportsController@index` |
| `app/Http/Controllers/CustomReportsController.php` | Create | Pure Laravel controller carrying the data-fetch logic previously in the Filament Page |
| `tests/Feature/CoexistenceTest.php` | Delete | Asserts `/admin` reachable — premise dead |
| `public/css/filament/` | Delete | Compiled Filament styles |
| `public/js/filament/` | Delete | Compiled Filament scripts + echo.js |
| `public/fonts/filament/` | Delete | Filament icon fonts |
| `storage/framework/views/` | Clear | `php artisan view:clear` to evict any cached Filament Blade |
| All Filament Resource Vue equivalents | Verify exist | Pre-flight check (see Task 1) — abort Phase 4 if any are missing |

Out of scope (Phase 5+): Public marketing site Inertia port (`/`, `/about`, `/services`, `/contact`).

---

## File Structure

### New backend files

| Path | Responsibility |
|---|---|
| `app/Http/Controllers/CustomReportsController.php` | Returns the Inertia (or Blade, see Task 3) response for `/custom-reports`, carrying the report data that the old Filament Page produced. |

### New frontend files

| Path | Responsibility |
|---|---|
| `resources/js/Pages/Reports/CustomReports.vue` *(only if not already created in Phase 3)* | Inertia page for `/custom-reports`. Skip if Phase 3 already shipped this. |

### Modified files

| Path | Change |
|---|---|
| `app/Models/User.php` | Drop `FilamentUser` interface + `canAccessPanel()` method + import. |
| `bootstrap/providers.php` | Remove `AdminPanelProvider` entry. |
| `composer.json` | Remove `filament/filament` from `require`; remove `@php artisan filament:upgrade` from `scripts.post-autoload-dump`. |
| `routes/web.php` | Line 28 — swap Filament Page call for `CustomReportsController`. |
| `resources/js/Layouts/AppLayout.vue` *(if it links to `/admin`)* | Remove any `/admin` link. (Recon shows Phase 2 nav already targets `/app/*` — verify in Task 1.) |

### Deleted files / dirs

| Path | Reason |
|---|---|
| `app/Filament/` (recursive) | The whole panel. |
| `app/Providers/Filament/` (recursive) | Provider lives nowhere else. |
| `tests/Feature/CoexistenceTest.php` | Tests the thing we are removing. |
| `public/css/filament/` | Vendor-published asset. |
| `public/js/filament/` | Vendor-published asset. |
| `public/fonts/filament/` | Vendor-published asset. |
| `vendor/filament/` | Will be removed by `composer update`. |

### New test files

| Path | Responsibility |
|---|---|
| `tests/Feature/FilamentRemovedTest.php` | Asserts `/admin` returns 404, `/admin/login` returns 404, `class_exists('Filament\\Panel')` is `false`. Guards against accidental re-introduction. |
| `tests/Feature/CustomReportsTest.php` *(if not added in Phase 3)* | Smoke test for `/custom-reports` via the new controller. |

---

## Tasks

### Task 1: Pre-flight verification — confirm Phase 3 coverage

Before touching anything, prove the brother won't lose features. This is a read-only audit; no commit.

- [ ] **Step 1:** Confirm Phase 3 merged.
  ```bash
  cd /home/taal/Documents/eyetech-system
  git log --oneline | grep -i "phase-3-complete\|feat(phase-3)" | head -20
  git tag | grep phase-3
  ```
  If no `phase-3-complete` tag and no `feat(phase-3):` commits on the current branch — **STOP**. Phase 3 not done. Do not proceed.

- [ ] **Step 2:** Enumerate every Filament Resource and confirm a Vue equivalent exists at `/app/<resource>`.
  ```bash
  # Filament resources present
  ls app/Filament/Resources/
  # Vue pages present
  find resources/js/Pages -maxdepth 2 -type d | sort
  # Routes registered
  php artisan route:list --columns=method,uri,name | grep -E '^(GET|POST)\s+/?app/'
  ```
  Required mapping (every left must have a right):
  | Filament Resource | Required Vue route |
  |---|---|
  | `Categories` | `/app/categories` |
  | `Products` | `/app/products` |
  | `Suppliers` | `/app/suppliers` |
  | `Sales` | `/app/sales` |
  | `StockMovements` | `/app/stock-movements` |
  | `Services` | `/app/services` |
  | `ServiceRequests` | `/app/service-requests` |
  | `Users` | `/app/users` |
  | `Pages/Dashboard` | `/app/dashboard` |
  | `Pages/Profile` | `/app/profile` |
  | `Pages/Reports` | `/app/reports` |
  | `Pages/CustomReports` | `/app/reports/custom` or `/custom-reports` (Task 3) |

  If any row's right side does not exist — **STOP** and file the gap back to Phase 3.

- [ ] **Step 3:** Manual browser smoke against the running app at `/app/*` for each resource above. Login as admin, create/edit/delete one record per resource. Confirm no `/admin` link remains in `AppLayout.vue`. Record any gaps; do not proceed until clean.

- [ ] **Step 4:** Create a safety branch off Phase 3.
  ```bash
  git checkout main
  git pull
  git checkout -b feature/inertia-vue-phase-4
  ```

No commit this task — pre-flight only.

---

### Task 2: Refactor `User` model — drop Filament contract

Recon found `app/Models/User.php` implements `\Filament\Models\Contracts\FilamentUser` (line 11) and defines `canAccessPanel()` (line 56). Both must go.

- [ ] **Step 1:** Read `app/Models/User.php` to confirm current shape.

- [ ] **Step 2:** Write a regression test that User still loads cleanly without the Filament import.
  ```bash
  php artisan make:test Unit/UserModelNoFilamentTest --unit
  ```
  Test body:
  ```php
  it('loads User model without referencing Filament', function () {
      $user = \App\Models\User::factory()->make();
      expect($user)->toBeInstanceOf(\App\Models\User::class);
      expect(class_implements($user))->not->toContain('Filament\\Models\\Contracts\\FilamentUser');
  });
  ```
  Run — expect failure (still implements it).

- [ ] **Step 3:** Edit `app/Models/User.php`:
  - Remove `use Filament\Models\Contracts\FilamentUser;` (line 11 area).
  - Remove `implements FilamentUser` from class declaration.
  - Remove `public function canAccessPanel(...)` method (around line 56).
  - Remove any `use Filament\Panel;` import.
  - **Use `Write`, not `Edit`** — Pint will strip unused imports on `Edit` and may corrupt the file (see memory `eyetech/phase-2-task3-products-crud`).

- [ ] **Step 4:** Run the test — expect green.
  ```bash
  ./vendor/bin/pest tests/Unit/UserModelNoFilamentTest.php
  ```

- [ ] **Step 5:** Run the full suite — expect green except for tests that reference Filament (those die in later tasks).
  ```bash
  ./vendor/bin/pest --exclude-group=filament 2>/dev/null || ./vendor/bin/pest
  ```
  Note expected failures and proceed; we will delete `CoexistenceTest` in Task 6.

- [ ] **Step 6:** Commit.
  ```bash
  git add app/Models/User.php tests/Unit/UserModelNoFilamentTest.php
  git commit -m "refactor(phase-4): drop FilamentUser contract from User model"
  ```

---

### Task 3: Port `CustomReports` to a plain controller

`routes/web.php:28` does something like `Route::get('/custom-reports', fn () => (new \App\Filament\Pages\CustomReports)->...)`. This is the only place a Filament Page is invoked from a non-`/admin` route, so it must move into a real controller before `app/Filament/` is deleted.

- [ ] **Step 1:** Read `app/Filament/Pages/CustomReports.php` and `routes/web.php:28` to capture the exact data the page produces and which view/template it renders.

- [ ] **Step 2:** Decide the target. If Phase 3 already shipped `resources/js/Pages/Reports/CustomReports.vue`, the controller returns Inertia. If not (and Phase 3 chose to leave this as a server-rendered report), copy the existing Blade template into `resources/views/reports/custom.blade.php` and have the controller `return view(...)`.

- [ ] **Step 3:** Create `app/Http/Controllers/CustomReportsController.php`:
  ```php
  <?php

  namespace App\Http\Controllers;

  use Illuminate\Http\Request;

  class CustomReportsController extends Controller
  {
      public function index(Request $request)
      {
          // Port the data-fetch logic verbatim from the Filament Page's mount()/getViewData().
          $data = [/* ... */];

          // Inertia variant (preferred if Phase 3 supplies the Vue page):
          return inertia('Reports/CustomReports', $data);

          // OR Blade variant:
          // return view('reports.custom', $data);
      }
  }
  ```

- [ ] **Step 4:** Write a feature test that hits `/custom-reports` as an admin and asserts 200 + expected payload keys.
  ```bash
  php artisan make:test Feature/CustomReportsTest
  ```
  ```php
  it('renders custom reports for admin', function () {
      $admin = \App\Models\User::factory()->create(['role' => 'admin'])->assignRole('admin');
      $this->actingAs($admin)->withoutVite()->get('/custom-reports')
          ->assertOk();
  });
  ```

- [ ] **Step 5:** Update `routes/web.php`:
  ```php
  // Replace the Filament Page line with:
  Route::middleware(['auth', 'role:admin'])
      ->get('/custom-reports', [\App\Http\Controllers\CustomReportsController::class, 'index'])
      ->name('reports.custom');
  ```

- [ ] **Step 6:** Run the test — expect green. Then `php artisan route:list | grep custom-reports` to confirm the route binds the new controller.

- [ ] **Step 7:** Commit.
  ```bash
  git add app/Http/Controllers/CustomReportsController.php routes/web.php tests/Feature/CustomReportsTest.php resources/views/reports/custom.blade.php 2>/dev/null
  git commit -m "refactor(phase-4): port CustomReports from Filament Page to plain controller"
  ```

---

### Task 4: Drop `AdminPanelProvider` from bootstrap

With Users decoupled and CustomReports refactored, nothing in app code requires the panel provider.

- [ ] **Step 1:** Read `bootstrap/providers.php` to locate the entry.

- [ ] **Step 2:** Remove the line:
  ```php
  App\Providers\Filament\AdminPanelProvider::class,
  ```

- [ ] **Step 3:** Clear caches and confirm boot still works.
  ```bash
  php artisan config:clear && php artisan route:clear && php artisan view:clear
  php artisan route:list > /tmp/routes.txt
  # /admin routes should still appear here — they come from the package itself via its own provider.
  # That's fine; the provider gets unregistered in Task 5 when the package is removed.
  ```

- [ ] **Step 4:** Run the suite — expect green (except `CoexistenceTest`, deleted next task).

- [ ] **Step 5:** Commit.
  ```bash
  git add bootstrap/providers.php
  git commit -m "chore(phase-4): unregister AdminPanelProvider"
  ```

---

### Task 5: Remove `filament/filament` from composer and vendor tree

This is the big rip. After this, `Filament\` symbols stop existing.

- [ ] **Step 1:** Edit `composer.json`:
  - In `require`, delete the line `"filament/filament": "^4.0"`.
  - In `scripts.post-autoload-dump`, delete `"@php artisan filament:upgrade"`.

- [ ] **Step 2:** Update lockfile and vendor.
  ```bash
  composer update --no-interaction --no-progress
  # Confirm filament is gone:
  test ! -d vendor/filament && echo "OK: vendor/filament removed" || echo "FAIL: still present"
  composer show | grep -i filament && echo "FAIL" || echo "OK: composer no longer knows filament"
  ```

- [ ] **Step 3:** App will now fail to boot because `app/Filament/` files reference removed classes. That's expected — delete them in this same task before running anything.
  ```bash
  rm -rf app/Filament app/Providers/Filament
  ```

- [ ] **Step 4:** Boot smoke.
  ```bash
  php artisan config:clear && php artisan route:clear && php artisan view:clear
  php artisan route:list | grep -i "/admin\|filament" && echo "FAIL: /admin routes remain" || echo "OK: no /admin routes"
  php artisan about | head -40
  ```

- [ ] **Step 5:** Run the suite — `CoexistenceTest` will now error out (class missing). That is what we want to delete next; this red is expected.

- [ ] **Step 6:** Commit (one atomic rip — composer + app/Filament together so HEAD is never broken).
  ```bash
  git add composer.json composer.lock app/Filament app/Providers/Filament
  git commit -m "chore(phase-4): drop filament/filament package and delete app/Filament tree"
  ```

---

### Task 6: Delete `CoexistenceTest` and the new FilamentRemovedTest guard

- [ ] **Step 1:** Delete the now-broken coexistence test.
  ```bash
  rm tests/Feature/CoexistenceTest.php
  ```

- [ ] **Step 2:** Create `tests/Feature/FilamentRemovedTest.php` as a regression guard so nobody can re-add Filament accidentally.
  ```php
  <?php

  use function Pest\Laravel\get;

  it('returns 404 for /admin', function () {
      get('/admin')->assertNotFound();
  });

  it('returns 404 for /admin/login', function () {
      get('/admin/login')->assertNotFound();
  });

  it('has no Filament classes loaded', function () {
      expect(class_exists('Filament\\Panel'))->toBeFalse();
      expect(class_exists('Filament\\FilamentManager'))->toBeFalse();
  });

  it('composer.json does not list filament', function () {
      $composer = json_decode(file_get_contents(base_path('composer.json')), true);
      expect($composer['require'] ?? [])->not->toHaveKey('filament/filament');
  });
  ```

- [ ] **Step 3:** Run the suite — expect fully green.
  ```bash
  ./vendor/bin/pest
  ```
  Quote the actual `Tests:` summary line. No success claim without the count.

- [ ] **Step 4:** Commit.
  ```bash
  git add tests/Feature/CoexistenceTest.php tests/Feature/FilamentRemovedTest.php
  git commit -m "test(phase-4): replace CoexistenceTest with FilamentRemovedTest guard"
  ```

---

### Task 7: Delete public Filament assets

- [ ] **Step 1:** Remove vendor-published static assets.
  ```bash
  rm -rf public/css/filament public/js/filament public/fonts/filament
  ```

- [ ] **Step 2:** Confirm nothing in Blade or Vue references those paths.
  ```bash
  grep -rE "css/filament|js/filament|fonts/filament" resources/ public/build/ 2>/dev/null
  # Expect: empty.
  ```

- [ ] **Step 3:** Rebuild frontend to ensure manifest is clean.
  ```bash
  npm run build
  ```

- [ ] **Step 4:** Commit.
  ```bash
  git add public/
  git commit -m "chore(phase-4): drop vendor-published Filament static assets"
  ```

---

### Task 8: Final verification — full smoke + tag

Per user doctrine: **no success claims without evidence.** Quote command output verbatim.

- [ ] **Step 1:** Backend suite.
  ```bash
  ./vendor/bin/pest
  ```
  Quote the `Tests:` line.

- [ ] **Step 2:** Static checks.
  ```bash
  ./vendor/bin/pint --test
  ./vendor/bin/phpstan analyse 2>/dev/null || echo "phpstan not configured — skip"
  ```

- [ ] **Step 3:** Frontend build + typecheck.
  ```bash
  npm run build
  npx vue-tsc --noEmit 2>&1 | tail -5
  ```

- [ ] **Step 4:** Repo-wide Filament reference scan — must be empty.
  ```bash
  grep -rE "Filament\\\\|filament/filament|app/Filament|/admin" \
    --include="*.php" --include="*.vue" --include="*.ts" --include="*.json" \
    --exclude-dir=node_modules --exclude-dir=vendor --exclude-dir=storage \
    --exclude-dir=public/build .
  ```
  Expect zero hits. Any hit = unfinished work; fix before tagging.

- [ ] **Step 5:** Manual browser smoke.
  ```bash
  php artisan serve &
  ```
  Walk through as admin: login → dashboard → POS checkout → sales receipt → categories CRUD → products CRUD → suppliers CRUD → stock movements → services → service requests → users → reports → custom reports → profile → logout. Then attempt `GET /admin` — must 404.

- [ ] **Step 6:** Tag and push.
  ```bash
  git tag phase-4-complete -m "Phase 4: Filament removed; /app is sole admin surface"
  git push origin feature/inertia-vue-phase-4 --tags
  ```

- [ ] **Step 7:** Persist findings to ruflo memory.
  ```
  mcp__ruflo__memory_store
    key=eyetech/phase-4-complete
    namespace=eyetech
    tags=[eyetech, phase-4, done]
    value="Filament gone. composer no longer requires filament/filament. app/Filament/ deleted. /admin 404s. FilamentRemovedTest guards regression. Tag: phase-4-complete. SHA: <commit>."
  ```

---

## Self-Review

**Spec coverage (Phase 4 DoD from design.md lines 494/497):**
- "All Filament Resources have working Vue equivalents at `/app/*`." ✓ verified pre-flight (Task 1)
- "`app/Filament/` directory removed." ✓ (Task 5)
- "`composer.json` no longer lists `filament/filament`." ✓ (Task 5, asserted by Task 6 test)

**Deferred to Phase 5:**
- Public marketing site Inertia port

**Placeholder scan:** none — every task ends with a real commit.

**Type consistency:** N/A — Phase 4 is removal, not new types.

**Risk register:**

| Risk | Mitigation |
|---|---|
| Phase 3 incomplete; admin loses Users/Services/etc. when Filament dies | Task 1 hard-stop pre-flight checks the resource map row-by-row and aborts on any gap. |
| `CustomReports` Filament Page used logic that doesn't trivially port | Task 3 reads the Page source first and either inlines into the new controller (Inertia) or copies its Blade verbatim. |
| `User` model's `FilamentUser` contract had logic referenced elsewhere | Recon shows `canAccessPanel()` is the only Filament method; not called from any non-Filament code. Task 2 regression test guards. |
| Hidden silent dependency (per spec line 480 — notifications, file uploads) | Task 8 step 4 does a repo-wide `Filament\\` grep that must return empty before tagging. |
| `composer update` pulls unintended upgrades on other packages | Run `composer update filament/filament --with-all-dependencies` first to see the targeted removal, or commit `composer.lock` separately if drift appears. |
| Cached Blade views still reference Filament classes after deletion | `php artisan view:clear` in Task 4 step 3 and Task 8 step 1. |

**Rollback:** This is a feature branch (`feature/inertia-vue-phase-4`). If any acceptance fails, `git checkout main && git branch -D feature/inertia-vue-phase-4`. No DB schema changes mean no migration rollback needed.

---

**End of Phase 4 Plan.** Next: Phase 5 — port the public marketing site (`/`, `/about`, `/services`, `/contact`) from current Blade to Inertia + Vue under `PublicLayout.vue`.
