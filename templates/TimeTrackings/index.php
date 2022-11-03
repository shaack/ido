<?php
/**
 * @var \App\View\AppView $this
 * @var iterable<\App\Model\Entity\TimeTracking> $timeTrackings
 */
?>
<div class="timeTrackings index content">
    <?= $this->Html->link(__('New Time Tracking'), ['action' => 'add'], ['class' => 'button float-right']) ?>
    <h3><?= __('Time Trackings') ?></h3>
    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th><?= $this->Paginator->sort('id') ?></th>
                    <th><?= $this->Paginator->sort('task_id') ?></th>
                    <th><?= $this->Paginator->sort('start') ?></th>
                    <th><?= $this->Paginator->sort('pause') ?></th>
                    <th><?= $this->Paginator->sort('duration') ?></th>
                    <th class="actions"><?= __('Actions') ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($timeTrackings as $timeTracking): ?>
                <tr>
                    <td><?= $this->Number->format($timeTracking->id) ?></td>
                    <td><?= $timeTracking->has('task') ? $this->Html->link($timeTracking->task->name, ['controller' => 'Tasks', 'action' => 'view', $timeTracking->task->id]) : '' ?></td>
                    <td><?= h($timeTracking->start) ?></td>
                    <td><?= $timeTracking->pause === null ? '' : $this->Number->format($timeTracking->pause) ?></td>
                    <td><?= $timeTracking->duration === null ? '' : $this->Number->format($timeTracking->duration) ?></td>
                    <td class="actions">
                        <?= $this->Html->link(__('View'), ['action' => 'view', $timeTracking->id]) ?>
                        <?= $this->Html->link(__('Edit'), ['action' => 'edit', $timeTracking->id]) ?>
                        <?= $this->Form->postLink(__('Delete'), ['action' => 'delete', $timeTracking->id], ['confirm' => __('Are you sure you want to delete # {0}?', $timeTracking->id)]) ?>
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
