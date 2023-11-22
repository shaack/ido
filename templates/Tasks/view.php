<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Task $task
 */

$parsedown = new Parsedown();
$this->assign('title', $task->name ? $task->name : $task->service->name);
?>
<div class="actions">
    <?= $this->Html->link(__('Edit Task'), ['action' => 'edit', $task->id], ['class' => 'side-nav-item']) ?>
    <?= $this->Form->postLink(__('Delete Task'), ['action' => 'delete', $task->id], ['confirm' => __('Are you sure you want to delete # {0}?', $task->id), 'class' => 'side-nav-item']) ?>
    <?= $this->Html->link(__('List Tasks'), ['action' => 'index'], ['class' => 'side-nav-item']) ?>
    <?= $this->Html->link(__('New Task in this Service'), ['action' => 'add', '?' => ['service_id' => $task->service->id]], ['class' => 'side-nav-item']) ?>
</div>
<div class="row">
    <div class="column-responsive column-80">
        <div class="tasks view content">
            <h3>Task: <?= h($task->name) ?: "[Task]" ?></h3>
            <!--
            <table>
                <tr>
                    <th><?= __('Name') ?></th>
                    <td><?= h($task->name) ?: "[Task]" ?></td>
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
                    <th><?= __('Start Est') ?></th>
                    <td><?= h($task->start_est) ?></td>
                </tr>
                <tr>
                    <th><?= __('Deadline') ?></th>
                    <td><?= h($task->deadline) ?></td>
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
            -->
            <div class="text">
                <strong><?= __('Notes') ?></strong>
                <?php if ($task->notes) { ?>
                    <blockquote>
                        <?php
                        $text = h($task->notes);
                        $text = preg_replace('/(?:^|\s)#(\w+)/', ' <span class="text-info">#$1</span>', $text);
                        ?>
                        <?= $parsedown->parse($text); ?>
                    </blockquote>
                <?php } ?>
            </div>

            <div class="related">
                <h4><?= __('Related Time Trackings') ?></h4>
                <div class="actions">
                    <a target="_blank" href="/timeTrackings/add?task_id=<?= $task->id ?>">Start Tracking</a>
                </div>
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
                                    <td><?= h($timeTrackings->created) ?></td>
                                    <td><?= h($timeTrackings->duration) ?></td>
                                    <td class="actions">
                                        <!-- <?= $this->Html->link(__('View'), ['controller' => 'TimeTrackings', 'action' => 'view', $timeTrackings->id]) ?> -->
                                        <?= $this->Html->link(__('Edit'), ['controller' => 'TimeTrackings', 'action' => 'edit', $timeTrackings->id]) ?>
                                        <?= $this->Form->postLink(__('Delete'), ['controller' => 'TimeTrackings', 'action' => 'delete', $timeTrackings->id], ['confirm' => __('Are you sure you want to delete # {0}?', $timeTrackings->id)]) ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            <tr>
                                <td class="text-end border-top border-2"
                                </td>
                                <td class="border-top border-2"><b><?= $durationSum ?></b></td>
                                <td class="text-end border-top border-2"
                                </td>
                            </tr>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
