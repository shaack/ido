<?php
/**
 * @var \App\View\AppView $this
 * @var array $days
 * @var iterable<\App\Model\Entity\Task> $tasks
 * @var int $openTaskCount
 * @var array<\App\Model\Entity\Project> $runningProjects
 * @var int $runningProjectCount
 * @var iterable<\App\Model\Entity\Project> $toInvoice
 * @var array $receivables
 * @var iterable<\App\Model\Entity\TimeTracking> $recentTrackings
 * @var array|null $lastBackup
 */
$this->assign('title', __('Today'));

$today = end($days);
$todayHours = $today['hours'];
$weekHours = array_sum(array_column($days, 'hours'));
$dayMax = max(8, ...array_column($days, 'hours'));

$toInvoiceNet = 0.0;
foreach ($toInvoice as $project) {
    $toInvoiceNet += $project->costs();
}
$toInvoiceCount = count($toInvoice->toList());
?>
<div class="dash">
    <header class="page-head">
        <h1 class="page-title"><?= h($today['date']->i18nFormat('EEEE, d. MMMM')) ?></h1>
        <div class="page-actions">
            <?= $this->Html->link(
                $this->element('icon', ['name' => 'plus']) . '<span>' . __('New task') . '</span>',
                ['controller' => 'Tasks', 'action' => 'add'],
                ['class' => 'btn btn-primary btn-icon', 'escape' => false]
            ) ?>
        </div>
    </header>

    <section class="dash-today" aria-labelledby="dash-today-title">
        <div class="dash-today-main">
            <h2 class="visually-hidden" id="dash-today-title"><?= __('Time today') ?></h2>
            <p class="dash-today-figure">
                <span class="dash-today-number"><?= $this->Effort->hours($todayHours) ?></span>
                <span class="dash-today-unit"><?= __('hours tracked today') ?></span>
            </p>
            <?= $this->element('ruler', ['hours' => $todayHours]) ?>
        </div>
        <div class="dash-week">
            <ol class="dash-week-bars">
                <?php foreach ($days as $i => $day) : $isToday = $i === count($days) - 1 ?>
                    <li class="<?= $isToday ? 'is-today' : '' ?><?= $day['hours'] <= 0 ? ' is-empty' : '' ?>"
                        title="<?= h($day['date']->i18nFormat('EEEE, d. MMMM')) ?>: <?= $this->Effort->hours($day['hours']) ?> h">
                        <span class="dash-week-hours"><?= $day['hours'] > 0 ? $this->Effort->hours($day['hours']) : '' ?></span>
                        <span class="dash-week-bar"><span style="height: <?= round($day['hours'] / $dayMax * 100, 1) ?>%"></span></span>
                        <span class="dash-week-day"><?= h($day['date']->i18nFormat('EEEEEE')) ?></span>
                    </li>
                <?php endforeach; ?>
            </ol>
            <p class="dash-week-sum">
                <?= __('Last 7 days') ?>
                <strong><?= $this->Effort->hours($weekHours) ?> h</strong>
                <?= $this->Html->link(__('Weekly report'), ['controller' => 'Reports', 'action' => 'weeklyHours']) ?>
            </p>
        </div>
    </section>

    <div class="dash-grid">
        <div class="dash-main">
            <section class="dash-section" aria-labelledby="dash-tasks-title">
                <div class="dash-section-head">
                    <h2 id="dash-tasks-title"><?= __('Next up') ?></h2>
                    <?= $this->Html->link(__('All {0} open tasks', $openTaskCount), ['controller' => 'Tasks', 'action' => 'index']) ?>
                </div>
                <?php if (count($tasks->toList()) === 0) : ?>
                    <p class="dash-empty"><?= __('Nothing open. Add a task to a project to plan the next step.') ?></p>
                <?php else : ?>
                    <ul class="task-list">
                        <?php foreach ($tasks as $task) :
                            $project = $task->service->project ?? null;
                            $customer = $project->customer ?? null;
                            ?>
                            <li class="task-row<?= $task->done ? ' is-done' : '' ?><?= $task->marked ? ' is-marked' : '' ?>">
                                <a class="task-check" href="/tasks/done/<?= $task->id ?>?done=<?= $task->done ? '0' : '1' ?>"
                                   aria-label="<?= $task->done ? __('Reopen task') : __('Mark task done') ?>">
                                    <i class="fa-<?= $task->done ? 'solid' : 'regular' ?> fa-circle"></i>
                                </a>
                                <div class="task-body">
                                    <?= $this->Html->link($task->smartName, ['controller' => 'Tasks', 'action' => 'view', $task->id],
                                        ['class' => 'task-name' . ($task->name ? '' : ' fst-italic')]) ?>
                                    <span class="task-meta">
                                        <?= $this->element('customer_tag', ['customer' => $customer]) ?>
                                        <?= $project ? h($project->name) : '' ?>
                                    </span>
                                </div>
                                <a class="task-flag" href="/tasks/marked/<?= $task->id ?>?marked=<?= $task->marked ? '0' : '1' ?>"
                                   aria-label="<?= $task->marked ? __('Unpin task') : __('Pin task') ?>">
                                    <i class="fa-<?= $task->marked ? 'solid' : 'regular' ?> fa-flag"></i>
                                </a>
                                <a class="task-track" href="/timeTrackings/add?task_id=<?= $task->id ?>"
                                   aria-label="<?= __('Track time') ?>">
                                    <span><?= $task->duration() > 0 ? $this->Effort->hours($task->duration()) : '' ?></span>
                                    <i class="fa-solid fa-stopwatch"></i>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </section>

            <section class="dash-section" aria-labelledby="dash-recent-title">
                <div class="dash-section-head">
                    <h2 id="dash-recent-title"><?= __('Recently tracked') ?></h2>
                    <?= $this->Html->link(__('All time entries'), ['controller' => 'TimeTrackings', 'action' => 'index']) ?>
                </div>
                <?php if (count($recentTrackings->toList()) === 0) : ?>
                    <p class="dash-empty"><?= __('No time tracked yet. Start the stopwatch on a task.') ?></p>
                <?php else : ?>
                    <table class="table dash-recent">
                        <tbody>
                        <?php foreach ($recentTrackings as $tracking) :
                            $task = $tracking->task;
                            $customer = $task->service->project->customer ?? null;
                            ?>
                            <tr>
                                <td class="text-nowrap text-body-secondary"><?= h($tracking->created->i18nFormat('EEEEEE d.M. HH:mm')) ?></td>
                                <td class="w-100">
                                    <?= $this->element('customer_tag', ['customer' => $customer]) ?>
                                    <?= $this->Html->link($task->smartName, ['controller' => 'Tasks', 'action' => 'view', $task->id]) ?>
                                </td>
                                <td class="text-end text-nowrap"><?= $this->Effort->hours($tracking->duration) ?> h</td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </section>
        </div>

        <aside class="dash-side">
            <section class="dash-section" aria-labelledby="dash-money-title">
                <div class="dash-section-head">
                    <h2 id="dash-money-title"><?= __('Money') ?></h2>
                </div>
                <dl class="money">
                    <div class="money-row">
                        <dt><?= __('Ready to invoice') ?></dt>
                        <dd>
                            <span class="money-amount"><?= $this->Number->currency($toInvoiceNet) ?></span>
                            <span class="money-note"><?= __n('{0} project, net', '{0} projects, net', $toInvoiceCount, $toInvoiceCount) ?></span>
                        </dd>
                    </div>
                    <?php foreach ($toInvoice as $project) : ?>
                        <div class="money-item">
                            <?= $this->Html->link($project->name, ['controller' => 'Projects', 'action' => 'view', $project->id]) ?>
                            <span><?= $this->Number->currency($project->costs()) ?></span>
                        </div>
                    <?php endforeach; ?>
                    <div class="money-row">
                        <dt><?= $this->Html->link(__('Awaiting payment'), ['controller' => 'Reports', 'action' => 'receivables']) ?></dt>
                        <dd>
                            <span class="money-amount"><?= $this->Number->currency($receivables['gross']) ?></span>
                            <span class="money-note">
                                <?= __n('{0} invoice, gross', '{0} invoices, gross', $receivables['count'], $receivables['count']) ?>
                                <?php if ($receivables['oldestDays'] !== null) : ?>
                                    <span class="<?= $receivables['oldestDays'] > 30 ? 'text-amber fw-semibold' : '' ?>">
                                        <?= __('oldest {0} days', $receivables['oldestDays']) ?>
                                    </span>
                                <?php endif; ?>
                            </span>
                        </dd>
                    </div>
                </dl>
            </section>

            <section class="dash-section" aria-labelledby="dash-projects-title">
                <div class="dash-section-head">
                    <h2 id="dash-projects-title"><?= __('Running projects') ?></h2>
                    <?= $this->Html->link(__('All {0}', $runningProjectCount), '/projects?current=1') ?>
                </div>
                <?php if (!$runningProjects) : ?>
                    <p class="dash-empty"><?= __('No project is running.') ?></p>
                <?php endif; ?>
                <ul class="project-list">
                    <?php foreach ($runningProjects as $project) :
                        $billed = $project->effort();
                        $planned = $project->effortPlanned();
                        $share = $planned > 0 ? $billed / $planned : null;
                        ?>
                        <li class="project-row">
                            <div class="project-row-head">
                                <?= $this->element('customer_tag', ['customer' => $project->customer]) ?>
                                <?= $this->Html->link($project->name, ['controller' => 'Projects', 'action' => 'view', $project->id], ['class' => 'project-name']) ?>
                            </div>
                            <?php if ($share !== null) : ?>
                                <div class="project-progress<?= $share > 1 ? ' is-over' : '' ?>"
                                     role="meter" aria-valuemin="0" aria-valuemax="<?= $planned ?>" aria-valuenow="<?= $billed ?>"
                                     aria-label="<?= __('{0} of {1} planned hours', $this->Effort->effort($billed), $this->Effort->effort($planned)) ?>">
                                    <span style="width: <?= round(min($share, 1) * 100, 1) ?>%"></span>
                                </div>
                            <?php endif; ?>
                            <div class="project-row-figures">
                                <span>
                                    <?= $this->Effort->effort($billed) ?>
                                    <?= $planned > 0 ? __('of {0} h', $this->Effort->effort($planned)) : 'h' ?>
                                </span>
                                <span><?= $this->Number->currency($project->costs()) ?></span>
                            </div>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </section>

            <p class="dash-backup">
                <?= $this->element('icon', ['name' => 'backups']) ?>
                <?php if ($lastBackup) : ?>
                    <?= $this->Html->link(__('Last backup {0}', $lastBackup['created']->i18nFormat('d.M. HH:mm')), ['controller' => 'Backups', 'action' => 'index']) ?>
                <?php else : ?>
                    <?= $this->Html->link(__('No backup yet'), ['controller' => 'Backups', 'action' => 'index'], ['class' => 'text-amber']) ?>
                <?php endif; ?>
            </p>
        </aside>
    </div>
</div>
