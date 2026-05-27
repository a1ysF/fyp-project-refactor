# CryptoLearn — Railway Deployment Guide

**Sistem Pembelajaran Kriptografi Interaktif** (plain PHP, Apache, MySQL)

This guide covers Phase 1: deploying the existing XAMPP project to [Railway](https://railway.com) without changing the application stack (no Laravel, Next.js, etc.).

> **Roles:** Only **Teacher** and **Student** are implemented. **Admin** is not in this codebase (future backlog only).

---

## 1. Repository layout (confirmed)

| Path | Purpose |
|------|---------|
| `index.html` | Public landing page (homepage) |
| `connection.php` | PDO database connection (local + Railway env) |
| `login_signup/` | Registration and login |
| `dashboard/` | Student dashboards and pages |
| `dashboard/dash_teach/` | Teacher dashboards and pages |
| `database/schema.sql` | MySQL table definitions |
| `database/seed.sql` | Demo teacher/student accounts |
| `uploads/` | Static assets for teacher views (`../uploads/`) |
| `dashboard/uploads/` | Static assets for student dashboard pages |

---

## 2. Railway architecture

- **Web service:** Docker image (`php:8.2-apache`) built from the repo `Dockerfile`
- **MySQL service:** Railway MySQL plugin (linked to the web service)
- **Homepage:** Apache `DirectoryIndex` serves `index.html` first
- **Port:** Container listens on Railway’s `PORT` (default `8080`)

---

## 3. Create a Railway project

1. Sign in at [railway.com](https://railway.com).
2. **New Project** → **Deploy from GitHub repo**.
3. Select this repository and branch (`main`).
4. Railway detects `railway.json` and builds with the **Dockerfile**.

---

## 4. Add MySQL

1. In the project, click **+ New** → **Database** → **MySQL**.
2. Open the **web** service → **Variables** → **Add variable reference** (or “Connect”) and link the MySQL service.
3. Railway injects (among others):

| Variable | Used by `connection.php` |
|----------|-------------------------|
| `MYSQLHOST` | Database host |
| `MYSQLPORT` | Port (usually `3306`) |
| `MYSQLUSER` | Username |
| `MYSQLPASSWORD` | Password |
| `MYSQLDATABASE` | Database name |

No secrets need to be committed to Git.

---

## 5. Web service environment variables

### Required (via MySQL link)

- `MYSQLHOST`, `MYSQLPORT`, `MYSQLUSER`, `MYSQLPASSWORD`, `MYSQLDATABASE`

### Recommended

| Variable | Example | Purpose |
|----------|---------|---------|
| `APP_BASE_URL` | `https://your-app.up.railway.app` | Email verification links in `plugins/admin/send_verification_email.php` |

Set `APP_BASE_URL` to your Railway **public URL** (no trailing slash). If omitted, verification emails fall back to `http://localhost`.

### Optional (email — not required for basic login testing)

Signup can send Gmail verification mail; SMTP credentials are still in `plugins/admin/send_verification_email.php`. Email is **not** required to log in with seeded accounts (`verify = 1`).

---

## 6. Import database

After MySQL is running, import schema and seed data.

### Option A — Railway MySQL shell / client

1. MySQL service → **Connect** → copy connection details.
2. From a machine with `mysql` CLI:

```bash
mysql -h YOUR_HOST -P YOUR_PORT -u YOUR_USER -pYOUR_PASSWORD YOUR_DATABASE < database/schema.sql
mysql -h YOUR_HOST -P YOUR_PORT -u YOUR_USER -pYOUR_PASSWORD YOUR_DATABASE < database/seed.sql
```

### Option B — phpMyAdmin / TablePlus / DBeaver

1. Connect using Railway’s host, port, user, password, database.
2. Run the contents of `database/schema.sql`, then `database/seed.sql`.

### Tables created

`users`, `class`, `class_users`, `materials`, `records`, `rewards`, `badges`, `badge_users`

---

## 7. Upload static assets (logos / intro video)

User-uploaded and media files are **gitignored** (`uploads/*`). For logos and the intro video to appear:

1. Copy from your XAMPP backup into the repo (or upload after deploy via Railway volume — not configured in Phase 1):
   - `uploads/crypto-logo.png`
   - `uploads/class-icon.png` (optional)
   - `uploads/Cryptography - Introduction.mp4` (optional)
   - Same files under `dashboard/uploads/` if used from student pages
2. Or redeploy after adding files locally and temporarily adjusting `.gitignore` for those assets only.

Dockerfile creates writable `uploads/` and `dashboard/uploads/` trees with `www-data` permissions.

---

## 8. Deploy and get the public URL

1. Push commits to GitHub (Railway auto-deploys if connected).
2. Web service → **Settings** → **Networking** → **Generate domain**.
3. Set `APP_BASE_URL` to that URL and redeploy if you use email verification.

### URLs to test

| Page | Path |
|------|------|
| Landing | `https://YOUR_DOMAIN/` |
| Login | `https://YOUR_DOMAIN/login_signup/login.php` |
| Teacher signup | `https://YOUR_DOMAIN/login_signup/signup.php` |
| Teacher dashboard | `https://YOUR_DOMAIN/dashboard/dash_teach/dashboardT.php` |
| Student dashboard | `https://YOUR_DOMAIN/dashboard/dashboardS.php` |

---

## 9. Demo login accounts (after `seed.sql`)

| Role | Login (username or email) | Password | User ID |
|------|---------------------------|----------|---------|
| Teacher | `teacher_demo` or `teacher@cryptolearn.test` | `teacher123` | `T1001` |
| Student | `student_demo` or `student@cryptolearn.test` | `student123` | `S1001` |

Demo class (for student “join class” tests):

- **Class code:** `DEMO01`
- **Class name:** Cryptography 101
- **Teacher:** `T1001`

Passwords are **plain text** in the database (legacy FYP behaviour).

---

## 10. Local XAMPP (unchanged)

If Railway variables are **not** set, `connection.php` uses:

- Host: `localhost`
- Port: `3306`
- User: `root`
- Password: *(empty)*
- Database: `fyp`

Import the same `database/schema.sql` and `database/seed.sql` into phpMyAdmin locally.

---

## 11. Manual testing checklist

1. [ ] Open Railway public URL — landing page (`index.html`) loads
2. [ ] Open `login_signup/login.php`
3. [ ] Login as teacher (`teacher_demo` / `teacher123`)
4. [ ] Teacher dashboard loads (`dashboard/dash_teach/dashboardT.php`)
5. [ ] Logout, login as student (`student_demo` / `student123`)
6. [ ] Student dashboard loads (`dashboard/dashboardS.php`)
7. [ ] Student **My Class** (`dashboard/myclass.php`) — shows class or join with `DEMO01`
8. [ ] Teacher **My Classes** (`dashboard/dash_teach/myclass_teach.php`)
9. [ ] No “Connection failed” database error
10. [ ] No PHP fatal errors in browser or Railway deploy logs

---

## 12. Troubleshooting

| Issue | Check |
|-------|--------|
| Database connection failed | MySQL service linked; variables present; schema imported |
| 502 / app not listening | Deploy logs; `PORT` set by Railway; Docker build succeeded |
| Class create AJAX fails | Fixed to relative URLs in `myclass_teach.php` / `class_enter.php` |
| Broken images | Copy `crypto-logo.png` into `uploads/` and `dashboard/uploads/` |
| Login works but “not verified” banner | Use seeded users (`verify = 1`) or complete email verify flow |

---

## 13. Files added for Railway (Phase 1)

- `Dockerfile` — PHP 8.2 + Apache, PDO MySQL, writable upload dirs
- `docker/entrypoint.sh` — bind Apache to `$PORT`
- `docker/apache-vhost.conf` — document root, `index.html` first
- `railway.json` — Dockerfile builder
- `.dockerignore` — smaller build context
- `database/schema.sql`, `database/seed.sql`
- `README_RAILWAY_DEPLOYMENT.md` (this file)

**Modified:** `connection.php`, AJAX URLs in teacher class pages, `send_verification_email.php` (`APP_BASE_URL`), `alt_connect.php` (env parity).

---

## 14. Out of scope (later phases)

- Admin role
- Password hashing
- UI/CSS redesign
- Forum backend
- Major security hardening
- Persistent volumes for user uploads on Railway
