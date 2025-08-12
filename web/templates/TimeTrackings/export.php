<?php
/**
 * @var \App\View\AppView $this
 * @var iterable<\App\Model\Entity\TimeTracking> $timeTrackings
 */
$fileName = "Time Trackings " . $customerShortcut ." " . $month;
$this->assign('title', $fileName);
?>
<div class="timeTrackings index content">
    <h3 class="mt-5">
        <?= __('Time Trackings') ?>
        <?= $customerShortcut ?>
        <?php if (isset($month) && $month): ?>
            - <?= h($month) ?>
        <?php endif; ?>
    </h3>
    <p>Stefan Haack</p>
    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th><?= __('Created') ?></th>
                    <th><?= __('Task') ?></th>
                    <th><?= __('Service') ?></th>
                    <th><?= __('Project') ?></th>
                    <th class="text-end"><?= __('Duration') ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($timeTrackings as $timeTracking):
                    if(!$timeTracking->duration) {
                        continue;
                    }
                    $customer = $timeTracking->task->service->project->customer;
                    ?>
                <tr>
                    <td><?= $this->Html->link($timeTracking->created, ['action' => 'edit', $timeTracking->id]) ?></td>
                    <td class="<?= $timeTracking->task->name ? "" : "fst-italic" ?>"><?= $timeTracking->has('task') ? $this->Html->link($timeTracking->task->name ?: 'Diverses', ['controller' => 'Tasks', 'action' => 'view', $timeTracking->task->id]) : '' ?></td>
                    <td><?= $this->Html->link($timeTracking->task->service->name, ['controller' => 'Services', 'action' => 'view', $timeTracking->task->service->id]) ?></td>
                    <td><?= $this->Html->link($timeTracking->task->service->project->name, ['controller' => 'Projects', 'action' => 'view', $timeTracking->task->service->project->id]) ?></td>
                    <td class="text-end"><?= $timeTracking->duration === null ? '' : $this->Number->format($timeTracking->duration) ?></td>
                </tr>
                <?php endforeach; ?>

                <?php if (isset($totalDuration) && $totalDuration !== null): ?>
                <tr>
                    <td colspan="4" class="text-end"><strong><?= __('Total') ?></strong></td>
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
