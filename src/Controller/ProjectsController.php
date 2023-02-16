<?php
declare(strict_types=1);

namespace App\Controller;

use Cake\I18n\FrozenDate;

/**
 * Projects Controller
 *
 * @property \App\Model\Table\ProjectsTable $Projects
 * @method \App\Model\Entity\Project[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class ProjectsController extends AppController
{
    /**
     * Index method
     *
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function index()
    {
        $current = $this->request->getQuery("current", false);
        $options = ['conditions' => ['OR' => [
            ['Projects.project_status_id' => 5],
            ['Projects.project_status_id' => 10],
            ['Projects.project_status_id' => 15],
            ['Projects.project_status_id' => 20],
            ['Projects.project_status_id' => 25],
            ['Projects.project_status_id' => 30],
            ['Projects.project_status_id' => 35]
        ]]];
        if($current) {
            $this->paginate = [
                'contain' => ['Customers', 'ParentProjects', 'ProjectStatuses', 'Services', 'Services.Tasks', 'Services.Tasks.TimeTrackings'],
                'order' => ['invoice_number' => 'asc', 'project_status_id' => 'asc', 'id' => 'asc']
            ];
        } else {
            $this->paginate = [
                'contain' => ['Customers', 'ParentProjects', 'ProjectStatuses', 'Services', 'Services.Tasks', 'Services.Tasks.TimeTrackings'],
                'order' => ['id' => 'desc']
            ];
        }
        $projects = $this->paginate($this->Projects, $current ? $options : []);

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
            'contain' => ['Customers', 'ParentProjects', 'ProjectStatuses', 'ChildProjects',
                'Services' => ['sort' => ['sort desc', 'Services.id desc']], 'Services.Projects.Customers', 'Services.Tasks', 'Services.Tasks.TimeTrackings'],
        ]);

        $this->set(compact('project'));
    }

    public function offer($id) {
        $project = $this->Projects->get($id, [
            'contain' => ['Customers', 'ParentProjects', 'ProjectStatuses', 'ChildProjects',
                'Services' => ['sort' => ['sort desc', 'Services.id asc']], 'Services.Projects.Customers', 'Services.Tasks', 'Services.Tasks.TimeTrackings'],
        ]);
        $this->viewBuilder()->setLayout('print');
        $this->set(compact('project'));
    }

    /**
     *
     */
    public function invoice($id) {
        $project = $this->Projects->get($id, [
            'contain' => ['Customers', 'ParentProjects', 'ProjectStatuses', 'ChildProjects',
                'Services' => ['sort' => ['sort desc', 'Services.id asc']], 'Services.Projects.Customers', 'Services.Tasks', 'Services.Tasks.TimeTrackings'],
        ]);
        if ($this->request->is(['post', 'patch', 'put'])) {
            $project = $this->Projects->patchEntity($project, $this->request->getData());
            if ($this->Projects->save($project)) {
                $this->Flash->success(__('The Invoice has been saved.'));
            } else {
                $this->Flash->error(__('The project could not be saved. Please, try again.'));
            }
        }
        $invoiceStored = true;
        if($project && !$project->invoice_number) {
            $latestInvoiceNumber = $this->Projects->find('all', [
                'fields' => ['amount' => 'MAX(Projects.invoice_number)']
            ])->first()->amount;
            $project->invoice_number = $latestInvoiceNumber + 1;
            $project->invoice_date = new FrozenDate();
            $invoiceStored = false;
            if(!$project->end) {
                $project->end = new FrozenDate();
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
        } else {
            $project->customer_id = $this->request->getQuery("customer_id");
        }
        $customers = $this->Projects->Customers->find('list', ['limit' => 1000])->all();
        $parentProjects = $this->Projects->ParentProjects->find('list', ['limit' => 1000, 'order' => ['id' => 'DESC']])->all();
        $projectStatuses = $this->Projects->ProjectStatuses->find('list', ['limit' => 200])->all();
        $this->set(compact('project', 'customers', 'parentProjects', 'projectStatuses'));
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
            'contain' => [],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $project = $this->Projects->patchEntity($project, $this->request->getData());
            if ($this->Projects->save($project)) {
                $this->Flash->success(__('The project has been saved.'));
            } else {
                $this->Flash->error(__('The project could not be saved. Please, try again.'));
            }
        }
        $customers = $this->Projects->Customers->find('list', ['limit' => 1000])->where(['Customers.current =' => true]);
        $parentProjects = $this->Projects->ParentProjects->find('list', ['limit' => 1000, 'order' => ['id' => 'DESC']])->all();
        $projectStatuses = $this->Projects->ProjectStatuses->find('list', ['limit' => 200])->all();
        $this->set(compact('project', 'customers', 'parentProjects', 'projectStatuses'));
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
