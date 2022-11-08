<?php
/**
 * @var \App\View\AppView $this
 * @var iterable<\App\Model\Entity\Service> $services
 */
?>
<div class="services index content">
    <h3><?= __('Services') ?></h3>
    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <!-- <th><?= $this->Paginator->sort('id') ?></th> -->
                    <th><?= $this->Paginator->sort('name') ?></th>
                    <th><?= $this->Paginator->sort('project_id') ?></th>
                    <th><?= $this->Paginator->sort('effort_est') ?></th>
                    <th><?= $this->Paginator->sort('estimation_or_fixed_price') ?></th>
                    <th><?= $this->Paginator->sort('effort') ?></th>
                    <th><?= $this->Paginator->sort('costs') ?></th>
                    <!-- <th><?= $this->Paginator->sort('sort') ?></th> -->
                    <!-- <th><?= $this->Paginator->sort('created') ?></th> -->
                    <th class="actions"><?= __('Actions') ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($services as $service): ?>
                <tr>
                    <!-- <td><?= $this->Number->format($service->id) ?></td> -->
                    <td><?= $this->Html->link($service->name, ['action' => 'view', $service->id]) ?></td>
                    <td><?= $service->has('project') ? $this->Html->link($service->project->name, ['controller' => 'Projects', 'action' => 'view', $service->project->id]) : '' ?></td>
                    <td><?= $service->effort_est === null ? '' : $this->Number->format($service->effort_est) ?></td>
                    <td><?= $service->estimation_or_fixed_price === null ? '' : $this->Number->format($service->estimation_or_fixed_price) ?></td>
                    <td><?= $service->effort === null ? '' : $this->Number->format($service->effort) ?></td>
                    <td><?= $service->costs === null ? '' : $this->Number->format($service->costs) ?></td>
                    <!-- <td><?= $service->sort === null ? '' : $this->Number->format($service->sort) ?></td>
                    <td><?= h($service->created) ?></td> -->
                    <td class="actions">
                        <?= $this->Html->link(__('Edit'), ['action' => 'edit', $service->id]) ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <div class="paginator">
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
    </div>
</div>
