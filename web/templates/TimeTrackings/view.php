<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\TimeTracking $timeTracking
 */
?>
<div class="row">
    <aside class="column">
        <div class="actions">
            <?= $this->Html->link(__('Edit Time Tracking'), ['action' => 'edit', $timeTracking->id]) ?>
            <?= $this->Html->link(__('Track'), ['action' => 'track', $timeTracking->id]) ?>
            <?= $this->Form->postLink(__('Delete Time Tracking'), ['action' => 'delete', $timeTracking->id], ['confirm' => __('Are you sure you want to delete # {0}?', $timeTracking->id)]) ?>
            <?= $this->Html->link(__('List Time Trackings'), ['action' => 'index']) ?>
            <?= $this->Html->link(__('New Time Tracking'), ['action' => 'add']) ?>
        </div>
    </aside>
    <div class="column-responsive column-80">
        <div class="timeTrackings view content">
            <h3><?= h($timeTracking->id) ?></h3>
            <table>
                <tr>
                    <th><?= __('Task') ?></th>
                    <td><?= $timeTracking->hasValue('task') ? $this->Html->link($timeTracking->task->name, ['controller' => 'Tasks', 'action' => 'view', $timeTracking->task->id]) : '' ?></td>
                </tr>
                <tr>
                    <th><?= __('Id') ?></th>
                    <td><?= $this->Number->format($timeTracking->id) ?></td>
                </tr>
                <tr>
                    <th><?= __('Duration') ?></th>
                    <td><?= $this->Effort->hours($timeTracking->duration) ?></td>
                </tr>
                <tr>
                    <th><?= __('Created') ?></th>
                    <td><?= h($timeTracking->created) ?></td>
                </tr>
            </table>
        </div>
    </div>
</div>
