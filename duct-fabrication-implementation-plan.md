# CE&P Duct Fabrication Ordering System — Implementation Plan

**Stack:** Laravel 12 + Spatie Permission + Blade + MySQL + Docker Compose
**Base:** Migrating `duct_size_calculator` (vanilla JS/Three.js) into a multi-user Laravel app
**Source of truth for formulas:** `duct.md` / `ducts.js` (ported server-side)

---

## 1. Goals

- Site engineers create duct orders (using the existing Three.js calculator UI).
- Project Manager / Site Manager approves or rejects submitted orders.
- Workshop team sees only approved orders and fabricates from them.
- Admin manages users, sites, and the duct type catalog.
- Surface area is always recalculated server-side — never trusted from the client.

---

## 2. Roles (Spatie)

| Role | Description | Key permissions |
|---|---|---|
| `admin` | Office / you | all permissions |
| `engineer` | Site engineer, creates orders | `orders.create`, `orders.edit-own`, `orders.view-own` |
| `manager` | PM / Site Manager, approves orders | `orders.view-site`, `orders.approve` |
| `workshop` | Fabrication team | `orders.view-all`, `orders.fabricate` |

`position` (job title, e.g. "Site Engineer", "Draftsman") is a separate display-only column from `role` (access control).

---

## 3. Order Status Workflow

```
draft → submitted → approved → in_fabrication → completed
                  ↘ rejected → (engineer edits) → submitted
```

- **draft**: engineer is still building the list, editable only by owner.
- **submitted**: locked from further edits by engineer, visible to their site's Manager.
- **approved**: Manager signed off; item dimensions/quantities frozen from this point.
- **rejected**: Manager sends back with a reason; engineer can revise and resubmit.
- **in_fabrication / completed**: Workshop-managed lifecycle after approval.

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
| submitted_at / approved_at | timestamp, nullable | |
| created_at / updated_at | | |

### `order_items`
| Column | Type | Notes |
|---|---|---|
| id | | |
| order_id | FK → orders, cascade delete | |
| duct_type_id | FK → duct_types | |
| dimensions | json | raw inputs only, e.g. `{"A":600,"B":400,"L":3000}` |
| quantity | unsigned int | |
| surface_area | decimal(10,3) | **always server-computed**, never from client |

---

## 5. `dimensions` JSON Reference (by duct type)

See prior discussion — one shape per `formula_key`, e.g.:

- `rect_straight`: `{A, B, L}`
- `round_straight`: `{D, L}`
- `rect_elbow90` / `rect_elbow45`: `{A, B, R}`
- `round_elbow90` / `round_elbow45`: `{D, R}`
- `duct_reducer`: `{A, B, C, D, L}`
- `rect_to_round`: `{A, B, D, L}`
- `butterfly_round` / `butterfly_round_two` / `butterfly_rect`: `{A, B, R1, R2, L}`
- `collar_duct`: `{A, B, C, D, L}`
- `offset_duct` / `offset_duct_straight`: `{A, B, C, D2, R, L}`
- `offset_duct_angular`: `{A, B, R, angle, L}`
- `y_duct`: `{A, B, R, L, branch_A, branch_B}`
- `r_type` / `r_type_round_two`: `{A, B, R, L, branch_D}`
- `plenum_box` / `plenum_top` / `plenum_tapered`: `{W, H, Depth, necks: [...]}`
- `canvas_round`: `{D, L}` / `canvas_rect`: `{A, B, L}`
- `fan_conn`: `{A, B, D, L}`
- `wire_mesh`: `{A, B}`
- `transfer_air`: `{A, B, L, offset}`
- `4ways`: `{A, B, R, branches}`
- `angle_bar` / `angle_bar_u`: `{L}`

`duct_types.config.fields` drives per-type validation in `StoreOrderItemRequest`.

---

## 6. Server-Side Area Calculation (Security Layer)

- `app/Services/DuctAreaCalculator.php` — one method per `formula_key`, ported 1:1 from `ducts.js` math in `duct.md`.
- Controller **ignores any client-submitted area** and always recomputes via this service before saving.
- Once an order is `approved`, item dimensions/area are frozen (enforced via policy, not just convention).

---

## 7. Authorization

