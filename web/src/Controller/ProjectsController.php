<?php
declare(strict_types=1);

namespace App\Controller;

use Cake\I18n\Date;

/**
 * Projects Controller
 *
 * @property \App\Model\Table\ProjectsTable $Projects
 * @method \App\Model\Entity\Project[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class ProjectsController extends AppController
{
    /**
     * Ohne diese Liste verwirft CakePHP die Sortierung nach Feldern aus
     * Assoziationen stillschweigend, der Klick auf die Spalte tut dann nichts.
     */
    private const SORTABLE_FIELDS = [
        'name', 'invoice_number', 'project_status_id', 'start', 'end', 'invoice_date',
        'Customers.shortcut',
    ];

    /**
     * Index method
     *
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function index()
    {
        $current = $this->request->getQuery("current", false);
        $query = $this->Projects->find()->contain([
            'Customers', 'ProjectStatuses', 'Services', 'Services.Tasks', 'Services.Tasks.TimeTrackings'
        ]);
        if ($current) {
            $query->where(['Projects.project_status_id IN' => [5, 10, 15, 20, 25, 30, 35]]);
            $this->paginate = [
                'order' => ['invoice_number' => 'asc', 'project_status_id' => 'asc', 'id' => 'asc'],
                'sortableFields' => self::SORTABLE_FIELDS,
            ];
        } else {
            $this->paginate = [
                'order' => ['id' => 'desc'],
                'sortableFields' => self::SORTABLE_FIELDS,
            ];
        }
        $projects = $this->paginate($query);

        $this->set(compact('projects'));
    }

    /**
     * View method
     *
     * @param string|null $id Project id.
     * @return \Cake\Http\Response|null|void Renders view
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $project = $this->Projects->get($id, [
            'contain' => ['Customers', 'ProjectStatuses',
                'Services' => ['sort' => ['sort desc', 'Services.id asc']], 'Services.Projects.Customers', 'Services.Tasks', 'Services.Tasks.TimeTrackings'],
        ]);
        $this->set(compact('project'));
    }

    public function offer($id)
    {
        $project = $this->Projects->get($id, [
            'contain' => ['Customers', 'ProjectStatuses',
                'Services' => ['sort' => ['sort desc', 'Services.id asc']], 'Services.Projects.Customers', 'Services.Tasks', 'Services.Tasks.TimeTrackings'],
        ]);
        $this->viewBuilder()->setLayout('print');
        $this->set(compact('project'));
    }

    public function invoice($id)
    {
        $project = $this->Projects->get($id, [
            'contain' => ['Customers', 'ProjectStatuses',
                'Services' => ['sort' => ['sort desc', 'Services.id asc']], 'Services.Projects.Customers', 'Services.Tasks', 'Services.Tasks.TimeTrackings'],
        ]);
        if ($this->request->is(['post', 'patch', 'put'])) {
            $project = $this->Projects->patchEntity($project, $this->request->getData());
            $project->project_status = $this->Projects->ProjectStatuses->get(25);
            if ($this->Projects->save($project)) {
                $this->Flash->success(__('The Invoice has been saved.'));
            } else {
                $this->Flash->error(__('The project could not be saved. Please, try again.'));
            }
        }
        $invoiceStored = true;
        if ($project && !$project->invoice_number) {
            $latestInvoiceNumber = $this->Projects->find('all', [
                'fields' => ['amount' => 'MAX(Projects.invoice_number)']
            ])->first()->amount;
            $project->invoice_number = $latestInvoiceNumber + 1;
            $project->invoice_date = new Date();
            $invoiceStored = false;
            if (!$project->end) {
                $project->end = new Date();
            }
        }
        $this->viewBuilder()->setLayout('print');
        $this->set(compact('project', 'invoiceStored'));
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null|void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $project = $this->Projects->newEmptyEntity();
        if ($this->request->is('post')) {
            $project = $this->Projects->patchEntity($project, $this->request->getData());
            if ($this->Projects->save($project)) {
                $this->Flash->success(__('The project has been saved.'));
                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The project could not be saved. Please, try again.'));
        }
        $customers = $this->customerList();

        // Stundensatz je Kunde, damit das Formular ihn beim Wechsel des Kunden
        // direkt einträgt. Hier stand vorher
        // $project->hourly_rate = $project->customer->hourly_rate. Das war
        // wirkungslos, denn geladen wurde nie der Kunde, nur customer_id
        // gesetzt. Die Zeile las eine Eigenschaft auf null und schrieb null.
        $customerRates = $this->Projects->Customers->find()
            ->select(['id', 'hourly_rate'])
            ->all()
            ->combine('id', 'hourly_rate')
            ->toArray();

        if (!$this->request->is('post')) {
            $project->customer_id = $this->request->getQuery("customer_id");
            if ($project->customer_id) {
                $project->hourly_rate = $customerRates[(int)$project->customer_id] ?? null;
                // Der Kunde selbst, nicht nur seine Id. Sonst kann das Layout die
                // Breadcrumb nicht bauen.
                $project->customer = $this->Projects->Customers->get((int)$project->customer_id);
            }
            $project->start = new Date();
            // Das Auswahlfeld hängt an project_status_id. Hier stand vorher
            // $project->project_status = ...->get(15), was nur das
            // Assoziationsobjekt setzte und im Formular nichts vorauswählte.
            $project->project_status_id = 15; // runs
        }

        $projectStatuses = $this->Projects->ProjectStatuses->find('list', ['limit' => 200])->all();
        $this->set(compact('project', 'customers', 'projectStatuses', 'customerRates'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Project id.
     * @return \Cake\Http\Response|null|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $project = $this->Projects->get($id, [
            'contain' => ['Customers'],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $project = $this->Projects->patchEntity($project, $this->request->getData());
            if ($this->request->getData()["paid_at"] && $project->project_status_id == 25) {
                $project->project_status = $this->Projects->ProjectStatuses->get(40); // invoice paid
            }
            if ($this->Projects->save($project)) {
                $this->Flash->success(__('The project has been saved.'));
            } else {
                $this->Flash->error(__('The project could not be saved. Please, try again.'));
            }
        }
        $customers = $this->customerList(true);
        $projectStatuses = $this->Projects->ProjectStatuses->find('list', ['limit' => 200])->all();
        $this->set(compact('project', 'customers', 'projectStatuses'));
    }

    /**
     * Kundenliste fürs Auswahlfeld, als "KÜRZEL - Kundenname" und nach Kürzel
     * sortiert. Der displayField von Customers ist name, das Kürzel fehlte
     * deshalb, obwohl man in der ganzen App danach sucht.
     *
     * @param bool $onlyCurrent Nur aktuelle Kunden, für das Bearbeiten-Formular.
     * @return \Cake\Datasource\ResultSetInterface
     */
    private function customerList(bool $onlyCurrent = false)
    {
        $query = $this->Projects->Customers->find('list', [
            'keyField' => 'id',
            // Ohne Kürzel bliebe sonst ein führendes " - " stehen.
            'valueField' => fn($customer) => $customer->shortcut
                ? $customer->shortcut . ' - ' . $customer->name
                : $customer->name,
        ])->orderBy(['Customers.shortcut' => 'asc']);

        if ($onlyCurrent) {
            $query->where(['Customers.current' => true]);
        }

        return $query->all();
    }

    /**
     * Delete method
     *
     * @param string|null $id Project id.
     * @return \Cake\Http\Response|null|void Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $project = $this->Projects->get($id);
        if ($this->Projects->delete($project)) {
            $this->Flash->success(__('The project has been deleted.'));
        } else {
            $this->Flash->error(__('The project could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
