<?php
/**
 * @var \App\View\AppView $this
 * @var iterable<\App\Model\Entity\TimeTracking> $timeTrackings
 */
?>
<div class="timeTrackings index content">
    <h3>
        <?= __('Time Trackings') ?>
        <?php if (isset($month) && $month): ?>
            - <?= h($month) ?>
        <?php endif; ?>
    </h3>
    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <?php if (isset($showPagination) && $showPagination): ?>
                        <th><?= $this->Paginator->sort('created') ?></th>
                        <th><?= $this->Paginator->sort('task_id') ?></th>
                        <th><?= $this->Paginator->sort('customer_id') ?></th>
                        <th><?= $this->Paginator->sort('service_id') ?></th>
                        <th><?= $this->Paginator->sort('project_id') ?></th>
                        <th class="text-end"><?= $this->Paginator->sort('duration') ?></th>
                    <?php else: ?>
                        <th><?= __('Created') ?></th>
                        <th><?= __('Task') ?></th>
                        <th><?= __('Customer') ?></th>
                        <th><?= __('Service') ?></th>
                        <th><?= __('Project') ?></th>
                        <th class="text-end"><?= __('Duration') ?></th>
                    <?php endif; ?>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($timeTrackings as $timeTracking):
                    $customer = $timeTracking->task->service->project->customer;
                    ?>
                <tr>
                    <td><?= $this->Html->link($timeTracking->created, ['action' => 'edit', $timeTracking->id]) ?></td>
                    <td class="<?= $timeTracking->task->name ? "" : "fst-italic" ?>"><?= $timeTracking->has('task') ? $this->Html->link($timeTracking->task->name ?: $timeTracking->task->service->name, ['controller' => 'Tasks', 'action' => 'view', $timeTracking->task->id]) : '' ?></td>
                    <td><?= $this->Html->link($customer->shortcut, ['controller' => 'Customers', 'action' => 'view', $customer->id], ['style' => 'color: ' . $customer->color]) ?></td>
                    <td><?= $this->Html->link($this->Text->truncate($timeTracking->task->service->name, 32), ['controller' => 'Services', 'action' => 'view', $timeTracking->task->service->id]) ?></td>
                    <td><?= $this->Html->link($this->Text->truncate($timeTracking->task->service->project->name, 32), ['controller' => 'Projects', 'action' => 'view', $timeTracking->task->service->project->id]) ?></td>
                    <td class="text-end"><?= $timeTracking->duration === null ? '' : $this->Number->format($timeTracking->duration) ?></td>
                </tr>
                <?php endforeach; ?>

                <?php if (isset($totalDuration) && $totalDuration !== null): ?>
                <tr>
                    <td colspan="5" class="text-end"><strong><?= __('Total') ?></strong></td>
                    <td class="text-end"><strong><?= $this->Number->format($totalDuration) ?></strong></td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <?php if (isset($showPagination) && $showPagination): ?>
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
    <?php endif; ?>
</div>
