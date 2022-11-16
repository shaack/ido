<?php
/**
 * @var \App\View\AppView $this
 * @var iterable<\App\Model\Entity\Task> $tasks
 */
?>
<div class="tasks index content">
    <div class="row d-flex align-items-center mb-2">
        <div class="col"><h3 class="m-0"><?= __('Tasks') ?></h3></div>
        <div
            class="col text-end"><?= $this->Html->link(__('New Task'), ['action' => 'add'], ['class' => 'btn btn-primary btn-sm float-right']) ?></div>
    </div>
    <div class="table-responsive">
        <table>
            <thead>
            <tr>
                <!-- <th><?= $this->Paginator->sort('id') ?></th> -->
                <!-- <th><?= $this->Paginator->sort('marked') ?></th> -->
                <!-- <th><?= $this->Paginator->sort('prio') ?></th> -->
                <th><?= $this->Paginator->sort('name') ?></th>
                <th class="text-end"><?= $this->Paginator->sort('timeTrackings') ?></th>
                <th><?= $this->Paginator->sort('start') ?></th>
                <th><?= $this->Paginator->sort('deadline') ?></th>
                <!-- <th><?= $this->Paginator->sort('duration_est') ?></th> -->
                <!-- <th><?= $this->Paginator->sort('link') ?></th> -->
                <th><?= $this->Paginator->sort('customer_id') ?></th>
                <th><?= $this->Paginator->sort('service_id') ?></th>
                <th><?= $this->Paginator->sort('project_id') ?></th>
                <!-- <th><?= $this->Paginator->sort('created') ?></th> -->
                <!-- <th><?= $this->Paginator->sort('done') ?></th> -->
                <!-- <th><?= $this->Paginator->sort('done_at') ?></th> -->
                <!-- <th class="actions"><?= __('Actions') ?></th> -->
            </tr>
            </thead>
            <tbody>
            <?php foreach ($tasks as $task): ?>
                <tr class="<?= $task->marked ? 'task-marked' : '' ?>">
                    <td><?= $this->Html->link($task->name ? $task->name : "=> " . $task->service->name,
                            ['action' => 'view', $task->id], ['class' => $task->name ? '' : '']) ?></td>
                    <td class="text-end">
                        <a class="text-nowrap" href="/timeTrackings/add?task_id=<?= $task->id ?>"><?= $task->duration() > 0 ? $this->Number->format($task->duration()) : ''; ?> ⏱️</a>
                    </td>
                    <td><?= h($task->start_est) ?></td>
                    <td><?= h($task->deadline) ?></td>
                    <!-- <td><?= $task->duration_est === null ? '' : $this->Number->format($task->duration_est) ?></td> -->
                    <!-- <td><?= h($task->link) ?></td> -->
                    <?php $customer = $task->service->project->customer ?>
                    <td><?= $this->Html->link($customer->shortcut, ['controller' => 'Customers', 'action' => 'view', $customer->id], ['style' => 'color: ' . $customer->color]) ?></td>
                    <td><?= $task->has('service') ? $this->Html->link($task->service->name, ['controller' => 'Services', 'action' => 'view', $task->service->id]) : '' ?></td>
                    <td><?= $this->Html->link($task->service->project->name, ['controller' => 'Projects', 'action' => 'view', $task->service->project->id]) ?></td>
                    <!-- <td><?= h($task->created) ?></td> -->
                    <!-- <td><?= h($task->done) ?></td> -->
                    <!-- <td><?= h($task->done_at) ?></td> -->
                    <!--
                    <td class="actions">
                        <?= $this->Html->link(__('View'), ['action' => 'view', $task->id]) ?>
                        <?= $this->Html->link(__('Edit'), ['action' => 'edit', $task->id]) ?>
                        <?= $this->Form->postLink(__('Delete'), ['action' => 'delete', $task->id], ['confirm' => __('Are you sure you want to delete # {0}?', $task->id)]) ?>
                    </td>
                    -->
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
