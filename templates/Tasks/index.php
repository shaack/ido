<?php
/**
 * @var \App\View\AppView $this
 * @var iterable<\App\Model\Entity\Task> $tasks
 */
?>
<div class="tasks index content">
    <?= $this->Html->link(__('New Task'), ['action' => 'add'], ['class' => 'button float-right']) ?>
    <h3><?= __('Tasks') ?></h3>
    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th><?= $this->Paginator->sort('id') ?></th>
                    <th><?= $this->Paginator->sort('marked') ?></th>
                    <!-- <th><?= $this->Paginator->sort('prio') ?></th> -->
                    <th><?= $this->Paginator->sort('name') ?></th>
                    <!-- <th><?= $this->Paginator->sort('start_est') ?></th> -->
                    <th><?= $this->Paginator->sort('deadline') ?></th>
                    <th><?= $this->Paginator->sort('duration_est') ?></th>
                    <!-- <th><?= $this->Paginator->sort('link') ?></th> -->
                    <th><?= $this->Paginator->sort('service_id') ?></th>
                    <th><?= $this->Paginator->sort('project_id') ?></th>
                    <!-- <th><?= $this->Paginator->sort('created') ?></th> -->
                    <th><?= $this->Paginator->sort('done') ?></th>
                    <!-- <th><?= $this->Paginator->sort('done_at') ?></th> -->
                    <th><?= $this->Paginator->sort('duration') ?></th>
                    <th class="actions"><?= __('Actions') ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($tasks as $task): ?>
                <tr>
                    <td><?= $this->Number->format($task->id) ?></td>
                    <td><?= h($task->marked) ?></td>
                    <!-- <td><?= $task->prio === null ? '' : $this->Number->format($task->prio) ?></td> -->
                    <td><?= h($task->name) ?></td>
                    <!-- <td><?= h($task->start_est) ?></td> -->
                    <td><?= h($task->deadline) ?></td>
                    <td><?= $task->duration_est === null ? '' : $this->Number->format($task->duration_est) ?></td>
                    <!-- <td><?= h($task->link) ?></td> -->
                    <td><?= $task->has('service') ? $this->Html->link($task->service->name, ['controller' => 'Services', 'action' => 'view', $task->service->id]) : '' ?></td>
                    <td><?= $task->service->project->name ?></td>
                    <!-- <td><?= h($task->created) ?></td> -->
                    <td><?= h($task->done) ?></td>
                    <!-- <td><?= h($task->done_at) ?></td> -->
                    <td><?= $task->duration === null ? '' : $this->Number->format($task->duration) ?></td>
                    <td class="actions">
                        <?= $this->Html->link(__('View'), ['action' => 'view', $task->id]) ?>
                        <?= $this->Html->link(__('Edit'), ['action' => 'edit', $task->id]) ?>
                        <?= $this->Form->postLink(__('Delete'), ['action' => 'delete', $task->id], ['confirm' => __('Are you sure you want to delete # {0}?', $task->id)]) ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <div class="paginator">
        <ul class="pagination">
            <?= $this->Paginator->first('<< ' . __('first')) ?>
            <?= $this->Paginator->prev('< ' . __('previous')) ?>
            <?= $this->Paginator->numbers() ?>
            <?= $this->Paginator->next(__('next') . ' >') ?>
            <?= $this->Paginator->last(__('last') . ' >>') ?>
        </ul>
        <p><?= $this->Paginator->counter(__('Page {{page}} of {{pages}}, showing {{current}} record(s) out of {{count}} total')) ?></p>
    </div>
</div>