### Policies
- `OrderPolicy@edit` — only `created_by === auth()->id()` AND `status === 'draft'`.
- `OrderPolicy@approve` — `auth()->can('orders.approve')` AND `auth()->id() === order->site->manager_id` AND `status === 'submitted'`.
- `OrderPolicy@fabricate` — role `workshop` AND `status === 'approved'`.

### Query scoping (in addition to route middleware/policies)
- Engineers: `Order::where('created_by', auth()->id())`
- Managers: `Order::where('site_id', auth()->user()->managedSite->id)`
- Workshop: `Order::where('status', 'approved')` — no site restriction
- Admin: unrestricted

---

## 8. Routes

```php
// Engineer
Route::middleware(['auth', 'role:engineer'])->group(function () {
    Route::resource('orders', OrderController::class)->only(['index','create','store','edit','update']);
    Route::post('orders/{order}/items', [OrderItemController::class, 'store']);
    Route::post('orders/{order}/submit', [OrderController::class, 'submit']);
});

// Manager
Route::middleware(['auth', 'role:manager'])->group(function () {
    Route::get('manager/orders', [ManagerOrderController::class, 'index']);
    Route::post('orders/{order}/approve', [OrderController::class, 'approve']);
    Route::post('orders/{order}/reject', [OrderController::class, 'reject']);
});

// Workshop
Route::middleware(['auth', 'role:workshop'])->group(function () {
    Route::get('workshop/orders', [WorkshopOrderController::class, 'index']);
    Route::post('orders/{order}/fabricate', [WorkshopOrderController::class, 'markInProgress']);
    Route::post('orders/{order}/complete', [WorkshopOrderController::class, 'complete']);
});

// Admin
Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::resource('users', UserController::class);
    Route::resource('sites', SiteController::class);
    Route::resource('duct-types', DuctTypeController::class);
});
```

---

## 9. Blade View Structure

```
resources/views/
  layouts/app.blade.php        # role-aware nav via @role()/@can()
  orders/
    index.blade.php
    create.blade.php           # port app.js + viewer.js form/3D preview here
    show.blade.php
  manager/pending.blade.php
  workshop/queue.blade.php
  admin/
    users/...
    sites/...
    duct-types/...
```

Client-side Three.js viewer stays for live preview only; server always recomputes area on submit.

---

## 10. Notifications

Reuse existing Telegram bot pattern (from CENP backup script):
- Order `submitted` → notify site's Manager.
- Order `approved` → notify Workshop.
- Order `rejected` → notify the engineer who created it.

---

## 11. Security Checklist

- [ ] Passwords hashed via Laravel default (bcrypt/argon2) — no plaintext, no reused MySQL exposure mistake.
- [ ] `surface_area` never accepted from client input — always `DuctAreaCalculator` output.
- [ ] Per-type dimension validation via `duct_types.config.fields`.
- [ ] Policies enforce edit-only-while-draft and site-scoped approval.
- [ ] Eloquent query scoping in addition to route middleware (defense in depth).
- [ ] `.env` not committed; DB port not publicly exposed (UFW: 22 + 8080 only, per existing CENP hardening pattern).
- [ ] Daily DB backup script (reuse CENP's Telegram-notified backup) applied to this app too.

---

## 12. Build Order (Suggested Milestones)

1. **Scaffold**: Laravel 12 install, Docker Compose (reuse CENP pattern), Spatie install + seeders (roles, permissions, duct_types).
2. **Auth & Admin**: User CRUD, Site CRUD, role assignment, `user_sites` history.
3. **Order creation**: Port `index.html`/`app.js`/`viewer.js` into `orders/create.blade.php`; wire to `OrderItemController@store` with server-side recalculation.
4. **Submit → Approve/Reject flow**: `ManagerOrderController`, policies, notifications.
5. **Workshop queue**: `WorkshopOrderController`, status transitions, print/export report (reuse existing CSV/print logic, now DB-driven).
6. **Hardening**: UFW rules, `.env` review, backup script, DEPLOYMENT.md update.
7. **QA pass**: verify each of the 25 duct-type formulas server-side against known `ducts.js` outputs before go-live.

---

## 13. Open Items / Future Enhancements

- Move duct type formulas fully into DB-configurable params if formulas ever need tweaking without redeploy (not urgent now).
- File attachments (site photos, spec PDFs) per order.
- Multi-site managers (if one PM oversees more than one site).
- Offline-friendly entry for site engineers with poor connectivity.
