<?php
/**
 * @var \App\View\AppView $this
 * @var iterable<\App\Model\Entity\Task> $tasks
 */

function doneClass($doneTime)
{
    $doneTodayClass = "text-muted opacity-50";
    if ($doneTime > 0) {
        $doneTodayClass = "text-muted";
        if ($doneTime >= 3 && $doneTime < 4) {
            $doneTodayClass = "text-success";
        } else if ($doneTime >= 4) {
            $doneTodayClass = "text-info";
        }
    }
    return $doneTodayClass;
}

?>
<div class="tasks index content">
    <div class="row d-flex align-items-baseline mb-2">
        <div class="col"><h3 class="m-0"><?= __('Tasks') ?></h3></div>
        <?php
        $doneToday = floatval($this->get('doneToday'));
        $done1 = $this->get('done1');
        $done2 = $this->get('done2');
        $done3 = $this->get('done3');
        ?>
        <div class="col-auto"><span class="text-muted">today's hrs:</span> <span
                class="<?= doneClass($doneToday) ?>"><?= $this->Number->format($doneToday, ["places" => 2]) ?></span>
        </div>
        <div class="col-auto text-muted">
            <span class="me-2 <?= doneClass($done1) ?>"><?= $this->Number->format($done1, ["places" => 2]) ?></span>
            <span class="me-2 <?= doneClass($done2) ?>"><?= $this->Number->format($done2, ["places" => 2]) ?></span>
            <span class="<?= doneClass($done3) ?>"><?= $this->Number->format($done3, ["places" => 2]) ?></span>
        </div>
        <div class="col-auto">
            <?php
            $projectOptions = $this->get('projectOptions') ?? [];
            $projectId = $this->get('projectId');
            ?>
            <form method="get" action="/tasks" class="m-0">
                <select name="project_id" class="form-select form-select-sm" onchange="this.form.submit()">
                    <option value=""><?= __('Alle Projekte') ?></option>
                    <?php foreach ($projectOptions as $id => $label): ?>
                        <option value="<?= $id ?>" <?= (int)$projectId === (int)$id ? 'selected' : '' ?>><?= h($label) ?></option>
                    <?php endforeach; ?>
                </select>
            </form>
        </div>
        <div
            class="col-auto text-end"><?= $this->Html->link(__('New Task'), ['action' => 'add'], ['class' => 'btn btn-primary btn-sm float-right']) ?></div>
    </div>
    <div class="table-responsive">
        <table>
            <thead>
            <tr>
                <th></th>
                <th><?= $this->Paginator->sort('name') ?></th>
                <th class="text-end"><?= __('Track') ?></th>
                <th></th>
                <th><?= $this->Paginator->sort('Customers.shortcut', 'Cst') ?></th>
                <th><?= $this->Paginator->sort('Services.name', 'Service') ?></th>
                <th><?= $this->Paginator->sort('Projects.name', 'Project') ?></th>
            </tr>
            </thead>
            <tbody>
            <script>
                function showTracking(taskId) {
                    window.open("/timeTrackings/add?task_id=" + taskId, "_self", "popup,width=640,height=800,left=500,top=200")
                }
            </script>
            <?php foreach ($tasks as $task): ?>
                <?php
                $customer = $task->service->project->customer;
                ?>
                <tr class="<?= $task->done ? 'done-true' : 'done-false' ?> <?= $task->marked ? 'task-marked' : '' ?>">
                    <td class="text-end"><a class="no-line-through"
                                            href="/tasks/done/<?= $task->id ?>?done=<?= $task->done ? "0" : "1" ?>"><?= $task->done ? '<i class="fa-solid fa-circle"></i>' : '<i class="fa-regular fa-circle"></i>' ?></a>
                    </td>
                    <td><?= $this->Html->link($this->Text->truncate($task->smartName, 80),
                            ['action' => 'view', $task->id], ['class' => $task->name ? '' : 'fst-italic']) ?></td>
                    <td class="text-end">
                        <!-- /timeTrackings/add?task_id=<?= $task->id ?> -->
                        <a class="text-nowrap hover-bg"
                           href="/timeTrackings/add?task_id=<?= $task->id ?>"><?= $task->duration() > 0 ? $this->Effort->hours($task->duration()) : ''; ?>
                            <i class="fa-solid fa-stopwatch"></i>️</a>
                    </td>
                    <td><a class="no-line-through"
                           href="/tasks/marked/<?= $task->id ?>?marked=<?= $task->marked ? "0" : "1" ?>"><?= $task->marked ? '<i class="fa-solid fa-flag"></i>' : '<i class="fa-regular fa-flag"></i>' ?></a>
                    <td><?= $this->Html->link($customer->shortcut, ['controller' => 'Customers', 'action' => 'view', $customer->id], ['style' => 'color: ' . $customer->color]) ?></td>
                    <td><?= $this->Html->link($this->Text->truncate($task->service->smartName, 45), ['controller' => 'Services', 'action' => 'view', $task->service->id],
                            ['class' => $task->service->name ? '' : 'fst-italic']) ?></td>
                    <td><?= $this->Html->link($this->Text->truncate($task->service->project->name, 40), ['controller' => 'Projects', 'action' => 'view', $task->service->project->id]) ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <div class="paginator">
        <!--
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
        -->
        <p><?= $this->Paginator->counter(__('{{current}} record(s)')) ?></p>
    </div>
</div>
