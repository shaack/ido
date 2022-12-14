<?php
/**
 * @var \App\View\AppView $this
 * @var iterable<\App\Model\Entity\Task> $tasks
 */

function doneClass($doneTime) {
    $doneTodayClass = "text-muted";
    if ($doneTime > 0) {
        if ($doneTime < 1) {
            // $doneTodayClass = "text-danger";
        } else if ($doneTime < 2) {
            // $doneTodayClass = "text-warning";
        } else if ($doneTime < 3) {
            // $doneTodayClass = "text-warning";
        } else if ($doneTime < 4) {
            $doneTodayClass = "text-success";
        } else if ($doneTime < 5) {
            $doneTodayClass = "text-info";
        } else if ($doneTime < 6) {
            $doneTodayClass = "text-info fs-5";
        } else if ($doneTime < 7) {
            $doneTodayClass = "text-info fs-4";
        } else if ($doneTime < 8) {
            $doneTodayClass = "text-info fs-3";
        } else if ($doneTime < 9) {
            $doneTodayClass = "text-info fs-2";
        } else {
            $doneTodayClass = "text-info fs-1";
        }
    }
    return $doneTodayClass;
}
?>
<div class="tasks index content">
    <div class="row d-flex align-items-center mb-2">
        <div class="col"><h3 class="m-0"><?= __('Tasks') ?></h3></div>
        <?php
        $doneToday = floatval($this->get('doneToday'));
        $done1 = $this->get('done1');
        $done2 = $this->get('done2');
        $done3 = $this->get('done3');
        ?>
        <div class="col-auto"><span class="text-muted">today's hrs:</span> <span
                class="<?= doneClass($doneToday) ?>"><?= $this->Number->format($doneToday) ?></span></div>
        <div class="col-auto text-muted">
            <span class="me-2 <?= doneClass($done1) ?>"><?= $this->Number->format($done1) ?></span>
            <span class="me-2 <?= doneClass($done2) ?>"><?= $this->Number->format($done2) ?></span>
            <span class="<?= doneClass($done3) ?>"><?= $this->Number->format($done3) ?></span>
        </div>
        <div
            class="col-auto text-end"><?= $this->Html->link(__('New Task'), ['action' => 'add'], ['class' => 'btn btn-primary btn-sm float-right']) ?></div>
    </div>
    <div class="table-responsive">
        <table>
            <thead>
            <tr>
                <th><?= $this->Paginator->sort('done') ?></th>
                <th><?= $this->Paginator->sort('name') ?></th>
                <th class="text-end"><?= $this->Paginator->sort('Track') ?></th>
                <th><?= $this->Paginator->sort('prio') ?></th>
                <th><?= $this->Paginator->sort('marked') ?></th>
                <th><?= $this->Paginator->sort('start') ?></th>
                <th><?= $this->Paginator->sort('deadline') ?></th>
                <th><?= $this->Paginator->sort('customer_id') ?></th>
                <th><?= $this->Paginator->sort('service_id') ?></th>
                <th><?= $this->Paginator->sort('project_id') ?></th>
            </tr>
            </thead>
            <tbody>
            <script>
                function showTracking(taskId) {
                    window.open("/timeTrackings/add?task_id=" + taskId, "_blank", "popup,width=640,height=800,left=500,top=200")
                }
            </script>
            <?php foreach ($tasks as $task): ?>
                <tr class="<?= $task->done ? 'done-true' : 'done-false' ?> <?= $task->marked ? 'task-marked' : '' ?>">
                    <td class="text-end"><a class="no-line-through"
                                            href="/tasks/done/<?= $task->id ?>?done=<?= $task->done ? "0" : "1" ?>"><?= $task->done ? '<i class="fa-solid fa-circle"></i>' : '<i class="fa-regular fa-circle"></i>' ?></a>
                    </td>
                    <td><?= $this->Html->link($task->name ? $this->Text->truncate($task->name, 80) : $task->service->name,
                            ['action' => 'view', $task->id], ['class' => $task->name ? '' : 'fst-italic']) ?></td>
                    <td class="text-end">
                        <!-- /timeTrackings/add?task_id=<?= $task->id ?> -->
                        <a class="text-nowrap hover-bg" target="_blank" <?php /*onclick="showTracking(<?= $task->id ?>)" */ ?>
                           href="/timeTrackings/add?task_id=<?= $task->id ?>"><?= $task->duration() > 0 ? $this->Number->format($task->duration()) : ''; ?> <i
                                class="fa-solid fa-stopwatch"></i>???</a>
                    </td>
                    <td class="text-end"><?php
                        $icon = "";
                        switch ($task->prio) {
                            case 0:
                                $icon = "<a href='tasks/prio/$task->id?prio=1'><i class=\"fa-solid fa-plus\"></i></a> <a href='tasks/prio/$task->id?prio=-1'><i class=\"fa-solid fa-minus\"></i></a>";
                                break;
                            case 1:
                                $icon = "<a href='tasks/prio/$task->id?prio=0' class='text-warning'><i class=\"fa-solid fa-plus\"></i></a>";
                                break;
                            case -1:
                                $icon = "<a href='tasks/prio/$task->id?prio=0' class='text-muted'><i class=\"fa-solid fa-minus\"></i></a>";
                                break;
                        }
                        echo $icon;
                        ?></td>
                    <td class="text-end"><a class="no-line-through"
                                            href="/tasks/marked/<?= $task->id ?>?marked=<?= $task->marked ? "0" : "1" ?>"><?= $task->marked ? '<i class="fa-solid fa-flag"></i>' : '<i class="fa-regular fa-flag"></i>' ?></a>
                    </td>
                    <td><?= h($task->start_est) ?></td>
                    <td><?= h($task->deadline) ?></td>
                    <?php $customer = $task->service->project->customer ?>
                    <td><?= $this->Html->link($customer->shortcut, ['controller' => 'Customers', 'action' => 'view', $customer->id], ['style' => 'color: ' . $customer->color]) ?></td>
                    <td><?= $task->has('service') ? $this->Html->link($this->Text->truncate($task->service->name, 32), ['controller' => 'Services', 'action' => 'view', $task->service->id]) : '' ?></td>
                    <td><?= $this->Html->link($this->Text->truncate($task->service->project->name, 64), ['controller' => 'Projects', 'action' => 'view', $task->service->project->id]) ?></td>
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
