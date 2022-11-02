<?php
declare(strict_types=1);

namespace App\Controller;

/**
 * TimeTracking Controller
 *
 * @property \App\Model\Table\TimeTrackingTable $TimeTracking
 * @method \App\Model\Entity\TimeTracking[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class TimeTrackingController extends AppController
{
    /**
     * Index method
     *
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function index()
    {
        $timeTracking = $this->paginate($this->TimeTracking);

        $this->set(compact('timeTracking'));
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
        $timeTracking = $this->TimeTracking->get($id, [
            'contain' => [],
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
        $timeTracking = $this->TimeTracking->newEmptyEntity();
        if ($this->request->is('post')) {
            $timeTracking = $this->TimeTracking->patchEntity($timeTracking, $this->request->getData());
            if ($this->TimeTracking->save($timeTracking)) {
                $this->Flash->success(__('The time tracking has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The time tracking could not be saved. Please, try again.'));
        }
        $this->set(compact('timeTracking'));
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
        $timeTracking = $this->TimeTracking->get($id, [
            'contain' => [],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $timeTracking = $this->TimeTracking->patchEntity($timeTracking, $this->request->getData());
            if ($this->TimeTracking->save($timeTracking)) {
                $this->Flash->success(__('The time tracking has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The time tracking could not be saved. Please, try again.'));
        }
        $this->set(compact('timeTracking'));
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
        $timeTracking = $this->TimeTracking->get($id);
        if ($this->TimeTracking->delete($timeTracking)) {
            $this->Flash->success(__('The time tracking has been deleted.'));
        } else {
            $this->Flash->error(__('The time tracking could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
