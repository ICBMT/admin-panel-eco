# Ecommerce Store + Spark Admin Panel

Laravel 13 ecommerce app whose **admin panel** was rebuilt with the **Spark Admin 1.0.0** template
(frontend code and images from the template only; backend adjusted only where the admin panel needed it).
The storefront, cart, checkout, Stripe flow and customer accounts are unchanged.

The application lives in the [`ecommerce/`](ecommerce/) folder — every command below runs from there.

---

## Requirements

| Tool | Version |
|---|---|
| PHP | **8.3+** with `pdo_sqlite` (default) *or* `pdo_mysql`, plus the usual Laravel extensions (`openssl`, `mbstring`, `ctype`, `tokenizer`, `fileinfo`, `dom`, `xml`, `gd` for image uploads) |
| Composer | 2.x |
| Node.js + npm | 20+ (only needed to build the frontend assets) |

---

## Option A — Fresh install (empty database / demo data)

```bash
cd ecommerce

composer install

cp .env.example .env
php artisan key:generate

# SQLite is the default driver — just create the file:
touch database/database.sqlite
# (For MySQL instead: set DB_CONNECTION=mysql + DB_HOST/DB_PORT/DB_DATABASE/DB_USERNAME/DB_PASSWORD in .env)

php artisan migrate                        # creates all tables
php artisan db:seed --class=DemoStoreSeeder  # optional: demo store data + admin account

npm ci
npm run build                              # builds public/ (gitignored, never committed)

php artisan storage:link                   # makes product images in storage/app/public reachable

php artisan serve                          # http://127.0.0.1:8000
```

Demo logins after seeding (`DemoStoreSeeder`):

| Role | Email | Password |
|---|---|---|
| Store admin | `admin@example.com` | `password` |
| Customers | `ava@example.com`, `liam@example.com`, `cust@example.com` | `password` |

Open **`/admin`** for the admin panel. (`php artisan db:seed` without `--class` only creates a `test@example.com` user.)

> ⚠️ **Do not use `composer setup`** if you care about keeping an existing `APP_KEY` — that script regenerates the key and runs migrations automatically.

---

## Option B — Restore with your **old `.env`** and **old database** (keep data, keys, passwords)

This is the "drop in my old `.env` and it just works" path.

### 1. Copy your old files in

```bash
cd ecommerce
cp /path/to/your/old/.env .env
```

Keep **everything** from the old file — in particular:

| Variable | Why it must stay the old value |
|---|---|
| `APP_KEY` | Signs cookies/sessions and decrypts existing encrypted values. **Never re-run `php artisan key:generate`** on an old install (or, if you must, put the old key in `APP_PREVIOUS_KEYS`). |
| `DB_CONNECTION`, `DB_*` | Points at your old database. If it was SQLite, also copy your old `database/database.sqlite` file back in. |
| `STRIPE_KEY`, `STRIPE_SECRET` | Used by `config/services.php` for the existing Stripe checkout. |
| `MAIL_*` | Order confirmation mails (queued). |
| `APP_URL` | Set it to the real host you serve the app on (used by `asset()`/links). |

### 2. Install dependencies and build assets

```bash
composer install
npm ci
npm run build
```

### 3. Clear cached config from any previous environment

```bash
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan cache:clear
```

### 4. Run the migration — safe, non-destructive

```bash
php artisan migrate
```

The **only** schema change in this update is one column:

```
users.is_admin  (boolean, default: false)
```

`php artisan migrate` only runs *pending* migrations — it never touches existing rows.
(⇢ Never run `migrate:fresh` or `migrate:refresh` against an old database: those **drop all data**.)

### 5. Link storage (product images)

```bash
php artisan storage:link
```

### 6. Promote your account to admin

Every user (including all your old accounts) defaults to `is_admin = false`, so nobody gets admin access by accident. Promote yourself once:

```bash
php artisan tinker
```

```php
\App\Models\User::where('email', 'you@example.com')->update(['is_admin' => true]);
```

### 7. Log in and open the admin panel

```bash
php artisan serve     # or your usual nginx/apache vhost pointing at public/
```

* Log in at `/login` **with your existing email and password** — passwords are bcrypt hashes stored *in your database*, not in `.env`, so **all old accounts and passwords keep working unchanged**.
* Open **`/admin`** — you are now an admin.

---

## What the new admin panel adds

| Area | Route | Notes |
|---|---|---|
| Dashboard | `/admin` | Revenue, 8-day income/cancelled charts, payment-method donut, recent orders, product overview (Spark widgets) |
| Orders | `/admin/orders` | Search by order id / customer name / email, filter by status, order detail with status history, update status |
| Order status updates | `PUT /admin/orders/{id}/status` | Writes an `order_status_histories` row; COD orders marked *delivered* are auto-marked *paid*; the customer gets a queued notification mail |
| Products | `/admin/products` | Searchable list + the existing create/edit/delete forms (now admin-only) |
| Categories | `/admin/categories` | Searchable list + existing create/edit/delete forms (now admin-only) |
| Excel import | `POST /products/import` | Bulk product import (xlsx/xls/ods/csv) |

Non-admin users get **403 Forbidden** on every admin route. Storefront routes are untouched.

---

## Troubleshooting

| Symptom | Fix |
|---|---|
| `403 Forbidden` on `/admin` | Your account isn't admin → Option B step 6. |
| `419 Page Expired` / everyone logged out after restoring `.env` | The `APP_KEY` changed. Restore the old key (or list it in `APP_PREVIOUS_KEYS`). |
| Product images broken | Run `php artisan storage:link`; check the old `storage/app/public/products` files were carried over. |
| Pages unstyled / JS errors | `public/build` is gitignored — run `npm ci && npm run build`. (Building fully offline can fail while the Vite Laravel plugin downloads Bunny Fonts; on a normal network it works as-is.) |
| `SQLSTATE[HY000] ... no such table: users.is_admin`-style errors | You skipped `php artisan migrate` after pulling this update. |
| Emails not arriving | Check `MAIL_*` in `.env`; default `MAIL_MAILER=log` writes mails to `storage/logs/laravel.log`. Queued mails need `queue:work` (or set `QUEUE_CONNECTION=sync`). |
