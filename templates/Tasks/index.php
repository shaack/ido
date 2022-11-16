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
                <th><?= $this->Paginator->sort('name') ?></th>
                <th><?= $this->Paginator->sort('prio') ?></th>
                <th class="text-center">Track</th>
                <th><?= $this->Paginator->sort('start') ?></th>
                <th><?= $this->Paginator->sort('deadline') ?></th>
                <th><?= $this->Paginator->sort('customer_id') ?></th>
                <th><?= $this->Paginator->sort('service_id') ?></th>
                <th><?= $this->Paginator->sort('project_id') ?></th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($tasks as $task): ?>
                <tr class="<?= $task->marked ? 'task-marked' : '' ?>">
                    <td><?= $this->Html->link($task->name ? $task->name : "=> " . $task->service->name,
                            ['action' => 'view', $task->id], ['class' => $task->name ? '' : '']) ?></td>
                    <td class="text-end"><?php
                        $icon = "";
                        switch($task->prio) {
                            case 1:
                                $icon = "<span class='text-warning'>+</span>";
                                break;
                            case -1:
                                $icon = "<span class='text-muted'>-</span>";
                                break;
                        }
                        echo $icon;
                        ?></td>
                    <td class="text-end">
                        <a class="text-nowrap hover-bg" href="/timeTrackings/add?task_id=<?= $task->id ?>"><?= $task->duration() > 0 ? $this->Number->format($task->duration()) : ''; ?> ⏱️</a>
                    </td>
                    <td><?= h($task->start_est) ?></td>
                    <td><?= h($task->deadline) ?></td>
                    <?php $customer = $task->service->project->customer ?>
                    <td><?= $this->Html->link($customer->shortcut, ['controller' => 'Customers', 'action' => 'view', $customer->id], ['style' => 'color: ' . $customer->color]) ?></td>
                    <td><?= $task->has('service') ? $this->Html->link($this->Text->truncate($task->service->name, 24), ['controller' => 'Services', 'action' => 'view', $task->service->id]) : '' ?></td>
                    <td><?= $this->Html->link($this->Text->truncate($task->service->project->name, 32), ['controller' => 'Projects', 'action' => 'view', $task->service->project->id]) ?></td>
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
