<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Project $project
 */

$parsedown = new Parsedown();
?>
<div class="row">
    <aside class="column">
        <div class="actions">
            <?= $this->Html->link(__('Edit Project'), ['action' => 'edit', $project->id], ['class' => 'side-nav-item']) ?>
            <?= $this->Html->link(__('View Invoice'), ['action' => 'invoice',  $project->id], ["target" => "_blank"]) ?>
            <?= $this->Html->link(__('List Projects'), ['action' => 'index'], ['class' => 'side-nav-item']) ?>
        </div>
    </aside>
    <div class="column-responsive column-80">
        <div class="projects view content">
            <h3><?= h($project->name) ?></h3>
            <div class="row">
                <div class="col-md-6">
                    <table>
                        <tr>
                            <th><?= __('Name') ?></th>
                            <td><?= h($project->name) ?></td>
                        </tr>
                        <tr>
                            <th><?= __('Customer') ?></th>
                            <td><?= $project->has('customer') ? $this->Html->link($project->customer->name, ['controller' => 'Customers', 'action' => 'view', $project->customer->id]) : '' ?></td>
                        </tr>
                        <tr>
                            <th><?= __('Invoice Number') ?></th>
                            <td><?= h($project->invoice_number) ?></td>
                        </tr>
                        <tr>
                            <th><?= __('Parent Project') ?></th>
                            <td><?= $project->has('parent_project') ? $this->Html->link($project->parent_project->name, ['controller' => 'Projects', 'action' => 'view', $project->parent_project->id]) : '' ?></td>
                        </tr>
                        <tr>
                            <th><?= __('Project Status') ?></th>
                            <td><?= $project->has('project_status') ? $this->Html->link($project->project_status->name, ['controller' => 'ProjectStatuses', 'action' => 'view', $project->project_status->id]) : '' ?></td>
                        </tr>
                        <!--
                        <tr>
                            <th><?= __('Id') ?></th>
                            <td><?= $this->Number->format($project->id) ?></td>
                        </tr>
                        -->
                        <tr>
                            <th><?= __('Hourly Rate') ?></th>
                            <td><?= $this->Number->format($project->hourly_rate) ?></td>
                        </tr>
                        <tr>
                            <th><?= __('Start') ?></th>
                            <td><?= h($project->start) ?></td>
                        </tr>
                        <tr>
                            <th><?= __('End Est') ?></th>
                            <td><?= h($project->end_est) ?></td>
                        </tr>
                        <tr>
                            <th><?= __('End') ?></th>
                            <td><?= h($project->end) ?></td>
                        </tr>
                        <tr>
                            <th><?= __('Invoice Date') ?></th>
                            <td><?= h($project->invoice_date) ?></td>
                        </tr>
                        <tr>
                            <th><?= __('Paid At') ?></th>
                            <td><?= h($project->paid_at) ?></td>
                        </tr>
                        <tr>
                            <th><?= __('Created') ?></th>
                            <td><?= h($project->created) ?></td>
                        </tr>
                        <tr>
                            <th><?= __('Fixed Price') ?></th>
                            <td><?= $project->fixed_price ? __('Yes') : __('No'); ?></td>
                        </tr>
                    </table>
                </div>

                <div class="col">
                    <?php if($project->notes) { ?>
                        <div class="text">
                            <strong><?= __('Notes') ?></strong>
                            <blockquote>
                                <?php
                                $text = h($project->notes);
                                $text = preg_replace('/(?:^|\s)#(\w+)/', ' <span class="text-info">#$1</span>', $text);
                                ?>
                                <?= $parsedown->parse($text); ?>
                            </blockquote>
                        </div>
                    <?php } ?>
                    <?php if($project->description) { ?>
                    <div class="text">
                        <strong><?= __('Description') ?></strong>
                        <blockquote>
                            <?= $parsedown->parse(h($project->description)); ?>
                        </blockquote>
                    </div>
                    <?php } ?>
                </div>
            </div>
            <?php if (!empty($project->child_projects)) : ?>
                <div class="related">
                    <h4><?= __('Related Projects') ?></h4>
                    <div class="table-responsive">
                        <table>
                            <tr>
                                <th><?= __('Id') ?></th>
                                <th><?= __('Name') ?></th>
                                <th><?= __('Customer Id') ?></th>
                                <th><?= __('Start') ?></th>
                                <th><?= __('End Est') ?></th>
                                <th><?= __('End') ?></th>
                                <th><?= __('Fixed Price') ?></th>
                                <th><?= __('Hourly Rate') ?></th>
                                <th><?= __('Notes') ?></th>
                                <th><?= __('Description') ?></th>
                                <th><?= __('Invoice Number') ?></th>
                                <th><?= __('Invoice Date') ?></th>
                                <th><?= __('Paid At') ?></th>
                                <th><?= __('Parent Id') ?></th>
                                <th><?= __('Project Status Id') ?></th>
                                <th><?= __('Created') ?></th>
                                <th class="actions"><?= __('Actions') ?></th>
                            </tr>
                            <?php foreach ($project->child_projects as $childProjects) : ?>
                                <tr>
                                    <td><?= h($childProjects->id) ?></td>
                                    <td><?= h($childProjects->name) ?></td>
                                    <td><?= h($childProjects->customer_id) ?></td>
                                    <td><?= h($childProjects->start) ?></td>
                                    <td><?= h($childProjects->end_est) ?></td>
                                    <td><?= h($childProjects->end) ?></td>
                                    <td><?= h($childProjects->fixed_price) ?></td>
                                    <td><?= h($childProjects->hourly_rate) ?></td>
                                    <td><?= h($childProjects->notes) ?></td>
                                    <td><?= h($childProjects->description) ?></td>
                                    <td><?= h($childProjects->invoice_number) ?></td>
                                    <td><?= h($childProjects->invoice_date) ?></td>
                                    <td><?= h($childProjects->paid_at) ?></td>
                                    <td><?= h($childProjects->parent_id) ?></td>
                                    <td><?= h($childProjects->project_status_id) ?></td>
                                    <td><?= h($childProjects->created) ?></td>
                                    <td class="actions">
                                        <?= $this->Html->link(__('View'), ['controller' => 'Projects', 'action' => 'view', $childProjects->id]) ?>
                                        <?= $this->Html->link(__('Edit'), ['controller' => 'Projects', 'action' => 'edit', $childProjects->id]) ?>
                                        <?= $this->Form->postLink(__('Delete'), ['controller' => 'Projects', 'action' => 'delete', $childProjects->id], ['confirm' => __('Are you sure you want to delete # {0}?', $childProjects->id)]) ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </table>
                    </div>
                </div>
            <?php endif; ?>
            <div class="related">
                <h4><?= __('Related Services') ?></h4>
                <div class="actions">
                    <a class="button-clear" href="/services/add?project_id=<?= $project->id ?>">Add Service</a>
                </div>
                <?php if (!empty($project->services)) : ?>
                    <div class="table-responsive">
                        <table>
                            <tr>
                                <th><?= __('Name') ?></th>
                                <th><?= __('Tasks') ?></th>
                                <th  class="text-end"><?= __('Effort Est') ?></th>
                                <th  class="text-end"><?= __('Effort') ?></th>
                                <th  class="text-end"><?= __('Costs') ?></th>
                                <th class="actions"><?= __('Actions') ?></th>
                            </tr>
                            <?php $effortSum = 0;
                            foreach ($project->services as $service) :
                                $effortSum += $service->effort();
                                $tasksCount = $service->countTasks();
                                $tasksDoneCount = $tasksCount["count"] - $tasksCount["todo"];
                                $trClass = "";
                                if($tasksCount["count"] == 0) {
                                    $trClass = "text-muted";
                                } else {
                                    $trClass = $tasksCount["count"] - $tasksDoneCount > 0 ? 'done-false' : 'done-true';
                                }
                                ?>
                                <tr class="<?= $trClass ?>">
                                    <td><?= $this->Html->link($service->name, ['controller' => 'Services', 'action' => 'view', $service->id]) ?></td>
                                    <td><?= $tasksDoneCount ?> / <?= $tasksCount["count"] ?></td>
                                    <td  class="text-end"><?= h($service->effort_est) ?></td>
                                    <td class="text-end"><?= h($service->effort()) ?></td>
                                    <td class="text-end"><?= $this->Number->currency($service->costs()) ?></td>
                                    <td class="actions">
                                        <?= $this->Html->link(__('View'), ['controller' => 'Services', 'action' => 'view', $service->id]) ?>
                                        <?= $this->Html->link(__('Edit'), ['controller' => 'Services', 'action' => 'edit', $service->id]) ?>
                                        <?= $this->Form->postLink(__('Delete'), ['controller' => 'Services', 'action' => 'delete', $service->id], ['confirm' => __('Are you sure you want to delete # {0}?', $service->id)]) ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            <tr>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td class="text-end"><b><?= $effortSum ?></b></td>
                                <td class="text-end"><b><?= $this->Number->currency($effortSum * $project->hourly_rate) ?></b></td>
                            </tr>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
