<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Task $task
 */
?>
<div class="row">
    <aside class="column">
        <div class="side-nav">
            <h4 class="heading"><?= __('Actions') ?></h4>
            <?= $this->Html->link(__('Edit Task'), ['action' => 'edit', $task->Id], ['class' => 'side-nav-item']) ?>
            <?= $this->Form->postLink(__('Delete Task'), ['action' => 'delete', $task->Id], ['confirm' => __('Are you sure you want to delete # {0}?', $task->Id), 'class' => 'side-nav-item']) ?>
            <?= $this->Html->link(__('List Tasks'), ['action' => 'index'], ['class' => 'side-nav-item']) ?>
            <?= $this->Html->link(__('New Task'), ['action' => 'add'], ['class' => 'side-nav-item']) ?>
        </div>
    </aside>
    <div class="column-responsive column-80">
        <div class="tasks view content">
            <h3><?= h($task->title) ?></h3>
            <table>
                <tr>
                    <th><?= __('Id') ?></th>
                    <td><?= $this->Number->format($task->Id) ?></td>
                </tr>
                <tr>
                    <th><?= __('Duration Est') ?></th>
                    <td><?= $task->duration_est === null ? '' : $this->Number->format($task->duration_est) ?></td>
                </tr>
                <tr>
                    <th><?= __('Service') ?></th>
                    <td><?= $task->service === null ? '' : $this->Number->format($task->service) ?></td>
                </tr>
                <tr>
                    <th><?= __('Customer') ?></th>
                    <td><?= $task->customer === null ? '' : $this->Number->format($task->customer) ?></td>
                </tr>
                <tr>
                    <th><?= __('Duration') ?></th>
                    <td><?= $task->duration === null ? '' : $this->Number->format($task->duration) ?></td>
                </tr>
                <tr>
                    <th><?= __('Start Est') ?></th>
                    <td><?= h($task->start_est) ?></td>
                </tr>
                <tr>
                    <th><?= __('Deadline') ?></th>
                    <td><?= h($task->deadline) ?></td>
                </tr>
                <tr>
                    <th><?= __('Created') ?></th>
                    <td><?= h($task->created) ?></td>
                </tr>
                <tr>
                    <th><?= __('Start') ?></th>
                    <td><?= h($task->start) ?></td>
                </tr>
                <tr>
                    <th><?= __('Done') ?></th>
                    <td><?= h($task->done) ?></td>
                </tr>
            </table>
            <div class="text">
                <strong><?= __('Desktop') ?></strong>
                <blockquote>
                    <?= $this->Text->autoParagraph(h($task->desktop)); ?>
                </blockquote>
            </div>
            <div class="text">
                <strong><?= __('Prio') ?></strong>
                <blockquote>
                    <?= $this->Text->autoParagraph(h($task->prio)); ?>
                </blockquote>
            </div>
            <div class="text">
                <strong><?= __('Title') ?></strong>
                <blockquote>
                    <?= $this->Text->autoParagraph(h($task->title)); ?>
                </blockquote>
            </div>
            <div class="text">
                <strong><?= __('Link') ?></strong>
                <blockquote>
                    <?= $this->Text->autoParagraph(h($task->link)); ?>
                </blockquote>
            </div>
            <div class="text">
                <strong><?= __('Notes') ?></strong>
                <blockquote>
                    <?= $this->Text->autoParagraph(h($task->notes)); ?>
                </blockquote>
            </div>
            <div class="text">
                <strong><?= __('Status') ?></strong>
                <blockquote>
                    <?= $this->Text->autoParagraph(h($task->status)); ?>
                </blockquote>
            </div>
        </div>
    </div>
</div>
