# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## What this is

**ido** is a personal project management, todo, time tracking and billing tool for the self-employed
(see `web/README.md`). It is a **CakePHP 5.3** application running on **PHP 8.4**.
All application code lives under `web/`; `compose/` holds the local Podman/Docker dev stack.

Single user, single tenant, behind HTTP basic auth on the server. There is no application-level
authentication.

## Running the dev environment

From the repo root:

```bash
./run.sh              # starts podman machine if needed, then `podman compose up --build` in compose/
```

This brings up two containers (`compose/docker-compose.yml`):
- **web** — `php:8.4-apache` (see `compose/php.Dockerfile`), mounts `../web` to `/var/www/html`.
  Reachable at http://localhost:8080 and https://localhost:8443.
- **db** — `mysql:8.0`, database `main`, root password `d3v_p455`, port 3306. Data persists in
  `compose/volumes/data/`, which is **gitignored** — the dev DB is local only and is not backed up
  by the repo.

The app's DB connection for the container is preconfigured in `web/config/app_local.php`
(host `db`, database `main`). This file is normally gitignored in CakePHP but is present here.

Do not run PHP from the host: the local CLI is far newer than the container and the two disagree.
Use `podman exec -w /var/www/html ido-web php ...`.

## Common commands

```bash
cd web
composer test          # PHPUnit — but see below, the suite tests nothing
bin/cake <command>     # CakePHP console
```

`bake` and the CodeSniffer were removed as unused. `composer cs-check` no longer exists.

### The test suite does not actually test anything

Do not trust a green run. The classes under `tests/TestCase/` are unmodified `bake` stubs, nearly
every method is a `markTestIncomplete()`. There is also no test schema: `tests/schema.sql` is the
untouched skeleton placeholder with zero `CREATE TABLE`, and `tests/bootstrap.php` builds the schema
via `(new Migrator())->run()` from `config/Migrations/`, which is empty.

**Verify changes against the running app, not against the test suite.** Drive the actual route with
curl, compare numbers against the database, and check `web/logs/error.log`.

## Domain model & architecture

```
Customer ──hasMany──> Contacts
    └────hasMany──> Project ──belongsTo──> ProjectStatus (lookup)
                       └──hasMany──> Service ──hasMany──> Task ──hasMany──> TimeTracking
```

A **TimeTracking** (duration in hours) hangs off a Task. Billing walks the whole chain back up to the
Customer, so controllers `contain` the full path, e.g.
`['Tasks', 'Tasks.Services', 'Tasks.Services.Projects', 'Tasks.Services.Projects.Customers']`.
Routing is default `DashedRoute` fallbacks, no custom routes.

### Billing rules — read before touching any of this

- **`Service::effortTracked()`** is the raw sum of tracked time. **`Service::effort()`** is that value
  rounded to quarter hours, and it is what gets billed. `Service::costs()` multiplies it by the hourly
  rate, so the rounding lands directly in the invoice amount.
- **`Service::fixed_price`** (a euro amount, nullable) overrides the time calculation. If it is set,
  `costs()` returns it and ignores the tracked time entirely. Tasks and time tracking on such a
  service therefore do **not** raise the invoice, they only make the real effort and the margin
  visible. Negative amounts are used as deduction lines for installments already paid.
- The **Stundennachweis** (`TimeTrackingsController::export($projectId)`) is the document that backs
  the invoice, so the two must agree. It lists the individual trackings and then reconciles per
  service: tracked hours next to billed hours. The sum of the billed column times the hourly rate is
  exactly the invoice net. Fixed-price services are labelled as such instead of showing hours.
- **`Service::effort_est`** is the estimate in hours and feeds the **offer** only
  (`Project::effortPlanned()` / `costsPlanned()`). It never touches the invoice.

### Project-specific pieces to know

- **`EffortHelper`** (`src/View/Helper/EffortHelper.php`) — renders hours with two decimals in the
  current locale. Two methods on purpose: `effort()` rounds to quarter hours (for the billed values,
  mirroring the entities), `hours()` does not round (for tracked time). Rounding tracked time would
  misstate the billing basis.
