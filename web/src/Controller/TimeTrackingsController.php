<?php
declare(strict_types=1);

namespace App\Controller;

use Cake\Http\Exception\NotFoundException;
use Cake\I18n\Date;
use Cake\I18n\DateTime;

/**
 * TimeTrackings Controller
 *
 * @property \App\Model\Table\TimeTrackingsTable $TimeTrackings
 * @method \App\Model\Entity\TimeTracking[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class TimeTrackingsController extends AppController
{
    /**
     * Index method
     *
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function index()
    {
        $this->paginate = [
            'order' => ['id' => 'desc'],
            // Ohne diese Liste verwirft CakePHP die Sortierung nach Feldern aus
            // Assoziationen stillschweigend.
            'sortableFields' => ['created', 'duration', 'Tasks.name', 'Services.name', 'Projects.name', 'Customers.shortcut'],
        ];
        $query = $this->TimeTrackings->find()
            ->contain(['Tasks', 'Tasks.Services', 'Tasks.Services.Projects', 'Tasks.Services.Projects.Customers']);
        $timeTrackings = $this->paginate($query);

        $this->set(compact('timeTrackings'));
    }

    /**
     * View method
     *
     * @param string|null $id Time Tracking id.
     * @return \Cake\Http\Response|null|void Renders view
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $timeTracking = $this->TimeTrackings->get($id, [
            'contain' => ['Tasks'],
        ]);

        $this->set(compact('timeTracking'));
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null|void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        // Eine Erfassung ohne Task gibt es nicht. Die Validierung verhindert das
        // zwar, aber der Redirect lief danach trotzdem auf track() mit leerer Id
        // und damit in einen 500er.
        $taskId = $this->request->getQuery("task_id");
        if (!$taskId) {
            $this->Flash->error(__('A time tracking needs a task.'));

            return $this->redirect(['controller' => 'Tasks', 'action' => 'index']);
        }

        $timeTracking = $this->TimeTrackings->newEmptyEntity();
        $timeTracking->task_id = (int)$taskId;
        $timeTracking->duration = 0;
        if (!$this->TimeTrackings->save($timeTracking)) {
            $this->Flash->error(__('Error saving time tracking.'));

            return $this->redirect(['controller' => 'Tasks', 'action' => 'index']);
        }
        $this->Flash->success(__('The time tracking has been saved.'));

        $this->TimeTrackings->deleteAll([ // delete old trackings
            "duration" => 0,
            "created <" => Date::now()->subDays(3)
        ]);

        return $this->redirect(['action' => 'track', $timeTracking->id]);
    }

    /**
     * Die Stoppuhr. Hieß bis zuletzt edit(), was irreführend war: Das Formular
     * lebt von seinem JavaScript, nicht vom Bearbeiten der Felder.
     *
     * @param string|null $id Time Tracking id.
     * @return \Cake\Http\Response|null|void Renders view
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function track($id = null)
    {
        $timeTracking = $this->TimeTrackings->get($id, [
            'contain' => ['Tasks', 'Tasks.Services', 'Tasks.Services.Projects', 'Tasks.Services.Projects.Customers'],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $timeTracking = $this->TimeTrackings->patchEntity($timeTracking, $this->request->getData());
            $timeTracking->set("duration", round($timeTracking->get("duration"), 2));
            if ($this->TimeTrackings->save($timeTracking)) {
                $this->Flash->success(__('The time tracking has been saved.'));
            } else {
                $this->Flash->error(__('The time tracking could not be saved. Please, try again.'));
            }
        }
        // Kein $tasks: Die Stoppuhr hat gar kein Auswahlfeld für den Task, sie
        // lud die Liste bisher umsonst.
        $doneToday = $this->TimeTrackings->find()->where(["created >" => Date::now()])->all()->sumOf('duration');
        $doneTask = $this->TimeTrackings->find()->where(["task_id" => $timeTracking->task_id])->all()->sumOf('duration');
        $this->set(compact('timeTracking', 'doneToday', 'doneTask'));
    }

    /**
     * Normales Bearbeiten-Formular. Damit lassen sich Dauer, Task und der
     * Zeitpunkt einer Erfassung nachträglich korrigieren, ohne die Stoppuhr.
     *
     * @param string|null $id Time Tracking id.
     * @return \Cake\Http\Response|null|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $timeTracking = $this->TimeTrackings->get($id, [
            'contain' => ['Tasks'],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $timeTracking = $this->TimeTrackings->patchEntity($timeTracking, $this->request->getData());
            if ($timeTracking->duration !== null) {
                $timeTracking->duration = round((float)$timeTracking->duration, 2);
            }
            if ($this->TimeTrackings->save($timeTracking)) {
                $this->Flash->success(__('The time tracking has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The time tracking could not be saved. Please, try again.'));
        }
        $tasks = $this->taskList($timeTracking->task_id !== null ? (int)$timeTracking->task_id : null);
        $this->set(compact('timeTracking', 'tasks'));
    }

    /**
     * Taskliste fürs Auswahlfeld, als "KÜRZEL / Projekt / Leistung / Task",
     * beschränkt auf die letzten 500 Tasks aktueller Kunden.
     *
     * @param int|null $ensureId Task, der auf jeden Fall enthalten sein muss.
     * @return array<int, string>
     */
    private function taskList(?int $ensureId = null): array
    {
        // 473 Tasks haben keinen Namen, 71 Leistungen ebenso. Leere Segmente
        // werden weggelassen, statt " / / " zu erzeugen.
        $label = function ($task) {
            $service = $task->service;
            $project = $service->project ?? null;
            $teile = [];
            if ($project && $project->customer) {
                $teile[] = $project->customer->shortcut;
            }
            if ($project) {
                $teile[] = $project->name;
            }
            if ($service && $service->name) {
                $teile[] = $service->name;
            }
            if ($task->name) {
                $teile[] = $task->name;
            }

            return implode(' / ', $teile);
        };

        $tasks = $this->TimeTrackings->Tasks->find('list', [
            'keyField' => 'id',
            'valueField' => $label,
        ])
            ->contain(['Services', 'Services.Projects', 'Services.Projects.Customers'])
            ->where(['Customers.current' => true])
            ->orderBy(['Tasks.id' => 'DESC'])
            ->limit(500)
            ->toArray();

        // Der Task der bearbeiteten Erfassung muss in der Liste stehen, auch wenn
        // er älter ist oder zu einem inaktiven Kunden gehört. Sonst wäre im
        // Select nichts ausgewählt, und ein Speichern würde die Erfassung
        // stillschweigend dem ersten Task zuschlagen. Das beträfe 1822 von 2867
        // Erfassungen.
        if ($ensureId !== null && !isset($tasks[$ensureId])) {
            $task = $this->TimeTrackings->Tasks->find()
                ->contain(['Services', 'Services.Projects', 'Services.Projects.Customers'])
                ->where(['Tasks.id' => $ensureId])
                ->first();
            if ($task) {
                $tasks = [$ensureId => $label($task)] + $tasks;
            }
        }

        return $tasks;
    }

    /**
     * Delete method
     *
     * @param string|null $id Time Tracking id.
     * @return \Cake\Http\Response|null|void Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $timeTracking = $this->TimeTrackings->get($id);
        if ($this->TimeTrackings->delete($timeTracking)) {
            $this->Flash->success(__('The time tracking has been deleted.'));
        } else {
            $this->Flash->error(__('The time tracking could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }

    /**
     * Stundennachweis für ein Projekt, als Beleg zur Rechnung.
     *
     * Früher lief der Export über Kundenkürzel und Monat. Das warf zwei
     * gleichzeitig laufende Projekte desselben Kunden in einen Topf. Der
     * Zuschnitt aufs Projekt trennt sie und braucht keine Datumsgrenzen mehr.
     *
     * @param string|null $projectId Project id.
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function export($projectId = null)
    {
        // Die Route lief früher über Kundenkürzel und Monat. Ein altes
        // Lesezeichen wie /export/FUZ/2026-02 soll einen sauberen 404 liefern
        // statt am Typcast der Id zu zerschellen.
        if (!is_numeric($projectId)) {
            throw new NotFoundException(__('Project not found.'));
        }

        // Services samt Tasks und Trackings, damit der Nachweis je Leistung die
        // erfasste und die abgerechnete Zeit gegenüberstellen kann.
        $project = $this->TimeTrackings->Tasks->Services->Projects->get($projectId, [
            'contain' => ['Customers', 'Services.Tasks.TimeTrackings']
        ]);

        $timeTrackings = $this->TimeTrackings->find()
            ->contain(['Tasks', 'Tasks.Services', 'Tasks.Services.Projects', 'Tasks.Services.Projects.Customers'])
            ->where(['Projects.id' => $project->id])
            ->orderBy(['TimeTrackings.created' => 'asc'])
            ->all();

        $totalDuration = $timeTrackings->sumOf('duration');
        $showPagination = false;

        $this->viewBuilder()->setLayout('print');
        $this->set(compact('timeTrackings', 'showPagination', 'totalDuration', 'project'));
        $this->render('export');
    }
}
