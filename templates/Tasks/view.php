<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Task $task
 */

$parsedown = new Parsedown();
$this->assign('title', $task->name);
?>
<div class="actions">
    <?= $this->Html->link(__('Edit Task'), ['action' => 'edit', $task->id], ['class' => 'side-nav-item']) ?>
    <?= $this->Form->postLink(__('Delete Task'), ['action' => 'delete', $task->id], ['confirm' => __('Are you sure you want to delete # {0}?', $task->id), 'class' => 'side-nav-item']) ?>
    <?= $this->Html->link(__('List Tasks'), ['action' => 'index'], ['class' => 'side-nav-item']) ?>
    <?= $this->Html->link(__('New Task'), ['action' => 'add'], ['class' => 'side-nav-item']) ?>
</div>
<div class="row">
    <div class="column-responsive column-80">
        <div class="tasks view content">
            <h3><?= h($task->name) ?></h3>
            <div class="row">
                <div class="col-md-8">
                    <table>
                        <tr>
                            <th><?= __('Name') ?></th>
                            <td><?= h($task->name) ?></td>
                        </tr>
                        <tr>
                            <th><?= __('Service') ?></th>
                            <td><?= $task->has('service') ? $this->Html->link($task->service->name, ['controller' => 'Services', 'action' => 'view', $task->service->id]) : '' ?></td>
                        </tr>
                        <tr>
                            <th><?= __('Prio') ?></th>
                            <td><?= $task->prio === null ? '' : $this->Number->format($task->prio) ?></td>
                        </tr>
                        <tr>
                            <th><?= __('Duration Est') ?></th>
                            <td><?= $task->duration_est === null ? '' : $this->Number->format($task->duration_est) ?></td>
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
                            <th><?= __('Marked') ?></th>
                            <td><?= $task->marked ? __('Yes') : __('No'); ?></td>
                        </tr>
                        <tr>
                            <th><?= __('Done') ?></th>
                            <td><?= $task->done ? __('Yes') : __('No'); ?></td>
                        </tr>
                    </table>
                </div>
                <div class="col">
                    <?php if($task->notes) { ?>
                        <div class="text">
                            <strong><?= __('Notes') ?></strong>
                            <blockquote>
                                <?php
                                $text = h($task->notes);
                                $text = preg_replace('/(?:^|\s)#(\w+)/', ' <span class="text-info">#$1</span>', $text);
                                ?>
                                <?= $parsedown->parse($text); ?>
                            </blockquote>
                        </div>
                    <?php } ?>
                </div>
            </div>
            <div class="related">
                <h4><?= __('Related Time Trackings') ?></h4>
                <a href="/timeTrackings/add?task_id=<?= $task->id ?>">Start Tracking</a>
                <?php if (!empty($task->time_trackings)) : ?>
                    <div class="table-responsive">
                        <table>
                            <tr>
                                <th><?= __('Start') ?></th>
                                <th><?= __('Duration') ?></th>
                                <th class="actions"><?= __('Actions') ?></th>
                            </tr>
                            <?php $durationSum = 0 ?>
                            <?php foreach ($task->time_trackings as $timeTrackings) :
                                $durationSum += $timeTrackings->duration;
                                ?>
                                <tr>
                                    <td><?= h($timeTrackings->start) ?></td>
                                    <td><?= h($timeTrackings->duration) ?></td>
                                    <td class="actions">
                                        <?= $this->Html->link(__('View'), ['controller' => 'TimeTrackings', 'action' => 'view', $timeTrackings->id]) ?>
                                        <?= $this->Html->link(__('Edit'), ['controller' => 'TimeTrackings', 'action' => 'edit', $timeTrackings->id]) ?>
                                        <?= $this->Form->postLink(__('Delete'), ['controller' => 'TimeTrackings', 'action' => 'delete', $timeTrackings->id], ['confirm' => __('Are you sure you want to delete # {0}?', $timeTrackings->id)]) ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            <tr>
                                <td></td>
                                <td><b><?= $durationSum ?></b></td>
                            </tr>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
