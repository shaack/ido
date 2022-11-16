<?php
/**
 * @var \App\View\AppView $this
 * @var iterable<\App\Model\Entity\TimeTracking> $timeTrackings
 */
?>
<div class="timeTrackings index content">
    <h3><?= __('Time Trackings') ?></h3>
    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <!-- <th><?= $this->Paginator->sort('id') ?></th> -->
                    <th><?= $this->Paginator->sort('start') ?></th>
                    <th><?= $this->Paginator->sort('task_id') ?></th>
                    <th><?= $this->Paginator->sort('customer_id') ?></th>
                    <!-- <th><?= $this->Paginator->sort('pause') ?></th> -->
                    <th class="text-end"><?= $this->Paginator->sort('duration') ?></th>
                    <th class="actions"><?= __('Actions') ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($timeTrackings as $timeTracking): ?>
                <tr>
                    <!-- <td><?= $this->Number->format($timeTracking->id) ?></td> -->
                    <td><?= $this->Html->link($timeTracking->start, ['action' => 'edit', $timeTracking->id]) ?></td>
                    <td><?= $timeTracking->has('task') ? $this->Html->link($timeTracking->task->name ? $timeTracking->task->name : "- - - - -", ['controller' => 'Tasks', 'action' => 'view', $timeTracking->task->id]) : '' ?></td>
                    <?php
                        $customer = $timeTracking->task->service->project->customer;
                    ?>
                    <td><?= $this->Html->link($customer->shortcut, ['controller' => 'Customers', 'action' => 'view', $customer->id], ['style' => 'color: ' . $customer->color]) ?></td>
                    <!-- <td><?= $timeTracking->pause === null ? '' : $this->Number->format($timeTracking->pause) ?></td> -->
                    <td class="text-end"><?= $timeTracking->duration === null ? '' : $this->Number->format($timeTracking->duration) ?></td>
                    <td class="actions">
                        <?= $this->Html->link(__('Edit'), ['action' => 'edit', $timeTracking->id]) ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <div class="paginator">
        <?php if ($this->Paginator->hasPage(2)) { ?>
            <ul class="pagination">
                <?= $this->Paginator->first('<< ' . __('first')) ?>
                <?= $this->Paginator->prev('< ' . __('previous')) ?>
                <?= $this->Paginator->numbers() ?>
                <?= $this->Paginator->next(__('next') . ' >') ?>
                <?= $this->Paginator->last(__('last') . ' >>') ?>
            </ul>
        <?php } ?>
        <p><?= $this->Paginator->counter(__('Page {{page}} of {{pages}}, showing {{current}} record(s) out of {{count}} total')) ?></p>
    </div>
</div>
