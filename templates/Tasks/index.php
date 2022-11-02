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
                    <th><?= $this->Paginator->sort('Id') ?></th>
                    <th><?= $this->Paginator->sort('start_est') ?></th>
                    <th><?= $this->Paginator->sort('deadline') ?></th>
                    <th><?= $this->Paginator->sort('duration_est') ?></th>
                    <th><?= $this->Paginator->sort('service') ?></th>
                    <th><?= $this->Paginator->sort('customer') ?></th>
                    <th><?= $this->Paginator->sort('created') ?></th>
                    <th><?= $this->Paginator->sort('start') ?></th>
                    <th><?= $this->Paginator->sort('done') ?></th>
                    <th><?= $this->Paginator->sort('duration') ?></th>
                    <th class="actions"><?= __('Actions') ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($tasks as $task): ?>
                <tr>
                    <td><?= $this->Number->format($task->Id) ?></td>
                    <td><?= h($task->start_est) ?></td>
                    <td><?= h($task->deadline) ?></td>
                    <td><?= $task->duration_est === null ? '' : $this->Number->format($task->duration_est) ?></td>
                    <td><?= $task->service === null ? '' : $this->Number->format($task->service) ?></td>
                    <td><?= $task->customer === null ? '' : $this->Number->format($task->customer) ?></td>
                    <td><?= h($task->created) ?></td>
                    <td><?= h($task->start) ?></td>
                    <td><?= h($task->done) ?></td>
                    <td><?= $task->duration === null ? '' : $this->Number->format($task->duration) ?></td>
                    <td class="actions">
                        <?= $this->Html->link(__('View'), ['action' => 'view', $task->Id]) ?>
                        <?= $this->Html->link(__('Edit'), ['action' => 'edit', $task->Id]) ?>
                        <?= $this->Form->postLink(__('Delete'), ['action' => 'delete', $task->Id], ['confirm' => __('Are you sure you want to delete # {0}?', $task->Id)]) ?>
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
