<?php
declare(strict_types=1);

namespace App\Controller;

use App\Backup\DatabaseBackup;
use Cake\I18n\Date;

/**
 * Dashboard Controller
 *
 * Die Einstiegsseite unter /: heute erfasste Zeit, die letzten sieben Tage,
 * die nächsten Tasks, laufende Projekte und was an Geld aussteht.
 */
class DashboardController extends AppController
{
    /** Status "Runs", "Finished, send invoice" laut project_statuses. */
    private const STATUS_RUNS = 15;
    private const STATUS_SEND_INVOICE = 20;

    /** Wie ReportsController::RECEIVABLE_EXCLUDED_STATUSES. */
    private const RECEIVABLE_EXCLUDED_STATUSES = [45, 50, 55];

    /** So viele Tasks zeigt "Next up", der Rest steht unter Tasks. */
    private const TASK_LIMIT = 8;

    /** So viele laufende Projekte zeigt das Dashboard, die zuletzt bearbeiteten. */
    private const PROJECT_LIMIT = 6;

    public function index(): void
    {
        $this->set('days', $this->lastDays(7));
        $this->set('tasks', $this->nextTasks());
        $this->set('openTaskCount', $this->fetchTable('Tasks')->find()
            ->innerJoinWith('Services.Projects', fn($q) => $q->where(['Projects.project_status_id' => self::STATUS_RUNS]))
            ->where(['Tasks.done' => false])
            ->count());
        $running = $this->projectsWithStatus(self::STATUS_RUNS)->toList();
        $this->set('runningProjectCount', count($running));
        $this->set('runningProjects', $this->recentlyWorkedOn($running, self::PROJECT_LIMIT));
        $this->set('toInvoice', $this->projectsWithStatus(self::STATUS_SEND_INVOICE));
        $this->set('receivables', $this->receivables());
        $this->set('recentTrackings', $this->fetchTable('TimeTrackings')->find()
            ->contain(['Tasks.Services.Projects.Customers'])
            ->where(['TimeTrackings.duration >' => 0])
            ->orderBy(['TimeTrackings.created' => 'DESC'])
            ->limit(6)
            ->all());
        $this->set('lastBackup', (new DatabaseBackup())->list()[0] ?? null);
    }

    /**
     * Die erfassten Stunden je Tag, heute zuletzt.
     *
     * @return array<int, array{date: \Cake\I18n\Date, hours: float}>
     */
    private function lastDays(int $count): array
    {
        $today = Date::now();
        $first = $today->subDays($count - 1);
        $days = [];
        for ($i = 0; $i < $count; $i++) {
            $date = $first->addDays($i);
            $days[$date->format('Y-m-d')] = ['date' => $date, 'hours' => 0.0];
        }
        $trackings = $this->fetchTable('TimeTrackings')->find()
            ->select(['created', 'duration'])
            ->where(['created >=' => $first])
            ->all();
        foreach ($trackings as $tracking) {
            $key = $tracking->created->format('Y-m-d');
            if (isset($days[$key])) {
                $days[$key]['hours'] += (float)$tracking->duration;
            }
        }

        return array_values($days);
    }

    /**
     * Offene Tasks laufender Projekte, markierte zuerst. Nur offene: Erledigt
     * wird per Ajax umgeschaltet, die Zeile bleibt bis zum Neuladen ohnehin
     * stehen. Gerade erledigte mitzuzeigen schob sie sonst über die
     * Sortierung nach Änderung an die Spitze und verdrängte die offenen.
     */
    private function nextTasks(): iterable
    {
        return $this->fetchTable('Tasks')->find()
            ->contain(['Services.Projects.Customers', 'TimeTrackings'])
            ->innerJoinWith('Services.Projects', fn($q) => $q->where(['Projects.project_status_id' => self::STATUS_RUNS]))
            ->where(['Tasks.done' => false])
            ->orderBy(['Tasks.marked' => 'DESC', 'Tasks.modified' => 'DESC', 'Tasks.id' => 'DESC'])
            ->limit(self::TASK_LIMIT)
            ->all();
    }

    private function projectsWithStatus(int $statusId): iterable
    {
        return $this->fetchTable('Projects')->find()
            ->contain(['Customers', 'Services.Tasks.TimeTrackings'])
            ->where(['Projects.project_status_id' => $statusId])
            ->orderBy(['Projects.start' => 'DESC', 'Projects.id' => 'DESC'])
            ->all();
    }

    /**
     * Die Projekte mit der jüngsten Zeiterfassung, ohne Erfassung nach
     * Projektbeginn. Bei vielen laufenden Projekten sind das die, an denen
     * gerade gearbeitet wird.
     *
     * @param array<\App\Model\Entity\Project> $projects
     * @return array<\App\Model\Entity\Project>
     */
    private function recentlyWorkedOn(array $projects, int $limit): array
    {
        $lastActivity = [];
        foreach ($projects as $project) {
            $last = $project->start ? $project->start->format('Y-m-d') : '';
            foreach ($project->services as $service) {
                foreach ($service->tasks as $task) {
                    foreach ($task->time_trackings as $tracking) {
                        if ($tracking->created && $tracking->created->format('Y-m-d') > $last) {
                            $last = $tracking->created->format('Y-m-d');
                        }
                    }
                }
            }
            $lastActivity[$project->id] = $last;
        }
        usort($projects, fn($a, $b) => strcmp($lastActivity[$b->id], $lastActivity[$a->id]));

        return array_slice($projects, 0, $limit);
    }

    /**
     * Gestellte, unbezahlte Rechnungen, nach denselben Regeln wie
     * ReportsController::receivables().
     *
     * @return array{count: int, gross: float, oldestDays: int|null}
     */
    private function receivables(): array
    {
        $projects = $this->fetchTable('Projects')->find()
            ->where([
                'invoice_date IS NOT' => null,
                'paid_at IS' => null,
                'project_status_id NOT IN' => self::RECEIVABLE_EXCLUDED_STATUSES,
            ])
            ->contain(['Services.Tasks.TimeTrackings'])
            ->all();
        $today = new \DateTimeImmutable(Date::now()->format('Y-m-d'));
        $count = 0;
        $gross = 0.0;
        $oldestDays = null;
        foreach ($projects as $project) {
            if ($project->costs() <= 0.005) {
                continue;
            }
            $count++;
            $gross += $project->total();
            $days = (int)$today->diff(new \DateTimeImmutable($project->invoice_date->format('Y-m-d')))->days;
            $oldestDays = max($oldestDays ?? 0, $days);
        }

        return ['count' => $count, 'gross' => $gross, 'oldestDays' => $oldestDays];
    }
}
