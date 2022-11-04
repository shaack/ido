<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Project $project
 */
?>
<div class="row">
    <aside class="column">
        <div class="side-nav">
            <h4 class="heading"><?= __('Actions') ?></h4>
            <?= $this->Html->link(__('Edit Project'), ['action' => 'edit', $project->id], ['class' => 'side-nav-item']) ?>
            <?= $this->Form->postLink(__('Delete Project'), ['action' => 'delete', $project->id], ['confirm' => __('Are you sure you want to delete # {0}?', $project->id), 'class' => 'side-nav-item']) ?>
            <?= $this->Html->link(__('List Projects'), ['action' => 'index'], ['class' => 'side-nav-item']) ?>
            <?= $this->Html->link(__('New Project'), ['action' => 'add'], ['class' => 'side-nav-item']) ?>
        </div>
    </aside>
    <div class="column-responsive column-80">
        <div class="projects view content">
            <h3><?= h($project->name) ?></h3>
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
                <tr>
                    <th><?= __('Id') ?></th>
                    <td><?= $this->Number->format($project->id) ?></td>
                </tr>
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
            <div class="text">
                <strong><?= __('Notes') ?></strong>
                <blockquote>
                    <?= $this->Text->autoParagraph(h($project->notes)); ?>
                </blockquote>
            </div>
            <div class="text">
                <strong><?= __('Description') ?></strong>
                <blockquote>
                    <?= $this->Text->autoParagraph(h($project->description)); ?>
                </blockquote>
            </div>
            <div class="related">
                <h4><?= __('Related Projects') ?></h4>
                <?php if (!empty($project->child_projects)) : ?>
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
                <?php endif; ?>
            </div>
            <div class="related">
                <h4><?= __('Related Services') ?></h4>
                <a class="button-clear" href="/services/add?project_id=<?= $project->id ?>">Add Service</a>
                <?php if (!empty($project->services)) : ?>
                <div class="table-responsive">
                    <table>
                        <tr>
                            <!-- <th><?= __('Id') ?></th> -->
                            <th><?= __('Name') ?></th>
                            <!-- <th><?= __('Project Id') ?></th> -->
                            <th><?= __('Effort Est') ?></th>
                            <!-- <th><?= __('Estimation Or Fixed Price') ?></th> -->
                            <th><?= __('Effort') ?></th>
                            <th><?= __('Costs') ?></th>
                            <!-- <th><?= __('Notes') ?></th> -->
                            <!-- <th><?= __('Sort') ?></th> -->
                            <!-- <th><?= __('Created') ?></th> -->
                            <th class="actions"><?= __('Actions') ?></th>
                        </tr>
                        <?php foreach ($project->services as $services) : ?>
                        <tr>
                            <!-- <td><?= h($services->id) ?></td> -->
                            <td><?= $this->Html->link($services->name, ['controller' => 'Services', 'action' => 'view', $services->id]) ?></td>
                            <!-- <td><?= h($services->project_id) ?></td> -->
                            <td><?= h($services->effort_est) ?></td>
                            <!-- <td><?= h($services->estimation_or_fixed_price) ?></td> -->
                            <td><?= h($services->effort) ?></td>
                            <td><?= h($services->costs) ?></td>
                            <!-- <td><?= h($services->notes) ?></td>
                            <td><?= h($services->sort) ?></td>
                            <td><?= h($services->created) ?></td> -->
                            <td class="actions">
                                <?= $this->Html->link(__('View'), ['controller' => 'Services', 'action' => 'view', $services->id]) ?>
                                <?= $this->Html->link(__('Edit'), ['controller' => 'Services', 'action' => 'edit', $services->id]) ?>
                                <?= $this->Form->postLink(__('Delete'), ['controller' => 'Services', 'action' => 'delete', $services->id], ['confirm' => __('Are you sure you want to delete # {0}?', $services->id)]) ?>
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
