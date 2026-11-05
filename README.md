# Supper Club
### A Zomato-inspired restaurant discovery and mock ordering project

Explore six fictional Bengaluru kitchens, search eighteen dishes, build a single-kitchen bag and place a simulated order. All artwork, styles, scripts and data are bundled locally. The site makes no third-party requests. Use only fictional classroom details.

## Start with XAMPP on Windows

1. Use XAMPP with **PHP 8.1 or later**, Apache 2.4 and MySQL or MariaDB. In the XAMPP Control Panel, start **Apache** and **MySQL**.
2. Extract the ZIP. Copy its **supper-club** folder to `C:\xampp\htdocs\supper-club`. You should see `public`, `database`, `app` and this README directly inside it. Keep both bundled `.htaccess` files. This Apache route assumes the normal XAMPP setting that permits local overrides.
3. Open [local phpMyAdmin](http://localhost/phpmyadmin/). From the server-level **Import** tab, choose `database/schema.sql`, select UTF-8, and click **Import** (or **Go**). Use a fresh database. The script creates and selects `supper_club`, then creates five tables and inserts 24 catalogue records. It intentionally stops on existing tables to protect your work.
4. Open `config.php`. Its defaults match a fresh local XAMPP installation using port 3306, database user `root` and an empty database password. Change them to match your **local demo database** if needed.
5. Visit [Supper Club on localhost](http://localhost/supper-club/public/). Confirm that **Kitchen picks** shows three cards. Use **All kitchens** to see all six. The public directory is the web entry point.
6. Visit **Sign in → Create a demo account**. For example, use demo name `Mira Rao`, email `mira@example.test`, code `SC-1001` and the made-up password `DosaClub42`. Nothing is sent to that email. No account is pre-created.
7. To enable the admin screens for that fictional account, open PowerShell and run the following. Sign out and back in. **Catalogue desk** then appears in the footer.

```powershell
cd C:\xampp\htdocs\supper-club
C:\xampp\php\php.exe bin\make-admin.php mira@example.test
```

**Privacy check for Apache.** Opening `http://localhost/supper-club/storage/feedback.txt` should return 403. If it opens, move the project outside the web root and use the PHP server option below.

## PHP development server option

Keep the extracted project outside Apache's web root, start MySQL/MariaDB, and import the same SQL file. From the project folder run:

```powershell
C:\xampp\php\php.exe -S 127.0.0.1:8000 -t public
```

Open [the PHP development server](http://127.0.0.1:8000/). On macOS or Linux, use `php` instead of the full Windows executable path. Enable `pdo_mysql`. The PHP process must be able to write to `storage/` and its configured session directory. There is no Composer or npm install step. Optionally override the database connection with `SUPPER_DSN`, `SUPPER_DB_USER`, and `SUPPER_DB_PASSWORD`.

## A five-minute evaluator walkthrough

1. On Home, search for **margherita**. Open **Slice Society**, then type **garlic** in the live menu filter. Clear the filter to see all three dishes.
2. Add two basil margheritas. Open **Bag** and check the ₹570 total. Try adding a dish from another kitchen, then return to the original bag.
3. Sign in and place the mock order. **My orders** shows a receipt with the dish names and quantities. The bag is empty after success.
4. Open **Tasting notes** in the footer. Create, edit and delete a note. In **Interaction lab**, move the mouse, tab between tickets, open help and press Escape.
5. Sign in as the promoted admin, open **Catalogue desk**, add a temporary kitchen and dish, change its price, mark it sold out and inspect the public menu. Delete the temporary records.

## Files for submission

| Path | Contents |
| --- | --- |
| `public/` | Front controller, responsive stylesheet, event/validation JavaScript and original SVG illustrations |
| `views/` | Page templates and shared navigation |
| `app/` | Database access, security, cart logic, POST actions, admin and feedback operations |
| `database/schema.sql` | MySQL-compatible schema, keys and sample rows |
| `storage/feedback.txt` | Private text-file module, initially an empty JSON array |
| `bin/make-admin.php` | Local CLI-only admin role assignment |
| `docs/REPORT.md` and `docs/er.svg` | Reference analysis, architecture, module map and ER diagram |
| `docs/TESTS.md` and `tests/journey.py` | Test cases, verification scope and repeatable HTTP tests |
| `docs/SCREENSHOTS.md` and `docs/screenshots/` | Screenshot guidance and captured replica views |

## Troubleshooting

- **404 or PHP shown as text** — Use the HTTP address above and verify the folder nesting. Opening a PHP file directly or using VS Code Live Server cannot execute PHP.
- **Local service error** — Check that MySQL is running, the import finished, the DSN matches the correct port and database, and PHP has `pdo_mysql` enabled. Check the PHP server terminal or Apache error log for the underlying message.
- **File feedback fails** — Give the PHP process write access to the project’s `storage` directory, including permission to create and rename files. Keep it outside the public path.
- **No admin link** — Promote the registered fictional email with the CLI script, then sign out and back in to refresh its session role.
- **Returning visitor** — The previous-visit label changes at the start of the next session. Sign out to end the current one and keep the last-visit cookie.

