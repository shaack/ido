<?php
declare(strict_types=1);

namespace App\Mcp;

use App\Backup\DatabaseBackup;
use App\Model\Entity\Project;
use App\Model\Entity\Service;
use App\Model\Entity\Task;
use Cake\I18n\Date;
use Cake\I18n\DateTime;
use Cake\ORM\Locator\LocatorAwareTrait;
use Cake\ORM\Query\SelectQuery;
use InvalidArgumentException;

/**
 * Die Tools des MCP-Endpunkts. Ein Tool ist ein Name, eine Beschreibung für
 * den Agenten, ein JSON-Schema seiner Argumente, ein Flag, ob es nur liest,
 * und eine Methode, die ein Array liefert. Das geht als JSON an den Agenten.
 *
 * Aufwand und Kosten kommen aus den Entities (Service::effort(), costs()),
 * damit der Agent dieselben Zahlen sieht wie Rechnung und Stundennachweis.
 * Löschen ist bewusst nicht möglich.
 *
 * Vor jedem schreibenden Tool wird die Datenbank gesichert, höchstens einmal
 * pro Stunde. Scheitert die Sicherung, unterbleibt die Änderung.
 */
class Tools
{
    use LocatorAwareTrait;

    /** Ab diesem Status ist ein Projekt beendet, siehe project_statuses. */
    private const ENDED_STATUS = 40;

    /** So alt darf die jüngste Sicherung vor einer Schreibaktion sein. */
    private const BACKUP_MAX_AGE = 3600;

    /**
     * Die Tools im Format von `tools/list`.
     */
    public function definitions(): array
    {
        $result = [];
        foreach ($this->tools() as $name => $tool) {
            $result[] = [
                'name' => $name,
                'description' => $tool['description'],
                'inputSchema' => $tool['inputSchema'],
                'annotations' => ['readOnlyHint' => $tool['readOnly']],
            ];
        }

        return $result;
    }

    public function isReadOnly(string $name): bool
    {
        return $this->tools()[$name]['readOnly'] ?? true;
    }

    /**
     * Führt ein Tool aus. Falsche Argumente werfen eine
     * InvalidArgumentException, deren Text an den Agenten geht.
     */
    public function call(string $name, array $arguments): array
    {
        $tool = $this->tools()[$name] ?? null;
        if ($tool === null) {
            throw new InvalidArgumentException("Unknown tool '$name'");
        }
        if (!$tool['readOnly']) {
            (new DatabaseBackup())->ensureRecent(self::BACKUP_MAX_AGE, 'mcp');
        }

        return $this->{$tool['method']}($arguments);
    }

