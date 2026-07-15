# CE&P Duct Fabrication Ordering System — Implementation Plan

**Stack:** Laravel 12 + Spatie Permission + Filament (admin) + Blade + MySQL + Docker Compose
**Base:** Migrating `duct_size_calculator` (vanilla JS/Three.js) into a multi-user Laravel app
**Source of truth for formulas:** `duct.md` / `ducts.js` (ported server-side)

---

## 1. Goals

- Site engineers create duct orders (using the existing Three.js calculator UI).
- Project Manager / Site Manager can edit or approve/reject submitted orders.
- Workshop team sees only approved orders, fabricates from them, and marks item-level status.
- Workshop/office can generate a PDF cut-list report grouped by duct type.
- Admin manages users, sites, and the duct type catalog via Filament.
- Surface area is always recalculated server-side — never trusted from the client.

---

## 2. Roles (Spatie)

| Role | Description | Key permissions |
|---|---|---|
| `admin` | Office / you | all permissions |
| `engineer` | Site engineer, creates orders | `orders.create`, `orders.edit-own`, `orders.view-own` |
| `manager` | PM / Site Manager, approves orders | `orders.view-site`, `orders.edit-site`, `orders.approve` |
| `workshop` | Fabrication team | `orders.view-all`, `orders.fabricate`, `reports.generate` |

`position` (job title, e.g. "Site Engineer", "Draftsman") is a separate display-only column from `role` (access control).

---

## 3. Order Status Workflow

```
draft → submitted → approved → in_fabrication → completed
          ↑    ↓
          |  rejected → (engineer edits) → resubmitted → submitted
          |
   (manager may edit directly while submitted, then approve)
```

- **draft**: engineer is still building the list, editable only by owner.
- **submitted**: locked from engineer edits; visible to the site's Manager, who **can edit item details directly** (fix a typo'd dimension, adjust quantity) before approving — no need to bounce back to the engineer for small fixes.
- **approved**: Manager signed off; item dimensions/quantities frozen from this point (see §7).
- **rejected**: Manager sends back with a reason; engineer revises and **resubmits** (distinct action from the original `submit`, see §9).
- **in_fabrication**: Workshop has started; item-level status tracked (see §6).
- **completed**: all items delivered/fabricated; delivered quantities recorded (see §6).

---

## 4. Database Schema

### `users` (extend default Laravel table)
| Column | Type | Notes |
|---|---|---|
| p_id | string, unique | Employee/personnel ID |
| name | string | |
| email | string, unique | |
| password | string | hashed (bcrypt, Laravel default) |
| position | string, nullable | display title only |
| (role via Spatie `model_has_roles`) | | |

### `sites`
| Column | Type | Notes |
|---|---|---|
| id | | |
| name | string | |
| manager_id | FK → users, nullable | current PM/Site Manager |
| created_at / updated_at | | |

### `user_sites` (assignment history)
| Column | Type | Notes |
|---|---|---|
| id | | |
| user_id | FK → users | |
| site_id | FK → sites | |
| assigned_from | date, nullable | |
| assigned_to | date, nullable | null = currently active |

### `duct_types` (catalog, ported from `ducts.js`)
| Column | Type | Notes |
|---|---|---|
| id | | |
| name | string | e.g. "Rectangular Straight" |
| formula_key | string, unique | e.g. `rect_straight` |
| config | json | `{ "fields": ["A","B","L"] }` — drives validation |

### `orders`
| Column | Type | Notes |
|---|---|---|
| id | | |
| site_id | FK → sites | |
| created_by | FK → users | |
| approved_by | FK → users, nullable | |
| status | enum | draft/submitted/approved/rejected/in_fabrication/completed |
| rejection_reason | text, nullable | |
| revision_of | FK → orders, nullable, self | set when this order is a fabrication-time revision of a completed/in-progress one (see §8) |
| submitted_at / approved_at | timestamp, nullable | |
| created_at / updated_at | | |

