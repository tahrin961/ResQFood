# ResQFood

A simple food-rescue platform for a Bangladesh context: donors (restaurants, caterers, households) post surplus food, NGOs claim and pick it up before it spoils, and an admin oversees the whole thing.

Built with PHP + MySQL + HTML/CSS. No frameworks — just what you'd use in XAMPP.

## Setup (XAMPP)

1. Copy the whole `resqfood` folder into `htdocs/`, so you end up with `htdocs/resqfood/...`.
2. Start Apache and MySQL in the XAMPP control panel.
3. Open phpMyAdmin ( http://localhost/phpmyadmin ) → **Import** → choose `sql/resqfood.sql` → Go.
   - This creates the `resqfood` database, its tables, and some sample data.
4. Copy `config/db.example.php` to `config/db.php` and fill in your real credentials. `db.php` is gitignored on purpose — it holds real DB credentials and should never be committed. The defaults in the example (`root`, no password) match a standard XAMPP install.
5. Visit **http://localhost/resqfood/** in your browser.

### Logging in

Sample accounts are included (password for all of them: `password123`):

| Role  | Email                        |
|-------|-------------------------------|
| Admin | admin@resqfood.test           |
| Donor | karim@resqfood.test           |
| Donor | farhana@resqfood.test         |
| NGO   | asharalo@resqfood.test        |
| NGO   | commkitchen@resqfood.test     |

Or register your own donor/NGO account from the homepage.

### Setting up your own admin instead

If you'd rather not use the sample admin account, delete the `admin` row from the `users` table, then visit `http://localhost/resqfood/create_admin.php` and fill in the form. **Delete `create_admin.php` afterwards** — it's a one-time setup script and shouldn't be left reachable.

## How it's structured

```
resqfood/
├── config/db.php          DB connection + BASE_URL constant
├── includes/              functions.php (auth/helpers), header.php, footer.php
├── assets/                style.css, app.js
├── index.php, login.php, register.php, logout.php, create_admin.php
├── donor/                 dashboard, add_listing, edit_listing, delete_listing
├── ngo/                   dashboard (browse+claim), my_claims, complete_claim
├── admin/                 dashboard, listings, users  (read-only views)
└── sql/resqfood.sql       schema + sample data
```

**Tables:** `users` (donor/ngo/admin roles), `food_listings` (what's posted), `claims` (which NGO claimed what).

**Listing lifecycle:** `available` → `claimed` (an NGO claims it) → `completed` (NGO marks it received). A listing whose expiry time passes while still `available` is shown as expired without a DB status change (`urgency_status()` in `functions.php` handles that check live, so nothing needs a cron job).

**Concurrency:** `ngo/claim.php` uses a conditional `UPDATE ... WHERE status = 'available'` so two NGOs can't successfully claim the same listing — worth mentioning if your practical asks about race conditions.

## What's simplified from the original FoodPulse pitch

This is the scoped-down version for a coursework build — it drops AI matching, blockchain tracking, SMS/USSD access, and the monetization layer, keeping only: listings, claiming, role-based dashboards, and basic admin oversight.