    private function tools(): array
    {
        $id = fn(string $description) => ['type' => 'integer', 'description' => $description];
        $customer = ['type' => 'string', 'description' => 'Customer shortcut like "ASO", or the numeric id'];

        return [
            'list_customers' => [
                'method' => 'listCustomers',
                'readOnly' => true,
                'description' => 'Lists the customers with shortcut, name and hourly rate. By default only the current ones.',
                'inputSchema' => ['type' => 'object', 'properties' => [
                    'all' => ['type' => 'boolean', 'description' => 'Also former customers, default false'],
                ]],
            ],
            'list_projects' => [
                'method' => 'listProjects',
                'readOnly' => true,
                'description' => 'Lists projects, the newest first, with customer, status, dates and invoice data. By default only projects that have not ended (status below 40).',
                'inputSchema' => ['type' => 'object', 'properties' => [
                    'customer' => $customer,
                    'status_id' => $id('Only projects with this status, 15 for running projects'),
                    'include_ended' => ['type' => 'boolean', 'description' => 'Also ended projects, default false'],
                    'limit' => ['type' => 'integer', 'description' => 'Default 100'],
                ]],
            ],
            'get_project' => [
                'method' => 'getProject',
                'readOnly' => true,
                'description' => 'A project with its services and their tasks, tracked and billed hours and the net amount as the invoice would show it.',
                'inputSchema' => ['type' => 'object', 'properties' => [
                    'id' => $id('Project id'),
                ], 'required' => ['id']],
            ],
            'list_tasks' => [
                'method' => 'listTasks',
                'readOnly' => true,
                'description' => 'Lists tasks with their service, project and customer and the hours tracked on them. By default only open tasks, marked ones first.',
                'inputSchema' => ['type' => 'object', 'properties' => [
                    'customer' => $customer,
                    'project_id' => $id('Only tasks of this project'),
                    'service_id' => $id('Only tasks of this service'),
                    'include_done' => ['type' => 'boolean', 'description' => 'Also done tasks, default false'],
                    'limit' => ['type' => 'integer', 'description' => 'Default 200'],
                ]],
            ],
            'list_time_trackings' => [
                'method' => 'listTimeTrackings',
                'readOnly' => true,
                'description' => 'Lists the time trackings in a date range with task, project and customer, plus the sum of hours. By default the last 7 days.',
                'inputSchema' => ['type' => 'object', 'properties' => [
                    'from' => ['type' => 'string', 'description' => 'First day, YYYY-MM-DD'],
                    'to' => ['type' => 'string', 'description' => 'Last day, YYYY-MM-DD, default today'],
                    'customer' => $customer,
                    'project_id' => $id('Only trackings of this project'),
                ]],
            ],
            'list_backups' => [
                'method' => 'listBackups',
                'readOnly' => true,
                'description' => 'Lists the backups of the database, the newest first. A backup is made daily and before every change through this server.',
                'inputSchema' => ['type' => 'object', 'properties' => new \stdClass()],
            ],
            'create_backup' => [
                'method' => 'createBackup',
                'readOnly' => true,
                'description' => 'Backs up the database now. Changes through this server are backed up automatically, use this before larger work.',
                'inputSchema' => ['type' => 'object', 'properties' => new \stdClass()],
            ],
            'create_service' => [
                'method' => 'createService',
                'readOnly' => false,
                'description' => 'Adds a service (a billable position) to a project.',
                'inputSchema' => ['type' => 'object', 'properties' => [
                    'project_id' => $id('Project id'),
                    'name' => ['type' => 'string'],
                    'effort_est' => ['type' => 'number', 'description' => 'Estimated hours for the offer'],
                    'fixed_price' => ['type' => 'number', 'description' => 'Fixed net amount in euro, replaces the billing by hours'],
                ], 'required' => ['project_id', 'name']],
            ],
            'create_task' => [
                'method' => 'createTask',
                'readOnly' => false,
                'description' => 'Adds a task to a service.',
                'inputSchema' => ['type' => 'object', 'properties' => [
                    'service_id' => $id('Service id, see get_project'),
                    'name' => ['type' => 'string'],
                    'marked' => ['type' => 'boolean', 'description' => 'Pin the task to the top of the list'],
                ], 'required' => ['service_id', 'name']],
            ],
            'update_task' => [
                'method' => 'updateTask',
                'readOnly' => false,
                'description' => 'Renames a task, marks it done or open again, or pins it.',
                'inputSchema' => ['type' => 'object', 'properties' => [
                    'id' => $id('Task id'),
                    'name' => ['type' => 'string'],
                    'done' => ['type' => 'boolean'],
                    'marked' => ['type' => 'boolean'],
                ], 'required' => ['id']],
            ],
            'add_time_tracking' => [
                'method' => 'addTimeTracking',
                'readOnly' => false,
                'description' => 'Logs hours on a task. The hours are billed, so only log time the user stated.',
                'inputSchema' => ['type' => 'object', 'properties' => [
                    'task_id' => $id('Task id'),
                    'hours' => ['type' => 'number', 'description' => 'Duration in hours, 1.5 for 90 minutes'],
                    'date' => ['type' => 'string', 'description' => 'Day of the work, YYYY-MM-DD, default today'],
                ], 'required' => ['task_id', 'hours']],
            ],
        ];
    }

    private function listCustomers(array $args): array
    {
        $query = $this->fetchTable('Customers')->find()->orderBy(['shortcut' => 'ASC']);
        if (empty($args['all'])) {
            $query->where(['current' => true]);
        }
        $customers = [];
        foreach ($query->all() as $customer) {
            $customers[] = [
                'id' => $customer->id,
                'shortcut' => $customer->shortcut,
                'name' => $customer->name,
                'hourly_rate' => $customer->hourly_rate,
                'current' => (bool)$customer->current,
                'internal' => (bool)$customer->internal,
                'website' => $customer->website,
            ];
        }

        return ['customers' => $customers];
    }

