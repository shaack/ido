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
            <?= $this->Html->link(__('Edit Time Tracking'), ['action' => 'edit', $timeTracking->Id], ['class' => 'side-nav-item']) ?>
            <?= $this->Form->postLink(__('Delete Time Tracking'), ['action' => 'delete', $timeTracking->Id], ['confirm' => __('Are you sure you want to delete # {0}?', $timeTracking->Id), 'class' => 'side-nav-item']) ?>
            <?= $this->Html->link(__('List Time Tracking'), ['action' => 'index'], ['class' => 'side-nav-item']) ?>
            <?= $this->Html->link(__('New Time Tracking'), ['action' => 'add'], ['class' => 'side-nav-item']) ?>
        </div>
    </aside>
    <div class="column-responsive column-80">
        <div class="timeTracking view content">
            <h3><?= h($timeTracking->Id) ?></h3>
            <table>
                <tr>
                    <th><?= __('Id') ?></th>
                    <td><?= $this->Number->format($timeTracking->Id) ?></td>
                </tr>
                <tr>
                    <th><?= __('Task') ?></th>
                    <td><?= $timeTracking->task === null ? '' : $this->Number->format($timeTracking->task) ?></td>
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