### `order_items`
| Column | Type | Notes |
|---|---|---|
| id | | |
| order_id | FK → orders, cascade delete | |
| duct_type_id | FK → duct_types | |
| dimensions | json | raw inputs only, e.g. `{"A":600,"B":400,"L":3000}` |
| quantity | unsigned int | ordered quantity |
| quantity_delivered | unsigned int, default 0 | for partial-fulfillment tracking |
| surface_area | decimal(10,3) | **always server-computed**, never from client |
| fabrication_status | enum | `pending` / `in_progress` / `done` — item-level, independent of order-level status |

---

## 5. `dimensions` JSON Reference (by duct type)

**Corrected against the actual `ducts.js` `fields` arrays** (field IDs match exactly, so the Blade form and `DuctTypesSeeder` stay in sync with the original calculator):

| formula_key | fields |
|---|---|
| `rect_straight` | `A, B, L` |
| `round_straight` | `D, L` |
| `rect_elbow90` / `rect_elbow45` | `A, B, R` |
| `round_elbow90` / `round_elbow45` | `D, R` |
| `duct_reducer` | `A, B, C, D2, L` (only A, B, L feed the formula) |
| `rect_to_round` | `A, B, D, L` |
| `butterfly_round` | `A, B, D, L, R1, E, F, R2` |
| `butterfly_round_two` | `A, B, D1, L1, R1, D2, L2, R2` |
| `butterfly_rect` | `A, B, C, D2, R1, E, F, R2` |
| `collar_duct` | `A, B, C, D2, L` (only A, C feed the formula) |
| `offset_duct` | `A, B, C, D2, R, L` |
| `offset_duct_straight` | `A, B, R, L, L1, L2` |
| `offset_duct_angular` | `A, B, R, L, Rc, A1, A2` |
| `y_duct` | `A, B, E, F, C, D, R, L` |
| `r_type` | `A, B, C, D2, E, F, R, L` |
| `r_type_round_two` | `A, B, D1, L1, L2, D2, L3, R` |
| `plenum_box` | `A, B, H2, C, D, H1, D2, H3, F` |
| `plenum_top` | `A, B, H1, D, H2, F` |
| `plenum_tapered` | `A, B, H1, C, D, H2, CW, CD, CH, F` (C defaults to A if omitted) |
| `canvas_round` | `D, L, F` |
| `canvas_rect` | `A, B, L, F` |
| `fan_conn` | `A, B, C, D2, L, F1, S, L1, L2, Fb, Fi` |
| `wire_mesh` | `A, B, C, OL` |
| `transfer_air` | `W1, D1, H1, H2, W3, G, W4, H4, H3, W2, FL` (all default if omitted) |
| `4ways` | `A1, B1, A4, B4, A2, B2, A3, B3, R1, R2` |
| `angle_bar` / `angle_bar_u` | `L, HD, Dist, Size` (only L feeds the formula; result is linear metres, not m²) |

`duct_types.config.fields` (seeded exactly as above in `DuctTypesSeeder.php`) drives per-type validation in `StoreOrderItemRequest`.

---

## 6. Server-Side Area Calculation (Security Layer)

- **`app/Services/DuctAreaCalculator.php` is now a complete, exact port of every `area()` function in `ducts.js`** — all 28 formula keys, including the multi-part butterfly/offset/plenum/transfer-air/4-ways formulas and their default-value fallbacks (e.g. `transfer_air`'s `W1 || 900`, `plenum_tapered`'s `C || A`). See the delivered file.
- Controller **ignores any client-submitted area** and always recomputes via this service before saving.
- Once an order is `approved`, `dimensions`/`surface_area`/`quantity` on its items are frozen (enforced via policy — see §7).
- `fabrication_status` and `quantity_delivered` remain editable by Workshop after approval, since those track physical progress, not the order spec.
- `angle_bar` / `angle_bar_u` return **linear metres**, not m² — flag this in the report template (§10) so it doesn't get summed alongside area-based items.

---

## 7. Authorization

