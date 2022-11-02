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
                    <th><?= $this->Paginator->sort('Id') ?></th>
                    <th><?= $this->Paginator->sort('customer') ?></th>
                    <th><?= $this->Paginator->sort('start') ?></th>
                    <th><?= $this->Paginator->sort('end_est') ?></th>
                    <th><?= $this->Paginator->sort('end') ?></th>
                    <th><?= $this->Paginator->sort('hourly_rate') ?></th>
                    <th><?= $this->Paginator->sort('invoice_date') ?></th>
                    <th><?= $this->Paginator->sort('paid_at') ?></th>
                    <th><?= $this->Paginator->sort('advance_payment_of') ?></th>
                    <th class="actions"><?= __('Actions') ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($projects as $project): ?>
                <tr>
                    <td><?= $this->Number->format($project->Id) ?></td>
                    <td><?= $project->customer === null ? '' : $this->Number->format($project->customer) ?></td>
                    <td><?= h($project->start) ?></td>
                    <td><?= h($project->end_est) ?></td>
                    <td><?= h($project->end) ?></td>
                    <td><?= $project->hourly_rate === null ? '' : $this->Number->format($project->hourly_rate) ?></td>
                    <td><?= h($project->invoice_date) ?></td>
                    <td><?= h($project->paid_at) ?></td>
                    <td><?= $project->advance_payment_of === null ? '' : $this->Number->format($project->advance_payment_of) ?></td>
                    <td class="actions">
                        <?= $this->Html->link(__('View'), ['action' => 'view', $project->Id]) ?>
                        <?= $this->Html->link(__('Edit'), ['action' => 'edit', $project->Id]) ?>
                        <?= $this->Form->postLink(__('Delete'), ['action' => 'delete', $project->Id], ['confirm' => __('Are you sure you want to delete # {0}?', $project->Id)]) ?>
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