    private function listProjects(array $args): array
    {
        $query = $this->fetchTable('Projects')->find()
            ->contain(['Customers', 'ProjectStatuses'])
            ->orderBy(['Projects.id' => 'DESC'])
            ->limit($this->limit($args, 100));
        if (isset($args['customer'])) {
            $query->where(['Projects.customer_id' => $this->customerId($args['customer'])]);
        }
        if (isset($args['status_id'])) {
            $query->where(['Projects.project_status_id' => (int)$args['status_id']]);
        } elseif (empty($args['include_ended'])) {
            $query->where(['OR' => [
                'Projects.project_status_id <' => self::ENDED_STATUS,
                'Projects.project_status_id IS' => null,
            ]]);
        }

        return ['projects' => array_map(fn(Project $p) => $this->projectSummary($p), $query->all()->toList())];
    }

    private function getProject(array $args): array
    {
        $project = $this->fetchTable('Projects')->find()
            ->contain([
                'Customers', 'ProjectStatuses',
                'Services' => ['sort' => ['Services.sort' => 'DESC', 'Services.id' => 'ASC']],
                'Services.Tasks' => ['sort' => ['Tasks.done' => 'ASC', 'Tasks.id' => 'ASC']],
                'Services.Tasks.TimeTrackings',
            ])
            ->where(['Projects.id' => $this->requireInt($args, 'id')])
            ->first();
        if ($project === null) {
            throw new InvalidArgumentException('No project with id ' . $args['id']);
        }
        $services = [];
        foreach ($project->services as $service) {
            $tasks = [];
            foreach ($service->tasks as $task) {
                $tasks[] = [
                    'id' => $task->id,
                    'name' => $task->name,
                    'done' => (bool)$task->done,
                    'marked' => (bool)$task->marked,
                    'tracked_hours' => round($task->duration(), 2),
                ];
            }
            $services[] = [
                'id' => $service->id,
                'name' => $service->name,
                'effort_est' => $service->effort_est,
                'fixed_price' => $service->fixed_price,
                'tracked_hours' => round($service->effortTracked(), 2),
                'billed_hours' => $service->fixed_price ? null : $service->effort(),
                'net' => round($service->costs($project->hourly_rate), 2),
                'tasks' => $tasks,
            ];
        }

        return $this->projectSummary($project) + [
            'description' => $project->description,
            'tracked_hours' => round($project->effortTracked(), 2),
            'billed_hours' => $project->effort(),
            'planned_hours' => $project->effortPlanned(),
            'planned_net' => round($project->costsPlanned(), 2),
            'net' => round($project->costs(), 2),
            'vat' => round($project->vat(), 2),
            'total' => round($project->total(), 2),
            'services' => $services,
        ];
    }

    private function listTasks(array $args): array
    {
        $query = $this->fetchTable('Tasks')->find()
            ->contain(['Services', 'Services.Projects', 'Services.Projects.Customers', 'Services.Projects.ProjectStatuses', 'TimeTrackings'])
            ->orderBy(['Tasks.marked' => 'DESC', 'Tasks.id' => 'ASC'])
            ->limit($this->limit($args, 200));
        if (empty($args['include_done'])) {
            $query->where(['Tasks.done' => false]);
        }
        if (isset($args['service_id'])) {
            $query->where(['Tasks.service_id' => (int)$args['service_id']]);
        }
        if (isset($args['project_id'])) {
            $query->where(['Services.project_id' => (int)$args['project_id']]);
        }
        if (isset($args['customer'])) {
            $query->where(['Projects.customer_id' => $this->customerId($args['customer'])]);
        }
        $tasks = [];
        foreach ($query->all() as $task) {
            $tasks[] = $this->taskSummary($task) + ['tracked_hours' => round($task->duration(), 2)];
        }

        return ['tasks' => $tasks];
    }

