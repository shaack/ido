# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## What this is

**ido** is a personal project management, todo, time tracking and billing tool for the self-employed
(see `web/README.md`). It is a **CakePHP 4.5** application running on **PHP 8.3**.
All application code lives under `web/`; `compose/` holds the local Podman/Docker dev stack.

## Running the dev environment

From the repo root:

```bash
./run.sh              # starts podman machine if needed, then `podman compose up --build` in compose/
```

This brings up two containers (`compose/docker-compose.yml`):
- **web** — `php:8.3-apache` (see `compose/php.Dockerfile`), mounts `../web` to `/var/www/html`.
  Reachable at http://localhost:8080 and https://localhost:8443.
- **db** — `mysql:8.0`, database `main`, root password `d3v_p455`, port 3306. Data persists in
  `compose/volumes/data/`, which is **gitignored** (`compose/volumes/.gitignore`) — the dev DB is
  local only and is not backed up by the repo.

The app's DB connection for the container is preconfigured in `web/config/app_local.php`
(host `db`, database `main`). This file is normally gitignored in CakePHP but is present here.

PHP 8.3 is the ceiling for CakePHP 4.5: from PHP 8.4 on, `DateTimeImmutable::createFromTimestamp()`
collides with `Cake\Chronos\Chronos::createFromTimestamp()` and the framework dies with a fatal error
on autoload. Going higher requires the upgrade to CakePHP 5.

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

### The test suite does not actually test anything

Be aware of this before trusting a green run. The test classes under `tests/TestCase/` are unmodified
`bake` stubs — nearly every method is a `markTestIncomplete()`. On top of that there is no test schema:
`tests/schema.sql` is the untouched 146-byte skeleton placeholder with zero `CREATE TABLE`, and
`tests/bootstrap.php` builds the schema via `(new Migrator())->run()` from `config/Migrations/`,
which is empty. So every fixture-backed test errors with "table does not exist".

Fixing this means dumping the schema out of the dev DB into `tests/schema.sql` and swapping the
`Migrator` call in `tests/bootstrap.php` for `(new SchemaLoader())->loadSqlFiles('./tests/schema.sql', 'test')`
(the line is already there, commented out). Until someone does that, **verify changes against the
running app, not against the test suite**.

PHPUnit is pinned to **9.6**: CakePHP 4 declares `phpunit ^8.5 || ^9.3`, and under PHPUnit 10
`Cake\TestSuite\TestCase` fatals trying to override methods that PHPUnit made `final`. The failure is
silent (Cake's error handler swallows it, you just get exit code 255). Do not bump PHPUnit before
CakePHP 5.

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
- **`MarkdownHelper`** (`src/View/Helper/MarkdownHelper.php`) — the only place Markdown gets rendered.
  Loaded in `AppView`, runs Parsedown in **safe mode**, so raw HTML in notes/descriptions is escaped.
  Never instantiate `Parsedown` in a template. `toHtmlWithHashtags()` additionally highlights
  `#hashtags`; it escapes the `#` *before* parsing (otherwise a hashtag opening a line would be read
  as a heading) and wraps it in a `<span>` *after* parsing (safe mode would escape markup injected
  into the source). A real heading is `# Titel` with a space and is left alone.

## Deployment

`./deploy.sh` mirrors `web/` to the Plesk host via SFTP (`lftp mirror --reverse --delete`), excluding
`config/app_local.php`, `config/.env`, `logs/`, `tmp/`. `--dry-run` shows what would happen,
deletions included — use it whenever the diff is bigger than a few files.

The upload includes the **locally built `web/vendor/`** — there is no `composer install` on the
server. Two consequences:

- The script rebuilds `vendor/` with `composer install --no-dev` before uploading and restores the
  dev state afterwards via an `EXIT` trap. Without this, DebugKit, PHPUnit, Bake and CodeSniffer end
  up on the production server (they used to). All of `composer audit`'s advisories live in those dev
  packages; `composer audit --no-dev` is clean.
- Composer must resolve against the *server's* PHP version, not the local CLI's, which is what the
  `config.platform.php` pin in `web/composer.json` is for. Keep that pin in sync with the PHP version
  in Plesk and in `compose/php.Dockerfile`; if they drift, you ship a `vendor/` that cannot run on
  the server.

`tmp/` is excluded, so the server keeps its old schema and routing cache across deploys. After a
schema change or a framework update, clear `tmp/cache/models/` and `tmp/cache/persistent/` in Plesk.

## Schema changes

There is **no migrations workflow in use** — `config/Migrations/` is empty and schema evolves via raw
SQL. `web/docs/add_field.md` and `web/docs/migration.md` contain the hand-written SQL used for past
column additions and data backfills (e.g. mapping old German string statuses to `project_status_id`,
converting `'Ja'/'Nein'` to booleans). Follow that pattern and update those docs when altering schema.

## Writing style (German prose)

User-facing German text follows the global rules in `~/.claude/CLAUDE.md`: avoid em/en dashes as a
stylistic device, use colons sparingly, write number ranges with a plain hyphen. Code is exempt.
Do not add `Co-Authored-By:` trailers to commits.
