<?php
/**
 * @var \App\View\AppView $this
 * @var iterable<\App\Model\Entity\Project> $projects
 */
?>
<div class="projects index content">
    <?= $this->Html->link(__('New Project'), ['action' => 'add'], ['class' => 'button float-right']) ?>
    <h3><?= __('Projects') ?></h3>
    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th><?= $this->Paginator->sort('id') ?></th>
                    <th><?= $this->Paginator->sort('name') ?></th>
                    <th><?= $this->Paginator->sort('customer_id') ?></th>
                    <th><?= $this->Paginator->sort('start') ?></th>
                    <th><?= $this->Paginator->sort('end_est') ?></th>
                    <th><?= $this->Paginator->sort('end') ?></th>
                    <th><?= $this->Paginator->sort('fixed_price') ?></th>
                    <th><?= $this->Paginator->sort('hourly_rate') ?></th>
                    <th><?= $this->Paginator->sort('invoice_number') ?></th>
                    <th><?= $this->Paginator->sort('invoice_date') ?></th>
                    <th><?= $this->Paginator->sort('paid_at') ?></th>
                    <th><?= $this->Paginator->sort('parent_id') ?></th>
                    <th><?= $this->Paginator->sort('project_status_id') ?></th>
                    <th><?= $this->Paginator->sort('created') ?></th>
                    <th class="actions"><?= __('Actions') ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($projects as $project): ?>
                <tr>
                    <td><?= $this->Number->format($project->id) ?></td>
                    <td><?= h($project->name) ?></td>
                    <td><?= $project->has('customer') ? $this->Html->link($project->customer->name, ['controller' => 'Customers', 'action' => 'view', $project->customer->id]) : '' ?></td>
                    <td><?= h($project->start) ?></td>
                    <td><?= h($project->end_est) ?></td>
                    <td><?= h($project->end) ?></td>
                    <td><?= h($project->fixed_price) ?></td>
                    <td><?= $this->Number->format($project->hourly_rate) ?></td>
                    <td><?= h($project->invoice_number) ?></td>
                    <td><?= h($project->invoice_date) ?></td>
                    <td><?= h($project->paid_at) ?></td>
                    <td><?= $project->has('parent_project') ? $this->Html->link($project->parent_project->name, ['controller' => 'Projects', 'action' => 'view', $project->parent_project->id]) : '' ?></td>
                    <td><?= $project->has('project_status') ? $this->Html->link($project->project_status->name, ['controller' => 'ProjectStatuses', 'action' => 'view', $project->project_status->id]) : '' ?></td>
                    <td><?= h($project->created) ?></td>
                    <td class="actions">
                        <?= $this->Html->link(__('View'), ['action' => 'view', $project->id]) ?>
                        <?= $this->Html->link(__('Edit'), ['action' => 'edit', $project->id]) ?>
                        <?= $this->Form->postLink(__('Delete'), ['action' => 'delete', $project->id], ['confirm' => __('Are you sure you want to delete # {0}?', $project->id)]) ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <div class="paginator">
        <ul class="pagination">
            <?= $this->Paginator->first('<< ' . __('first')) ?>
            <?= $this->Paginator->prev('< ' . __('previous')) ?>
            <?= $this->Paginator->numbers() ?>
            <?= $this->Paginator->next(__('next') . ' >') ?>
            <?= $this->Paginator->last(__('last') . ' >>') ?>
        </ul>
        <p><?= $this->Paginator->counter(__('Page {{page}} of {{pages}}, showing {{current}} record(s) out of {{count}} total')) ?></p>
    </div>
</div>