### Policies
- `OrderPolicy@edit` (engineer) — only `created_by === auth()->id()` AND `status ∈ [draft, rejected]`.
- `OrderPolicy@editAsManager` — `auth()->hasRole('manager')` AND `auth()->id() === order->site->manager_id` AND `status === 'submitted'`. Allows direct item edits without a reject round-trip.
- `OrderPolicy@approve` — same scope check AND `status === 'submitted'`.
- `OrderPolicy@fabricate` — role `workshop` AND `status ∈ [approved, in_fabrication]`.
- `OrderItemPolicy@updateFabricationStatus` — role `workshop` only, regardless of order-level lock, since this doesn't touch dimensions/quantity.

### Query scoping (in addition to route middleware/policies)
- Engineers: `Order::where('created_by', auth()->id())`
- Managers: `Order::where('site_id', auth()->user()->managedSite->id)`
- Workshop: `Order::whereIn('status', ['approved', 'in_fabrication'])` — no site restriction
- Admin: unrestricted

---

## 8. Mid-Fabrication Change Handling

Requirements change after an order is already `in_fabrication`. Editing the original order in place is risky — the workshop may have already cut material against the original dimensions. Recommended approach:

1. **Don't allow direct edits** to an `in_fabrication` order's items. `OrderPolicy@edit` and `@editAsManager` both exclude this status.
2. **Create a revision order** instead: engineer (or manager on their behalf) starts a new order with `revision_of` pointing at the original. Only the *changed* items go in the revision — not a full duplicate — so the workshop clearly sees "this is additional/replacement work," not a whole new job.
3. The revision goes through the **same approval flow** (submitted → approved) before workshop acts on it — a spec change still needs sign-off.
4. The original order stays `in_fabrication`/`completed` for whatever was already correct; the revision tracks the delta. The order `show` view links both directions (`revision_of` / `revisions()` relationship) so anyone looking at either sees the full history.
5. This gives you an audit trail of *what changed and when*, instead of silently mutating a record the workshop is actively working from.

---

## 9. Resubmit Flow (Rejected → Revised → Resubmitted)

Distinct from the initial `submit` action so the history is clear (you can tell "submitted twice" from "submitted once, no rejection").

```php
// routes/web.php (engineer group)
Route::post('orders/{order}/resubmit', [OrderController::class, 'resubmit']);
```

```php
// app/Http/Controllers/OrderController.php
public function resubmit(Order $order)
{
    $this->authorize('edit', $order); // created_by + status in [draft, rejected]
    abort_unless($order->status === 'rejected', 400, 'Only rejected orders can be resubmitted.');

    $order->update([
        'status' => 'submitted',
        'rejection_reason' => null,
        'submitted_at' => now(),
    ]);

    // notify Manager, same as initial submit
    return redirect()->route('orders.show', $order)->with('success', 'Order resubmitted for approval.');
}
```

The engineer edits items via the normal `OrderItemController@update` while status is `rejected` (allowed by `OrderPolicy@edit`), then hits **Resubmit** once satisfied — a single explicit action rather than resubmission happening implicitly on save.

---

## 10. Reports & Output (Workshop Cut-List PDF)

This is the actual deliverable the workshop uses to fabricate — the most important missing piece from earlier.

### Package
```bash
composer require barryvdh/laravel-dompdf
```

### Report structure
- Grouped **by duct type**, not by the order they were added in — a workshop fabricator wants "all rectangular elbows in one place," not "order #42's items."
- Each line: dimensions (formatted per type), quantity, surface area, and a checkbox/status column.
- Header: site name, order ID(s) included, date generated, approved-by.

```php
// app/Http/Controllers/ReportController.php
public function download(Order $order)
{
    $this->authorize('view', $order);

    $itemsByType = $order->items()
        ->with('ductType')
        ->get()
        ->groupBy(fn ($item) => $item->ductType->name);

    $pdf = Pdf::loadView('reports.cutlist', [
        'order' => $order,
        'itemsByType' => $itemsByType,
    ]);

    return $pdf->download("order-{$order->id}-cutlist.pdf");
}
```

