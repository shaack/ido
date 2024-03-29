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
        if ($doneTime >=3 && $doneTime < 4) {
            $doneTodayClass = "text-success";
        } else  if ($doneTime >= 4) {
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
                class="<?= doneClass($doneToday) ?>"><?= $this->Number->format($doneToday, ["places" => 2]) ?></span></div>
        <div class="col-auto text-muted">
            <span class="me-2 <?= doneClass($done1) ?>"><?= $this->Number->format($done1, ["places" => 2]) ?></span>
            <span class="me-2 <?= doneClass($done2) ?>"><?= $this->Number->format($done2, ["places" => 2]) ?></span>
            <span class="<?= doneClass($done3) ?>"><?= $this->Number->format($done3, ["places" => 2]) ?></span>
        </div>
        <div
            class="col-auto text-end"><?= $this->Html->link(__('New Task'), ['action' => 'add'], ['class' => 'btn btn-primary btn-sm float-right']) ?></div>
    </div>
    <div class="table-responsive">
        <table>
            <thead>
            <tr>
                <th><?= $this->Paginator->sort('') ?></th>
                <th><?= $this->Paginator->sort('name') ?></th>
                <th class="text-end"><?= $this->Paginator->sort('Track') ?></th>
                <th><?= $this->Paginator->sort('') ?></th>
                <th><?= $this->Paginator->sort('cst') ?></th>
                <!--
                <th><?= $this->Paginator->sort('start') ?></th>
                <th><?= $this->Paginator->sort('deadline') ?></th>
                -->
                <th><?= $this->Paginator->sort('due') ?></th>
                <th><?= $this->Paginator->sort('service_id') ?></th>
                <th><?= $this->Paginator->sort('project_id') ?></th>
                <!-- <th><?= $this->Paginator->sort('prio') ?></th> -->
            </tr>
            </thead>
            <tbody>
            <script>
                function showTracking(taskId) {
                    window.open("/timeTrackings/add?task_id=" + taskId, "_blank", "popup,width=640,height=800,left=500,top=200")
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
                           target="_blank" <?php /*onclick="showTracking(<?= $task->id ?>)" */ ?>
                           href="/timeTrackings/add?task_id=<?= $task->id ?>"><?= $task->duration() > 0 ? $this->Number->format($task->duration()) : ''; ?>
                            <i
                                class="fa-solid fa-stopwatch"></i>️</a>
                    </td>
                    <td><a class="no-line-through"
                           href="/tasks/marked/<?= $task->id ?>?marked=<?= $task->marked ? "0" : "1" ?>"><?= $task->marked ? '<i class="fa-solid fa-flag"></i>' : '<i class="fa-regular fa-flag"></i>' ?></a>
                    <td><?= $this->Html->link($customer->shortcut, ['controller' => 'Customers', 'action' => 'view', $customer->id], ['style' => 'color: ' . $customer->color]) ?></td>
                    </td>
                    <!--
                        <td><?= h($task->start_est) ?></td>
                        <td><?= h($task->deadline) ?></td>
                        -->
                    <td><?= h($task->deadline) ?></td>
                    <td><?= $this->Html->link($this->Text->truncate($task->service->smartName, 45), ['controller' => 'Services', 'action' => 'view', $task->service->id],
                            ['class' => $task->service->name ? '' : 'fst-italic']) ?></td>
                    <td><?= $this->Html->link($this->Text->truncate($task->service->project->name, 40), ['controller' => 'Projects', 'action' => 'view', $task->service->project->id]) ?></td>
                    <!--
                    <td class="text-end"><?php
                        $icon = "";
                        switch ($task->prio) {
                            case 0:
                                $icon = "<a href='tasks/prio/$task->id?prio=1'><i class=\"fa-solid fa-plus\"></i></a> <a href='tasks/prio/$task->id?prio=-1'><i class=\"fa-solid fa-minus\"></i></a>";
                                break;
                            case 1:
                                $icon = "<a href='tasks/prio/$task->id?prio=-1' class='text-warning'><i class=\"fa-solid fa-plus\"></i></a>";
                                break;
                            case -1:
                                $icon = "<a href='tasks/prio/$task->id?prio=1' class='text-muted'><i class=\"fa-solid fa-minus\"></i></a>";
                                break;
                        }
                        echo $icon;
                        ?></td>-->
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
