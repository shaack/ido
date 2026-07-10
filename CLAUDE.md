# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## What this is

**ido** is a personal project management, todo, time tracking and billing tool for the self-employed
(see `web/README.md`). It is a **CakePHP 4.5** application (PHP 7.4+, targets 8.1 in the container).
All application code lives under `web/`; `compose/` holds the local Podman/Docker dev stack.

## Running the dev environment

From the repo root:

```bash
./run.sh              # starts podman machine if needed, then `podman compose up --build` in compose/
```

This brings up two containers (`compose/docker-compose.yml`):
- **web** — `php:8.1-apache` (see `compose/php.Dockerfile`), mounts `../web` to `/var/www/html`.
  Reachable at http://localhost:8080 and https://localhost:8443.
- **db** — `mysql:8.0`, database `main`, root password `d3v_p455`, port 3306. Data persists in
  `compose/volumes/data/` (committed to git, so the dev DB ships with the repo).

The app's DB connection for the container is preconfigured in `web/config/app_local.php`
(host `db`, database `main`). This file is normally gitignored in CakePHP but is present here.

## Common commands

Run these **inside `web/`** (or exec into the `ido-web` container):

```bash
composer test          # PHPUnit (also: vendor/bin/phpunit)
composer cs-check      # PHP_CodeSniffer against the CakePHP standard (src/ tests/)
composer cs-fix        # phpcbf auto-fix
composer stan          # PHPStan (level 8, src/ only) — requires phpstan installed
composer check         # test + cs-check

vendor/bin/phpunit --filter TimeTrackingsControllerTest        # single test class
vendor/bin/phpunit tests/TestCase/Controller/TasksControllerTest.php   # single file

bin/cake <command>     # CakePHP console (bake, migrations, etc.)
```

Tests run against SQLite in CI (`DATABASE_TEST_URL=sqlite://...`); the `test` datasource in
`app_local.php` points at MySQL locally. Fixtures live in `tests/Fixture/`, schema in `tests/schema.sql`.

## Domain model & architecture

The core is a strict **ownership hierarchy** wired through CakePHP ORM associations
(`web/src/Model/Table/*Table.php`):

```
Customer ──hasMany──> Contacts
    └────hasMany──> Project ──belongsTo──> ProjectStatus (lookup)
                       ├──self-ref: ParentProjects / ChildProjects (sub-projects)
                       └──hasMany──> Service ──hasMany──> Task ──hasMany──> TimeTracking
```

A **TimeTracking** (billable duration in hours) hangs off a Task; billing/reporting walks the whole
chain back up to the Customer. Controllers therefore `contain` the full path, e.g.
`['Tasks', 'Tasks.Services', 'Tasks.Services.Projects', 'Tasks.Services.Projects.Customers']`.
The seven entities/tables (Customers, Contacts, Projects, ProjectStatuses, Services, Tasks,
TimeTrackings) each have a matching Controller + `templates/<Name>/` directory following standard
CakePHP conventions. Routing is default `DashedRoute` fallbacks — no custom routes
(`config/routes.php`).

### Project-specific pieces to know

- **`PreserveNullBehavior`** (`src/Model/Behavior/PreserveNullBehavior.php`) — added to tables via
  `$this->addBehavior('PreserveNull')`. On `beforeMarshal` it recursively converts empty-string form
  values (`''`) to `null` for nullable columns, walking into associated data. Needed so blank form
  fields don't overwrite nullable DB columns with empty strings.
- **Time-tracking export** (`TimeTrackingsController::export($customerShortcut, $month)`) — the
  billing feature. Filters TimeTrackings by `Customers.shortcut` and a `YYYY-MM` month range, sums
  `duration`, and renders the `export` template with the `print` layout. Companion raw-SQL reports
  live in `web/scripts/reports/*.sql`.
- **`AjaxView`** (`src/View/AjaxView.php`) — switches to the `ajax` layout for AJAX responses.

## Schema changes

There is **no migrations workflow in use** — `config/Migrations/` is empty and schema evolves via raw
SQL. `web/docs/add_field.md` and `web/docs/migration.md` contain the hand-written SQL used for past
column additions and data backfills (e.g. mapping old German string statuses to `project_status_id`,
converting `'Ja'/'Nein'` to booleans). Follow that pattern and update those docs when altering schema.

## Writing style (German prose)

User-facing German text follows the global rules in `~/.claude/CLAUDE.md`: avoid em/en dashes as a
stylistic device, use colons sparingly, write number ranges with a plain hyphen. Code is exempt.
Do not add `Co-Authored-By:` trailers to commits.