```blade
{{-- resources/views/reports/cutlist.blade.php --}}
<h1>Duct Cut List — {{ $order->site->name }}</h1>
<p>Order #{{ $order->id }} · Approved by {{ $order->approver->name }} on {{ $order->approved_at->format('d M Y') }}</p>

@foreach ($itemsByType as $typeName => $items)
    <h3>{{ $typeName }}</h3>
    <table border="1" cellpadding="4">
        <tr><th>Dimensions</th><th>Qty</th><th>Delivered</th><th>Area (m²)</th><th>Status</th></tr>
        @foreach ($items as $item)
            <tr>
                <td>{{ collect($item->dimensions)->map(fn($v,$k)=>"$k: $v")->implode(', ') }}</td>
                <td>{{ $item->quantity }}</td>
                <td>{{ $item->quantity_delivered }}</td>
                <td>{{ number_format($item->surface_area, 3) }}</td>
                <td>{{ ucfirst(str_replace('_',' ', $item->fabrication_status)) }}</td>
            </tr>
        @endforeach
    </table>
@endforeach
```

### Status marking (Workshop UI, before generating final report)
```php
// routes (workshop group)
Route::patch('order-items/{orderItem}/status', [OrderItemController::class, 'updateFabricationStatus']);
```
```php
public function updateFabricationStatus(Request $request, OrderItem $orderItem)
{
    $this->authorize('updateFabricationStatus', $orderItem);
    $request->validate([
        'fabrication_status' => ['required', Rule::in(['pending', 'in_progress', 'done'])],
        'quantity_delivered' => ['nullable', 'integer', 'min:0', 'lte:' . $orderItem->quantity],
    ]);
    $orderItem->update($request->only('fabrication_status', 'quantity_delivered'));

    // if all items on the order are 'done' with full quantity_delivered, auto-flip order to 'completed'
    if ($orderItem->order->items()->where('fabrication_status', '!=', 'done')->doesntExist()) {
        $orderItem->order->update(['status' => 'completed']);
    }

    return back();
}
```

Workshop marks status inline in `workshop/queue.blade.php` (dropdown per item), then hits **Download PDF** to get the printable cut list any time — draft-in-progress or final.

---

## 11. Routes (Full)

```php
// Engineer
Route::middleware(['auth', 'role:engineer'])->group(function () {
    Route::resource('orders', OrderController::class)->only(['index','create','store','edit','update']);
    Route::post('orders/{order}/items', [OrderItemController::class, 'store']);
    Route::post('orders/{order}/submit', [OrderController::class, 'submit']);
    Route::post('orders/{order}/resubmit', [OrderController::class, 'resubmit']);
});

// Manager
Route::middleware(['auth', 'role:manager'])->group(function () {
    Route::get('manager/orders', [ManagerOrderController::class, 'index']);
    Route::patch('orders/{order}/items/{orderItem}', [OrderItemController::class, 'update']); // manager direct-edit
    Route::post('orders/{order}/approve', [OrderController::class, 'approve']);
    Route::post('orders/{order}/reject', [OrderController::class, 'reject']);
});

// Workshop
Route::middleware(['auth', 'role:workshop'])->group(function () {
    Route::get('workshop/orders', [WorkshopOrderController::class, 'index']);
    Route::post('orders/{order}/fabricate', [WorkshopOrderController::class, 'markInProgress']);
    Route::patch('order-items/{orderItem}/status', [OrderItemController::class, 'updateFabricationStatus']);
    Route::get('orders/{order}/report', [ReportController::class, 'download'])->name('orders.report');
});

// Admin (Filament handles most of this — see §13)
```

---

## 12. Blade View Structure — ✅ Delivered

All views below are included as working skeletons (Tailwind CDN, wired to the routes/policies in this plan). Controller wiring (`$order`, `$orders`, `$ductTypes`, `$itemsByType`) matches the variable names each view expects.

