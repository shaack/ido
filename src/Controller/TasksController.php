<?php
declare(strict_types=1);

namespace App\Controller;

use Cake\Chronos\Date;
use Cake\I18n\FrozenDate;
use Cake\I18n\FrozenTime;
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
        $this->paginate = [
            'contain' => ['Services', 'Services.Projects' => ['conditions' => ['project_status_id' => '15']], 'Services.Projects.Customers', 'TimeTrackings'],
            'order' => ['marked' => 'desc', 'prio' => 'desc', 'id' => 'asc']
        ];
        $now = FrozenDate::now();
        $oneDayInterval = new DateInterval("P1D");
        $yesterday = $now->sub($oneDayInterval);
        $conditions = [['OR' => [['done =' => false], ['done_at >' => $now]]]];
        if($filter == "customers") {
            $conditions[] = ['Customers.shortcut !=' => 'SHA'];
        }
        $options = ['maxLimit' => 1000, 'limit' => 1000, 'conditions' => $conditions];
        $tasks = $this->paginate($this->Tasks, $options);

        $day = FrozenDate::now();
        $dayBefore = $day->add($oneDayInterval);

        // time trackings today
        $doneToday = $this->Tasks->TimeTrackings->find()->where(["created >" => $day])->sumOf('duration');
        $day = $day->sub($oneDayInterval);
        $dayBefore = $dayBefore->sub($oneDayInterval);
        $done1 = $this->Tasks->TimeTrackings->find()->where(["created >" => $day])->where(["created <" => $dayBefore])->sumOf('duration');
        $day = $day->sub($oneDayInterval);
        $dayBefore = $dayBefore->sub($oneDayInterval);
        $done2 = $this->Tasks->TimeTrackings->find()->where(["created >" => $day])->where(["created <" => $dayBefore])->sumOf('duration');
        $day = $day->sub($oneDayInterval);
        $dayBefore = $dayBefore->sub($oneDayInterval);
        $done3 = $this->Tasks->TimeTrackings->find()->where(["created >" => $day])->where(["created <" => $dayBefore])->sumOf('duration');

        $this->set(compact('tasks', 'doneToday', 'done1', 'done2', 'done3'));
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
            'contain' => ['Services', 'TimeTrackings' => [
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
        $services = $this->Tasks->Services->find('list', ['limit' => 1000, 'order' => ['id' => 'DESC']])->all();
        $task->prio = 1;
        $this->set(compact('task', 'services'));
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
        $task = $this->Tasks->get($id, [
            'contain' => [],
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
        $services = $this->Tasks->Services->find('list', ['limit' => 1000, 'order' => ['id' => 'DESC']])->all();
        $this->set(compact('task', 'services'));
    }

    public function done($id)
    {
        $done = $this->request->getQuery("done", null);
        $task = $this->Tasks->get($id);
        $task->done = $done;
        $task->done_at = FrozenTime::now();
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

    public function prio($id)
    {
        $prio = $this->request->getQuery("prio", 0);
        $task = $this->Tasks->get($id);
        $task->prio = $prio;
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

        return $this->redirect(['action' => 'index']);
    }
}