- **`MarkdownHelper`** (`src/View/Helper/MarkdownHelper.php`) — the only place Markdown gets
  rendered. Loaded in `AppView`, runs Parsedown in **safe mode**, so raw HTML in notes is escaped.
  Never instantiate `Parsedown` in a template. `toHtmlWithHashtags()` escapes the `#` *before*
  parsing (otherwise a hashtag opening a line would become a heading) and wraps it in a `<span>`
  *after* parsing.
- **`PreserveNullBehavior`** (`src/Model/Behavior/PreserveNullBehavior.php`) — on `beforeMarshal` it
  converts empty-string form values to `null` for nullable columns, so blank fields do not overwrite
  nullable columns with `''`.
- **`AjaxView`** (`src/View/AjaxView.php`) — switches to the `ajax` layout for AJAX responses.
- Locale is **de_DE** and the timezone **Europe/Berlin**, both as committed defaults in
  `config/app.php`. They used to depend on `config/.env`, which is gitignored — when that file went
  missing the app silently fell back to `en_US` and rendered `3.5` and `$` on invoices.

### CakePHP 5 traps this codebase already walked into

- **`contain` in paginator settings is silently ignored.** `$this->paginate = ['contain' => [...]]`
  no longer works. Build a query with `->contain()` and pass that to `paginate()`. When it broke, the
  associations came back `null` and it looked like corrupt data.
- **Sorting by an associated field needs `sortableFields`.** Without it CakePHP silently drops the
  sort, and the column header becomes a link that does nothing. Ten such links existed. Fields that
  are computed in PHP (`effort()`, `costs()`) cannot be sorted at all, they are not columns.
- **`Entity::has()` returns true for a field set to `null`.** Use `hasValue()` for the old behaviour.
- **`SelectQuery` is not a collection.** `$query->sumOf()` is gone, use `$query->all()->sumOf()`.
- The global `h()`, `env()`, `__()` moved into namespaces. `composer.json` autoloads CakePHP's
  `functions_global.php` files to keep the 574 existing call sites working.

## Dead columns kept for their data

These are gone from the code but still in the database, so historic values survive. Do not
reintroduce them, and do not be surprised to find them in `SHOW COLUMNS`:

`projects.parent_id`, `projects.notes`, `projects.fixed_price`, `projects.invoice_type`,
`projects.end_est`, `services.effort`, `services.costs`, `services.notes`.

`services.effort` and `services.costs` are the pre-time-tracking way of recording effort, last used
in 2022. They disagree with the computed values for 478 of 618 services.

Careful when reading SQL: there is a `services.fixed_price` (double, an amount, live) **and** a
`projects.fixed_price` (tinyint, a dead flag). Same name, different tables, different meaning.

## Deployment

`./deploy.sh` mirrors `web/` to the Plesk host via SFTP (`lftp mirror --reverse --delete`), excluding
`config/app_local.php`, `config/.env`, `logs/`, `tmp/`. `--dry-run` shows what would happen,
deletions included.

The upload includes the **locally built `web/vendor/`**, there is no `composer install` on the
server. Three consequences:

- The script rebuilds `vendor/` with `composer install --no-dev` before uploading and restores the
  dev state afterwards via an `EXIT` trap.
- It then **boots the production build** in the container before uploading anything, with a
  temporary `config/.env` in place, because the server has one. This catches packages that are needed
  at runtime but sit in `require-dev`. That exact mistake once took the site down
  (`josegonzalez/dotenv`).
- Composer must resolve against the *server's* PHP version, not the local CLI's, which is what the
  `config.platform.php` pin in `web/composer.json` is for. Keep it in sync with Plesk and
  `compose/php.Dockerfile`.

`tmp/` is excluded, so the server keeps its old schema and routing cache across deploys. After a
schema change or a framework update, clear `tmp/cache/models/` and `tmp/cache/persistent/` in Plesk.

## Schema changes

There is **no migrations workflow** — `config/Migrations/` is empty and the schema evolves via raw
SQL. Record every change in `web/docs/schema_changes.md`, including the order it has to run in
relative to the deploy. `web/docs/add_field.md` and `web/docs/migration.md` hold older notes.

## Writing style (German prose)

User-facing German text follows the global rules in `~/.claude/CLAUDE.md`: avoid em/en dashes as a
stylistic device, use colons sparingly, write number ranges with a plain hyphen. Code is exempt.
Do not add `Co-Authored-By:` trailers to commits.