```
resources/views/
  layouts/app.blade.php        # role-aware nav via @role() directives, flash messages
  orders/
    index.blade.php            # engineer's order list, status badges
    create.blade.php           # duct type select + dynamic dimension fields (JS-driven from
                                # config.fields) + running item list + Three.js preview hook
    show.blade.php             # shared detail view; role-conditional actions (resubmit/
                                # approve+reject/fabricate) via @role and @can
  manager/pending.blade.php    # pending orders with inline item-edit + approve/reject
  workshop/queue.blade.php     # approved/in-fabrication orders, inline status + quantity_delivered
  reports/cutlist.blade.php    # dompdf template, grouped by duct type, see §10
```

**Still to do (not view-layer — these are backend pieces referenced by the views):**
- Port `viewer.js` itself into `public/js/viewer.js` and wire its render call where `create.blade.php` has the `TODO` comment — the view already loads it and has the hook point ready.
- `OrderController`, `OrderItemController`, `ManagerOrderController`, `WorkshopOrderController`, `ReportController` (signatures given in §9–§11) — the views assume these exist but the controllers themselves aren't scaffolded here.
- Auth/login views (`resources/views/auth/*`) — standard Laravel Breeze/Fortify scaffolding, not duct-specific, so left to a standard `laravel/breeze` install.

Client-side Three.js viewer stays for live preview only; server always recomputes area on submit.

---

## 13. Admin Panel (Filament)

```bash
composer require filament/filament
php artisan filament:install --panels
composer require bezhansalleh/filament-shield
php artisan shield:install
```

Resources: `UserResource`, `SiteResource`, `DuctTypeResource`, and a read-only `OrderResource` (list/view only — approvals happen in the manager-facing Blade flow, not here). Restrict panel access to `admin` role via a `Filament::serving()` gate check.

**Status: designed, not yet generated as files.** None of the four resources above have been scaffolded yet. `UserResource` in particular (user management — create/edit users with `p_id`, name, email, `position`, role assignment via `roles` relationship, site assignment via `user_sites`, and hashed password field) still needs to be built with `php artisan make:filament-resource`. See the earlier discussion in this conversation for the intended `UserResource` form field config as a starting point.

---

## 14. Notifications

Reuse existing Telegram bot pattern (from CENP backup script):
- Order `submitted`/`resubmitted` → notify site's Manager.
- Order `approved` → notify Workshop.
- Order `rejected` → notify the engineer who created it.
- Order `completed` → notify the engineer (their order is fully delivered).

---

## 15. Testing Plan

### Unit tests — `DuctAreaCalculator` (highest priority)
This is the security-critical logic; every formula needs a regression test against a known-good value pulled from the existing `ducts.js` output, **before** go-live.

```php
// tests/Unit/DuctAreaCalculatorTest.php
class DuctAreaCalculatorTest extends TestCase
{
    private DuctAreaCalculator $calc;

    protected function setUp(): void
    {
        parent::setUp();
        $this->calc = new DuctAreaCalculator();
    }

    /** @dataProvider ductFixtures */
    public function test_calculates_expected_area(string $formulaKey, array $dimensions, float $expected)
    {
        $this->assertEqualsWithDelta($expected, $this->calc->calculate($formulaKey, $dimensions), 0.001);
    }

    public static function ductFixtures(): array
    {
        return [
            'rect_straight' => ['rect_straight', ['A' => 600, 'B' => 400, 'L' => 3000], 6.0],
            'round_straight' => ['round_straight', ['D' => 300, 'L' => 2000], 1.885],
            // ...one row per formula_key, values captured from the existing JS calculator's output
        ];
    }
}
```
Generate the expected values by running each duct type through the existing `ducts.js` in the browser with the same test inputs, and hardcoding those outputs as fixtures — this guarantees the Laravel port matches the original exactly.

