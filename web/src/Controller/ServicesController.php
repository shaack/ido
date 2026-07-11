<?php
declare(strict_types=1);

namespace App\Controller;

use Cake\I18n\DateTime;

/**
 * Services Controller
 *
 * @property \App\Model\Table\ServicesTable $Services
 * @method \App\Model\Entity\Service[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class ServicesController extends AppController
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
            // Assoziationen stillschweigend, der Klick auf die Spalte tut dann
            // einfach nichts.
            'sortableFields' => ['name', 'Customers.shortcut', 'Projects.name'],
        ];
        $query = $this->Services->find()
            ->contain(['Projects', 'Projects.Customers', 'Tasks', 'Tasks.TimeTrackings'])
            ->where(['project_status_id' => 15]);
        $services = $this->paginate($query);

        $this->set(compact('services'));
    }

    /**
     * View method
     *
     * @param string|null $id Service id.
     * @return \Cake\Http\Response|null|void Renders view
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $service = $this->Services->get($id, [
            'contain' => ['Projects.Customers', 'Projects', 'Tasks' => ['sort' => ['done ASC', 'id DESC']], 'Tasks.TimeTrackings']
        ]);

        $this->set(compact('service'));
    }

    public function timesheet($id = null)
    {
        $service = $this->Services->get($id, [
            'contain' => ['Projects.Customers', 'Projects', 'Tasks' => ['sort' => ['done ASC', 'id DESC']], 'Tasks.TimeTrackings']
        ]);
        $this->viewBuilder()->setLayout('print');
        $this->set(compact('service'));
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null|void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $service = $this->Services->newEmptyEntity();
        if ($this->request->is('post')) {
            $service = $this->Services->patchEntity($service, $this->request->getData());
            if ($this->Services->save($service)) {
                $this->Flash->success(__('The service has been saved.'));
            } else {
                $this->Flash->error(__('The service could not be saved. Please, try again.'));
            }
        } else {
            $service->project_id = $this->request->getQuery("project_id"); // shaack patch
        }
        $projects = $this->projectList($service->project_id !== null ? (int)$service->project_id : null);
        // $project wird nur für die Breadcrumb im Layout gebraucht. Ohne
        // project_id in der URL lief get(null) bisher in einen 500er.
        $projectId = $this->request->getQuery("project_id");
        $project = $projectId ? $this->Services->Projects->get($projectId, ["contain" => ["Customers"]]) : null;
        $this->set(compact('service', 'project', 'projects'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Service id.
     * @return \Cake\Http\Response|null|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $service = $this->Services->get($id, [
            'contain' => ['Projects', 'Projects.Customers'],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $service = $this->Services->patchEntity($service, $this->request->getData());
            if ($this->Services->save($service)) {
                $this->Flash->success(__('The service has been saved.'));
                // return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error(__('The service could not be saved. Please, try again.'));
            }
        }
        $projects = $this->projectList($service->project_id !== null ? (int)$service->project_id : null);
        $this->set(compact('service', 'projects'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Service id.
     * @return \Cake\Http\Response|null|void Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $service = $this->Services->get($id);
        if ($this->Services->delete($service)) {
            $this->Flash->success(__('The service has been deleted.'));
        } else {
            $this->Flash->error(__('The service could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'view', 'controller' => 'projects', $service->project_id]);
    }

    /**
     * Projektliste fürs Auswahlfeld.
     *
     * Eine Leistung von einem Kunden zu einem anderen zu verschieben ergibt
     * keinen Sinn. Steht der Kunde fest, zeigt die Liste deshalb nur dessen
     * Projekte, und der Projektname allein genügt. Der größte Kunde hat 154
     * Projekte, das passt.
     *
     * @param int|null $projectId Projekt der Leistung, daraus folgt der Kunde.
     * @return array<int, string>
     */
    private function projectList(?int $projectId = null): array
    {
        $current = $projectId !== null
            ? $this->Services->Projects->find()
                ->contain(['Customers'])
                ->where(['Projects.id' => $projectId])
                ->first()
            : null;

        if ($current && $current->customer_id !== null) {
            return $this->Services->Projects->find('list', [
                'keyField' => 'id',
                'valueField' => 'name',
            ])
                ->where(['Projects.customer_id' => $current->customer_id])
                ->orderBy(['Projects.id' => 'DESC'])
                ->toArray();
        }

        // Kein Kunde bekannt: beim Anlegen ohne project_id, oder bei den zwei
        // Projekten, die gar keinen Kunden haben. Dann die letzten 100 Projekte
        // aktueller Kunden, und hier hilft das Kürzel sehr wohl, weil
        // "Leistungen 2026-07" bei mehreren Kunden vorkommt.
        $label = fn($project) => $project->customer
            ? $project->customer->shortcut . ' / ' . $project->name
            : $project->name;

        $projects = $this->Services->Projects->find('list', [
            'keyField' => 'id',
            'valueField' => $label,
        ])
            ->contain(['Customers'])
            ->where(['Customers.current' => true])
            ->orderBy(['Projects.id' => 'DESC'])
            ->limit(100)
            ->toArray();

        // Das Projekt der bearbeiteten Leistung muss in der Liste stehen, sonst
        // wäre im Select nichts ausgewählt und ein Speichern würde die Leistung
        // stillschweigend dem ersten Eintrag zuschlagen.
        if ($current && !isset($projects[$projectId])) {
            $projects = [$projectId => $label($current)] + $projects;
        }

        return $projects;
    }
}
