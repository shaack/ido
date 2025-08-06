<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\TimeTracking $timeTracking
 */
?>
<div class="row">
    <aside class="column">
        <div class="side-nav">
            <h4 class="heading"><?= __('Actions') ?></h4>
            <?= $this->Html->link(__('Edit Time Tracking'), ['action' => 'edit', $timeTracking->id], ['class' => 'side-nav-item']) ?>
            <?= $this->Form->postLink(__('Delete Time Tracking'), ['action' => 'delete', $timeTracking->id], ['confirm' => __('Are you sure you want to delete # {0}?', $timeTracking->id), 'class' => 'side-nav-item']) ?>
            <?= $this->Html->link(__('List Time Trackings'), ['action' => 'index'], ['class' => 'side-nav-item']) ?>
            <?= $this->Html->link(__('New Time Tracking'), ['action' => 'add'], ['class' => 'side-nav-item']) ?>
        </div>
    </aside>
    <div class="column-responsive column-80">
        <div class="timeTrackings view content">
            <h3><?= h($timeTracking->id) ?></h3>
            <table>
                <tr>
                    <th><?= __('Task') ?></th>
                    <td><?= $timeTracking->has('task') ? $this->Html->link($timeTracking->task->name, ['controller' => 'Tasks', 'action' => 'view', $timeTracking->task->id]) : '' ?></td>
                </tr>
                <tr>
                    <th><?= __('Id') ?></th>
                    <td><?= $this->Number->format($timeTracking->id) ?></td>
                </tr>
                <tr>
                    <th><?= __('Pause') ?></th>
                    <td><?= $timeTracking->pause === null ? '' : $this->Number->format($timeTracking->pause) ?></td>
                </tr>
                <tr>
                    <th><?= __('Duration') ?></th>
                    <td><?= $timeTracking->duration === null ? '' : $this->Number->format($timeTracking->duration) ?></td>
                </tr>
                <tr>
                    <th><?= __('Start') ?></th>
                    <td><?= h($timeTracking->start) ?></td>
                </tr>
            </table>
        </div>
    </div>
</div>
