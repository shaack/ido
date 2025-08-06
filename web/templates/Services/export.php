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
                    <?= $this->Html->link(__("View Project"), ['action' => 'view', 'controller' => 'projects', $service->project_id], ['class' => 'side-nav-item']) ?>
                </div>
                <div class="col-auto">
                    <?= $this->Form->postLink(__('Delete'), ['action' => 'delete', $service->id], ['confirm' => __('Are you sure you want to delete # {0}?', $service->id), 'class' => 'side-nav-item text-danger']) ?>
                </div>
            </div>
        </div>
    </aside>
    <div class="column-responsive column-80">
        <div class="services view content">
            <h3>Service: <?= h($service->name) ?></h3>
            <!--
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
                    <th><?= __('Fixed Price') ?></th>
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
            -->
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
                            <th><?= __('Name') ?></th>
                            <th><?= __('Duration') ?></th>
                        </tr>
                        <?php $durationSum = 0 ?>
                        <?php foreach ($service->tasks as $task) :
                            $durationSum += $task->duration();
                            ?>
                        <tr>
                            <td><?= $this->Html->link($task->name ?: "[Task]", ['controller' => 'Tasks', 'action' => 'view', $task->id]) ?></td>
                            <td><?= $task->duration() ?></td>
                        </tr>
                        <?php endforeach; ?>
                        <tr>
                            <td class="text-end border-top border-2">Summe
                            </td>
                            <td class="border-top border-2"><b><?= $durationSum ?></b></td>
                        </tr>
                    </table>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
