<?php
declare(strict_types=1);

namespace App\Controller;

use Cake\I18n\Date;
use Cake\I18n\DateTime;
use DateInterval;

/**
 * Tasks Controller
 *
 * @property \App\Model\Table\TasksTable $Tasks
 * @method \App\Model\Entity\Task[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class TasksController extends AppController
{
    /**
     * Index method
     *
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function index()
    {
        $filter = $this->request->getQuery("filter");

        // Projektfilter: steht ein project_id in der URL, merken wir ihn für die
        // Session (leerer Wert räumt ihn wieder ab). Ohne URL-Parameter gilt der
        // gemerkte Wert weiter.
        $session = $this->request->getSession();
        if ($this->request->getQuery('project_id') !== null) {
            $chosen = $this->request->getQuery('project_id');
            $session->write('Tasks.projectId', $chosen === '' ? null : (int)$chosen);
        }
        $projectId = $session->read('Tasks.projectId');

        $this->paginate = [
            'order' => ['marked' => 'desc', 'id' => 'asc'],
            'maxLimit' => 1000,
            'limit' => 1000,
            // Ohne diese Liste verwirft CakePHP die Sortierung nach Feldern aus
            // Assoziationen stillschweigend.
            'sortableFields' => ['name', 'marked', 'Customers.shortcut', 'Services.name', 'Projects.name'],
        ];
        $now = DateTime::now();
        // Offene Tasks, plus die, die vor weniger als einer Stunde erledigt
        // wurden, damit ein Klick auf "done" die Zeile nicht sofort wegreißt.
        $conditions = ['OR' => [['done =' => false], ['done_at >' => $now->sub(new DateInterval("PT1H"))]]];
        if($filter == "customers") {
            $conditions[] = ['Customers.shortcut !=' => 'SHA'];
        }

        // Auswahlliste für den Filter: die Projekte, die in der ungefilterten
        // Liste vorkommen. So lässt sich der Filter jederzeit wieder wechseln.
        $projectOptions = [];
        $base = $this->Tasks->find()
            ->contain(['Services.Projects' => ['conditions' => ['project_status_id' => '15']], 'Services.Projects.Customers'])
            ->where($conditions);
        foreach ($base->all() as $t) {
            $project = $t->service->project ?? null;
            if ($project !== null) {
                $shortcut = $project->customer->shortcut ?? '';
                $projectOptions[$project->id] = trim($shortcut . ' / ' . $project->name, ' /');
            }
        }
        asort($projectOptions);

        // Der gemerkte Projektfilter greift nur, solange das Projekt noch in der
        // Liste steht, sonst räumen wir ihn ab.
        if ($projectId !== null && !isset($projectOptions[$projectId])) {
            $projectId = null;
            $session->delete('Tasks.projectId');
        }
        if ($projectId !== null) {
            $conditions[] = ['Services.project_id' => $projectId];
        }

        $query = $this->Tasks->find()
            ->contain(['Services', 'Services.Projects' => ['conditions' => ['project_status_id' => '15']], 'Services.Projects.Customers', 'TimeTrackings'])
            ->where($conditions);
        $tasks = $this->paginate($query);

        $oneDayInterval = new DateInterval("P1D");

        $day = Date::now();
        $dayBefore = $day->add($oneDayInterval);

        // time trackings today
        $doneToday = $this->Tasks->TimeTrackings->find()->where(["created >" => $day])->all()->sumOf('duration');
        $day = $day->sub($oneDayInterval);
        $dayBefore = $dayBefore->sub($oneDayInterval);
        $done1 = $this->Tasks->TimeTrackings->find()->where(["created >" => $day])->where(["created <" => $dayBefore])->all()->sumOf('duration');
        $day = $day->sub($oneDayInterval);
        $dayBefore = $dayBefore->sub($oneDayInterval);
        $done2 = $this->Tasks->TimeTrackings->find()->where(["created >" => $day])->where(["created <" => $dayBefore])->all()->sumOf('duration');
        $day = $day->sub($oneDayInterval);
        $dayBefore = $dayBefore->sub($oneDayInterval);
        $done3 = $this->Tasks->TimeTrackings->find()->where(["created >" => $day])->where(["created <" => $dayBefore])->all()->sumOf('duration');

        $this->set(compact('tasks', 'doneToday', 'done1', 'done2', 'done3', 'projectOptions', 'projectId'));
    }

    /**
     * View method
     *
     * @param string|null $id Task id.
     * @return \Cake\Http\Response|null|void Renders view
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $task = $this->Tasks->get($id, [
            'contain' => ['Services.Projects.Customers', 'Services.Projects', 'Services', 'TimeTrackings' => [
                'sort' => ['id' => 'DESC']
            ]],
        ]);

        $this->set(compact('task'));
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null|void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $task = $this->Tasks->newEmptyEntity();
        if ($this->request->is('post')) {
            $task = $this->Tasks->patchEntity($task, $this->request->getData());
            if ($this->Tasks->save($task)) {
                $this->Flash->success(__('The task has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The task could not be saved. Please, try again.'));
        } else {
            $task->service_id = $this->request->getQuery("service_id"); // shaack patch
        }
        $services = $this->serviceList($task->service_id !== null ? (int)$task->service_id : null);
        // $service wird nur für die Breadcrumb im Layout gebraucht. Ohne
        // service_id in der URL lief get(null) bisher in einen 500er.
        $serviceId = $this->request->getQuery("service_id");
        $service = $serviceId
            ? $this->Tasks->Services->get($serviceId, ["contain" => ["Projects", "Projects.Customers"]])
            : null;
        $this->set(compact('task', 'service', 'services'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Task id.
     * @return \Cake\Http\Response|null|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        // Die Kette wird für die Breadcrumb im Layout gebraucht. Ohne sie stand
        // dort nichts, anders als in der Task-Ansicht.
        $task = $this->Tasks->get($id, [
            'contain' => ['Services', 'Services.Projects', 'Services.Projects.Customers'],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $task = $this->Tasks->patchEntity($task, $this->request->getData());
            if ($this->Tasks->save($task)) {
                $this->Flash->success(__('The task has been saved.'));
                // return $this->redirect(['action' => 'index']); // remove this
            } else {
                $this->Flash->error(__('The task could not be saved. Please, try again.'));
            }
        }
        $services = $this->serviceList($task->service_id !== null ? (int)$task->service_id : null);
        $this->set(compact('task', 'services'));
    }

    public function done($id)
    {
        $done = $this->request->getQuery("done", null);
        $task = $this->Tasks->get($id);
        $task->done = $done;
        $task->done_at = DateTime::now();
        $this->Tasks->save($task);
        $this->redirect(["action" => "index"]);
    }

    public function marked($id)
    {
        $marked = $this->request->getQuery("marked", 0);
        $task = $this->Tasks->get($id);
        $task->marked = $marked;
        $this->Tasks->save($task);
        $this->redirect(["action" => "index"]);
    }

    /**
     * Delete method
     *
     * @param string|null $id Task id.
     * @return \Cake\Http\Response|null|void Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $task = $this->Tasks->get($id);
        if ($this->Tasks->delete($task)) {
            $this->Flash->success(__('The task has been deleted.'));
        } else {
            $this->Flash->error(__('The task could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'view', 'controller' => 'services', $task->service_id]);
    }

    /**
     * Leistungsliste fürs Auswahlfeld.
     *
     * Einen Task in ein anderes Projekt zu verschieben ergibt keinen Sinn. Steht
     * das Projekt fest, zeigt die Liste deshalb nur dessen Leistungen, und der
     * Leistungsname allein genügt. Das größte Projekt hat 16 Leistungen.
     *
     * @param int|null $serviceId Leistung des Tasks, daraus folgt das Projekt.
     * @return array<int, string>
     */
    private function serviceList(?int $serviceId = null): array
    {
        // Kein Projekt hat mehr als eine namenlose Leistung, der Platzhalter ist
        // innerhalb eines Projekts also eindeutig.
        $nameOnly = fn($service) => $service->name ?: '[ohne Namen]';

        $current = $serviceId !== null
            ? $this->Tasks->Services->find()
                ->contain(['Projects', 'Projects.Customers'])
                ->where(['Services.id' => $serviceId])
                ->first()
            : null;

        if ($current && $current->project_id !== null) {
            return $this->Tasks->Services->find('list', [
                'keyField' => 'id',
                'valueField' => $nameOnly,
            ])
                ->where(['Services.project_id' => $current->project_id])
                ->orderBy(['Services.id' => 'DESC'])
                ->toArray();
        }

        // Kein Projekt bekannt, also beim Anlegen ohne service_id. Dann die
        // letzten 500 Leistungen aktueller Kunden, und hier hilft der volle Pfad
        // sehr wohl, weil Leistungsnamen projektübergreifend nichtssagend sind.
        $fullPath = function ($service) {
            $project = $service->project;
            $teile = [];
            if ($project && $project->customer) {
                $teile[] = $project->customer->shortcut;
            }
            if ($project) {
                $teile[] = $project->name;
            }
            if ($service->name) {
                $teile[] = $service->name;
            }

            return implode(' / ', $teile);
        };

        $services = $this->Tasks->Services->find('list', [
            'keyField' => 'id',
            'valueField' => $fullPath,
        ])
            ->contain(['Projects', 'Projects.Customers'])
            ->where(['Customers.current' => true])
            ->orderBy(['Services.id' => 'DESC'])
            ->limit(500)
            ->toArray();

        // Die Leistung des bearbeiteten Tasks muss in der Liste stehen, sonst wäre
        // im Select nichts ausgewählt und ein Speichern würde den Task
        // stillschweigend der ersten Leistung zuschlagen.
        if ($current && !isset($services[$serviceId])) {
            $services = [$serviceId => $fullPath($current)] + $services;
        }

        return $services;
    }
}
