<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Service $service
 */

$this->assign('title', h($service->name));
?>
<div class="row">
    <aside class="column">
        <div class="actions">
            <div class="row">
                <div class="col">
                    <?= $this->Html->link(__('Edit Service'), ['action' => 'edit', $service->id], ['class' => 'side-nav-item']) ?>
                    <?= $this->Html->link(__("Project " . $service->project->name), ['action' => 'view', 'controller' => 'projects', $service->project_id], ['class' => 'side-nav-item']) ?>
                </div>
                <div class="col-auto">
                    <?= $this->Form->postLink(__('Delete'), ['action' => 'delete', $service->id], ['confirm' => __('Are you sure you want to delete # {0}?', $service->id), 'class' => 'side-nav-item text-danger']) ?>
                </div>
            </div>
        </div>
    </aside>
    <div class="column-responsive column-80">
        <div class="services view content">
            <h3><?= h($service->name) ?></h3>
            <table>
                <tr>
                    <th><?= __('Name') ?></th>
                    <td><?= h($service->name) ?></td>
                </tr>
                <tr>
                    <th><?= __('Project') ?></th>
                    <td><?= $service->has('project') ? $this->Html->link($service->project->name, ['controller' => 'Projects', 'action' => 'view', $service->project->id]) : '' ?></td>
                </tr>
                <tr>
                    <th><?= __('Effort Est') ?></th>
                    <td><?= $service->effort_est === null ? '' : $this->Number->format($service->effort_est) ?></td>
                </tr>
                <tr>
                    <th><?= __('Estimation Or Fixed Price') ?></th>
                    <td><?= $service->estimation_or_fixed_price === null ? '' : $this->Number->format($service->estimation_or_fixed_price) ?></td>
                </tr>
                <tr>
                    <th><?= __('Effort') ?></th>
                    <td><?= $service->effort() ?></td>
                </tr>
                <tr>
                    <th><?= __('Costs') ?></th>
                    <td><?= $service->costs === null ? '' : $this->Number->format($service->costs) ?></td>
                </tr>
                <tr>
                    <th><?= __('Sort') ?></th>
                    <td><?= $service->sort === null ? '' : $this->Number->format($service->sort) ?></td>
                </tr>
                <tr>
                    <th><?= __('Created') ?></th>
                    <td><?= h($service->created) ?></td>
                </tr>
            </table>
            <div class="text">
                <strong><?= __('Notes') ?></strong>
                <blockquote>
                    <?= $this->Text->autoParagraph(h($service->notes)); ?>
                </blockquote>
            </div>
            <div class="related">
                <h4><?= __('Related Tasks') ?></h4>
                <a class="button-clear" href="/tasks/add?service_id=<?= $service->id ?>">Add Task</a>
                <?php if (!empty($service->tasks)) : ?>
                <div class="table-responsive">
                    <table>
                        <tr>
                            <!-- <th><?= __('Id') ?></th> -->
                            <th><?= __('Done') ?></th>
                            <th><?= __('Marked') ?></th>
                            <th><?= __('Name') ?></th>
                            <th><?= __('Prio') ?></th>
                            <!-- <th><?= __('Start Est') ?></th>
                            <th><?= __('Deadline') ?></th>
                            <th><?= __('Duration Est') ?></th> -->
                            <!-- <th><?= __('Link') ?></th> -->
                            <!-- <th><?= __('Service Id') ?></th> -->
                            <!-- <th><?= __('Created') ?></th> -->
                            <!-- <th><?= __('Notes') ?></th> -->
                            <!-- <th><?= __('Done At') ?></th> -->
                            <th><?= __('Duration') ?></th>
                            <th class="actions"><?= __('Actions') ?></th>
                        </tr>
                        <?php foreach ($service->tasks as $task) : ?>
                        <tr>
                            <!-- <td><?= h($task->id) ?></td> -->
                            <td><?= h($task->done) ?></td>
                            <td><?= h($task->marked) ?></td>
                            <td><?= $this->Html->link($task->name, ['controller' => 'Tasks', 'action' => 'view', $task->id]) ?></td>
                            <td><?= h($task->prio) ?></td>
                            <!-- <td><?= h($task->start_est) ?></td>
                            <td><?= h($task->deadline) ?></td>
                            <td><?= h($task->duration_est) ?></td> -->
                            <!-- <td><?= h($task->link) ?></td>
                            <td><?= h($task->service_id) ?></td>
                            <td><?= h($task->created) ?></td>
                            <td><?= h($task->notes) ?></td> -->
                            <!-- <td><?= h($task->done_at) ?></td> -->
                            <td>
                                <?php
                                print_r($task->duration());
                                ?></td>
                            <td class="actions">
                                <?= $this->Html->link(__('View'), ['controller' => 'Tasks', 'action' => 'view', $task->id]) ?>
                                <?= $this->Html->link(__('Edit'), ['controller' => 'Tasks', 'action' => 'edit', $task->id]) ?>
                                <?= $this->Form->postLink(__('Delete'), ['controller' => 'Tasks', 'action' => 'delete', $task->id], ['confirm' => __('Are you sure you want to delete # {0}?', $task->id)]) ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </table>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
