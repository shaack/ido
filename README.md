# ido

Project management, todos, time tracking and billing for the self-employed.

ido is the tool I use in my daily work to manage customers, plan projects, track my time and write offers and invoices from it. More about it on the product page: [shaack.com/de/products/ido](https://shaack.com/products/ido)

## What it does

- **Customers and contacts** with a short code and a colour that marks them throughout the app.
- **Projects** with a status that follows the course of the work, from offer to invoice.
- **Services** as the line items of a project, each with an hourly rate, an estimate or a fixed price.
- **Tasks** as todos below a service, with a list of everything that is open and a way to mark the important ones.
- **Time tracking** on tasks, with a quarter-hour ruler and the hours tracked today always in view.
- **Offers and invoices** generated from the project, including a timesheet (Stundennachweis) that backs every invoice.
- **Reports** on weekly hours, hours per weekday and per customer, revenue per month and open receivables.
- **Dashboard** with what is next, what was tracked recently and what is ready to be invoiced.
- **Database backups** that run automatically and can be downloaded from the app.
- **MCP endpoint** so AI agents like Claude Code can read projects and add tasks and time trackings.

## Domain model

```
Customer ──hasMany──> Contacts
    └────hasMany──> Project ──belongsTo──> ProjectStatus
                       └──hasMany──> Service ──hasMany──> Task ──hasMany──> TimeTracking
```

Billing walks this chain upwards. The tracked time of a service is rounded to quarter hours and multiplied by the hourly rate. A service with a fixed price is billed with that amount, its tracked time then only shows the real effort. The estimate of a service feeds the offer, never the invoice.

## Technology

- CakePHP 5 on PHP 8.4, MySQL 8
- Bootstrap 5.3 with SCSS, light and dark mode
- Markdown in notes, rendered with Parsedown in safe mode

ido is a single-user application without its own login. On the server it runs behind HTTP basic auth.

## Development

The local stack runs with Podman:

```bash
./run.sh
```

This starts a PHP/Apache container and a MySQL container, see `compose/docker-compose.yml`. The app is then reachable at http://localhost:8080. All application code lives in `web/`.

Styles are compiled by hand and committed:

```bash
cd web/webroot/assets/styles && sass --quiet screen.scss screen.css
```

There is no migrations workflow. Schema changes are made with plain SQL and recorded in `web/docs/schema_changes.md`.

## Deployment

`./deploy.sh` builds `web/vendor/` without dev dependencies, boots the production build in the container as a check and then mirrors `web/` to the server via SFTP. `./deploy.sh --dry-run` shows what would change.

## Notes for contributors

`CLAUDE.md` describes the architecture, the billing rules and the pitfalls of this codebase in more detail.
