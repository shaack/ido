<?php
declare(strict_types=1);

namespace App\Controller;

use Cake\Chronos\Date;
use Cake\I18n\FrozenTime;

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
            'contain' => ['Tasks', 'Tasks.Services', 'Tasks.Services.Projects', 'Tasks.Services.Projects.Customers'],
            'order' => ['id' => 'desc']
        ];
        $timeTrackings = $this->paginate($this->TimeTrackings);

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
        $timeTracking = $this->TimeTrackings->newEmptyEntity();
        /*
        if ($this->request->is('post')) {
            $timeTracking = $this->TimeTrackings->patchEntity($timeTracking, $this->request->getData());
            if ($this->TimeTrackings->save($timeTracking)) {
                $this->Flash->success(__('The time tracking has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The time tracking could not be saved. Please, try again.'));
        } else {
            $timeTracking->task_id = $this->request->getQuery("task_id"); // shaack patch
            $timeTracking->start = new FrozenTime('now', 'CET'); // shaack patch
        }
        */
        $timeTracking->task_id = $this->request->getQuery("task_id"); // shaack patch
        // $timeTracking->start = new FrozenTime('now', 'CET'); //
        if ($this->TimeTrackings->save($timeTracking)) {
            $this->Flash->success(__('The time tracking has been saved.'));
        } else {
            $this->Flash->error(__('Error saving time tracking.'));
        }
        // $tasks = $this->TimeTrackings->Tasks->find('list', ['limit' => 1000, 'order' => ['id' => 'DESC']])->all();
        // $this->set(compact('timeTracking', 'tasks'));
        return $this->redirect(['action' => 'edit', $timeTracking->id]);
    }

    /**
     * Edit method
     *
     * @param string|null $id Time Tracking id.
     * @return \Cake\Http\Response|null|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
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
        $tasks = $this->TimeTrackings->Tasks->find('list', ['limit' => 1000, 'order' => ['id' => 'DESC']])->all();
        $hideNavigation = true;
        $this->set(compact('timeTracking', 'tasks', 'hideNavigation'));
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
}