    private function listTimeTrackings(array $args): array
    {
        $to = isset($args['to']) ? $this->date($args['to'], 'to') : Date::now();
        $from = isset($args['from']) ? $this->date($args['from'], 'from') : $to->subDays(6);
        $query = $this->fetchTable('TimeTrackings')->find()
            ->contain(['Tasks', 'Tasks.Services', 'Tasks.Services.Projects', 'Tasks.Services.Projects.Customers', 'Tasks.Services.Projects.ProjectStatuses'])
            ->where([
                'TimeTrackings.created >=' => new DateTime($from->format('Y-m-d') . ' 00:00:00'),
                'TimeTrackings.created <' => new DateTime($to->addDays(1)->format('Y-m-d') . ' 00:00:00'),
                'TimeTrackings.duration >' => 0,
            ])
            ->orderBy(['TimeTrackings.created' => 'ASC']);
        if (isset($args['project_id'])) {
            $query->where(['Services.project_id' => (int)$args['project_id']]);
        }
        if (isset($args['customer'])) {
            $query->where(['Projects.customer_id' => $this->customerId($args['customer'])]);
        }
        $trackings = [];
        $sum = 0.0;
        foreach ($query->all() as $tracking) {
            $sum += $tracking->duration;
            $trackings[] = [
                'id' => $tracking->id,
                'created' => $tracking->created?->format('Y-m-d H:i'),
                'hours' => $tracking->duration,
                'task' => $this->taskSummary($tracking->task),
            ];
        }

        return [
            'from' => $from->format('Y-m-d'),
            'to' => $to->format('Y-m-d'),
            'total_hours' => round($sum, 2),
            'time_trackings' => $trackings,
        ];
    }

    private function listBackups(array $args): array
    {
        return ['backups' => array_map(fn(array $b) => $this->backupSummary($b), (new DatabaseBackup())->list())];
    }

    private function createBackup(array $args): array
    {
        return $this->backupSummary((new DatabaseBackup())->create('mcp-manual'));
    }

    private function backupSummary(array $backup): array
    {
        return [
            'name' => $backup['name'],
            'created' => $backup['created']->format('Y-m-d H:i:s'),
            'reason' => $backup['reason'],
            'size_kb' => round($backup['size'] / 1024, 1),
        ];
    }

    private function createService(array $args): array
    {
        $projectId = $this->requireInt($args, 'project_id');
        $projects = $this->fetchTable('Projects');
        if (!$projects->exists(['id' => $projectId])) {
            throw new InvalidArgumentException("No project with id $projectId");
        }
        $services = $this->fetchTable('Services');
        $service = $services->newEntity([
            'project_id' => $projectId,
            'name' => $this->requireString($args, 'name'),
            'effort_est' => isset($args['effort_est']) ? (float)$args['effort_est'] : null,
            'fixed_price' => isset($args['fixed_price']) ? (float)$args['fixed_price'] : null,
        ]);
        $services->saveOrFail($service);

        return ['id' => $service->id, 'project_id' => $projectId, 'name' => $service->name];
    }

    private function createTask(array $args): array
    {
        $serviceId = $this->requireInt($args, 'service_id');
        if (!$this->fetchTable('Services')->exists(['id' => $serviceId])) {
            throw new InvalidArgumentException("No service with id $serviceId");
        }
        $tasks = $this->fetchTable('Tasks');
        $task = $tasks->newEntity([
            'service_id' => $serviceId,
            'name' => $this->requireString($args, 'name'),
            'marked' => !empty($args['marked']),
            'done' => false,
        ]);
        $tasks->saveOrFail($task);

        return $this->taskSummary($this->loadTask($task->id));
    }

    private function updateTask(array $args): array
    {
        $tasks = $this->fetchTable('Tasks');
        $task = $this->loadTask($this->requireInt($args, 'id'));
        if (isset($args['name'])) {
            $task->name = $this->requireString($args, 'name');
        }
        if (isset($args['marked'])) {
            $task->marked = (bool)$args['marked'];
        }
        if (isset($args['done'])) {
            // Wie TasksController::done(): done_at hält den Zeitpunkt der
            // letzten Änderung, auch beim Wiedereröffnen.
            $task->done = (bool)$args['done'];
            $task->done_at = DateTime::now();
        }
        $tasks->saveOrFail($task);

        return $this->taskSummary($this->loadTask($task->id));
    }