### Feature tests — workflow & authorization
```php
// tests/Feature/OrderWorkflowTest.php
test('engineer cannot edit a submitted order');
test('engineer cannot approve their own order');
test('manager can edit items on a submitted order at their site');
test('manager cannot approve an order from a different site');
test('manager cannot edit an order that is already approved');
test('workshop cannot see draft or submitted orders');
test('rejected order can be resubmitted only by its creator');
test('order auto-completes when all items marked done with full quantity_delivered');
test('client-submitted surface_area is ignored and recalculated server-side'); // critical
```

### Manual QA checklist (pre-launch)
- [ ] All 25 duct type formulas verified against `ducts.js` fixtures.
- [ ] PDF report renders correctly for an order with mixed duct types.
- [ ] Revision order (§8) correctly links to and displays alongside the original.
- [ ] Telegram notifications fire at each status transition.

---

## 16. Seed Data

```php
// database/seeders/DatabaseSeeder.php
public function run(): void
{
    $this->call([
        RolesAndPermissionsSeeder::class,
        DuctTypesSeeder::class,
    ]);

    $site = Site::create(['name' => 'Example Site A']);

    $pm = User::create([
        'p_id' => 'PM001',
        'name' => 'Example Project Manager',
        'email' => 'pm@example.com',
        'password' => Hash::make('changeme'),
        'position' => 'Project Manager',
    ]);
    $pm->assignRole('manager');
    $site->update(['manager_id' => $pm->id]);

    UserSite::create([
        'user_id' => $pm->id,
        'site_id' => $site->id,
        'assigned_from' => now(),
    ]);
}
```
One example Site + one Manager, so there's a working record to test the engineer→submit→manager-approve flow against immediately after deploy. Add real users/sites afterward via the Filament admin panel.

---

## 17. Security Checklist

- [ ] Passwords hashed via Laravel default (bcrypt/argon2) — no plaintext, no reused MySQL exposure mistake.
- [ ] `surface_area` never accepted from client input — always `DuctAreaCalculator` output.
- [ ] Per-type dimension validation via `duct_types.config.fields`.
- [ ] Policies enforce edit-only-while-draft/rejected (engineer), submitted-only (manager), and site-scoped approval.
- [ ] Eloquent query scoping in addition to route middleware (defense in depth).
- [ ] `.env` not committed; DB port not publicly exposed (UFW: 22 + 8080 only, per existing CENP hardening pattern).
- [ ] Daily DB backup script (reuse CENP's Telegram-notified backup) applied to this app too.

---

## 18. Build Order (Suggested Milestones)

1. **Scaffold**: Laravel 12 install, Docker Compose (reuse CENP pattern), Spatie install + seeders (roles, permissions, duct_types, example site+PM).
2. **Admin panel**: Filament install + Shield + `UserResource`/`SiteResource`/`DuctTypeResource`.
3. **Order creation**: Port `index.html`/`app.js`/`viewer.js` into `orders/create.blade.php`; wire to `OrderItemController@store` with server-side recalculation.
4. **Submit → Manager review → Approve/Reject/Resubmit flow**: `ManagerOrderController`, policies, notifications.
5. **Workshop queue + status tracking**: `WorkshopOrderController`, `fabrication_status`/`quantity_delivered` updates, auto-complete logic.
6. **Reports**: dompdf cut-list, grouped by duct type.
7. **Mid-fabrication revisions**: `revision_of` linkage, revision creation UI.
8. **Testing**: `DuctAreaCalculatorTest` fixtures (all 25 formulas), feature tests for workflow/authorization.
9. **Hardening & deployment**: UFW rules, `.env` review, backup script, Docker Compose + DEPLOYMENT.md (write after app is functionally complete, per your plan).

---

## 19. Open Items / Future Enhancements

- Move duct type formulas fully into DB-configurable params if formulas ever need tweaking without redeploy (not urgent now).
- File attachments (site photos, spec PDFs) per order.
- Multi-site managers (if one PM oversees more than one site).
- Offline-friendly entry for site engineers with poor connectivity.