    private function addTimeTracking(array $args): array
    {
        $task = $this->loadTask($this->requireInt($args, 'task_id'));
        $hours = round((float)($args['hours'] ?? 0), 2);
        if ($hours <= 0 || $hours > 24) {
            throw new InvalidArgumentException('hours must be above 0 and at most 24');
        }
        // Ohne Datum gilt jetzt, mit Datum die Mittagszeit des Tages, damit
        // keine Zeitzonenverschiebung den Eintrag auf den Vortag schiebt.
        $created = isset($args['date'])
            ? new DateTime($this->date($args['date'], 'date')->format('Y-m-d') . ' 12:00:00')
            : DateTime::now();
        $timeTrackings = $this->fetchTable('TimeTrackings');
        $tracking = $timeTrackings->newEntity(['task_id' => $task->id, 'duration' => $hours]);
        $tracking->created = $created;
        $timeTrackings->saveOrFail($tracking);
        $taskHours = $timeTrackings->find()->where(['task_id' => $task->id])->all()->sumOf('duration');

        return [
            'id' => $tracking->id,
            'created' => $created->format('Y-m-d H:i'),
            'hours' => $hours,
            'task' => $this->taskSummary($task),
            'task_tracked_hours' => round((float)$taskHours, 2),
        ];
    }

    private function projectSummary(Project $project): array
    {
        return [
            'id' => $project->id,
            'name' => $project->name,
            'customer' => $project->customer?->shortcut,
            'customer_name' => $project->customer?->name,
            'status_id' => $project->project_status_id,
            'status' => $project->project_status?->name,
            'start' => $project->start?->format('Y-m-d'),
            'end' => $project->end?->format('Y-m-d'),
            'hourly_rate' => $project->hourly_rate,
            'invoice_number' => $project->invoice_number,
            'invoice_date' => $project->invoice_date?->format('Y-m-d'),
            'paid_at' => $project->paid_at?->format('Y-m-d'),
        ];
    }

    private function taskSummary(Task $task): array
    {
        /** @var Service|null $service */
        $service = $task->service;
        $project = $service?->project;

        return [
            'id' => $task->id,
            'name' => $task->name,
            'done' => (bool)$task->done,
            'marked' => (bool)$task->marked,
            'service' => $service ? ['id' => $service->id, 'name' => $service->name] : null,
            'project' => $project ? ['id' => $project->id, 'name' => $project->name, 'status' => $project->project_status?->name] : null,
            'customer' => $project?->customer?->shortcut,
        ];
    }

    private function loadTask(int $id): Task
    {
        $task = $this->fetchTable('Tasks')->find()
            ->contain(['Services', 'Services.Projects', 'Services.Projects.Customers', 'Services.Projects.ProjectStatuses'])
            ->where(['Tasks.id' => $id])
            ->first();
        if ($task === null) {
            throw new InvalidArgumentException("No task with id $id");
        }

        return $task;
    }

    private function customerId(mixed $customer): int
    {
        $customers = $this->fetchTable('Customers');
        $found = is_int($customer) || ctype_digit((string)$customer)
            ? $customers->find()->where(['id' => (int)$customer])->first()
            : $customers->find()->where(['shortcut' => strtoupper(trim((string)$customer))])->first();
        if ($found === null) {
            throw new InvalidArgumentException("No customer '$customer', see list_customers");
        }

        return $found->id;
    }

    private function date(mixed $value, string $name): Date
    {
        if (!is_string($value) || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $value)) {
            throw new InvalidArgumentException("$name must be a date in the form YYYY-MM-DD");
        }

        return new Date($value);
    }

    private function limit(array $args, int $default): int
    {
        return max(1, min(1000, (int)($args['limit'] ?? $default)));
    }

    private function requireInt(array $args, string $name): int
    {
        if (!isset($args[$name]) || !is_numeric($args[$name])) {
            throw new InvalidArgumentException("$name is required and must be a number");
        }

        return (int)$args[$name];
    }

    private function requireString(array $args, string $name): string
    {
        $value = trim((string)($args[$name] ?? ''));
        if ($value === '') {
            throw new InvalidArgumentException("$name is required");
        }

        return $value;
    }
}
