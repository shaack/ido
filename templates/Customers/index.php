<?php
/**
 * @var \App\View\AppView $this
 * @var iterable<\App\Model\Entity\Customer> $customers
 */
?>
<div class="customers index content">
    <div class="row d-flex align-items-center mb-2">
        <div class="col"><h3 class="m-0"><?= __('Customers') ?></h3></div>
        <div class="col text-end"><?= $this->Html->link(__('New Customer'), ['action' => 'add'], ['class' => 'btn btn-primary btn-sm float-right']) ?></div>
    </div>
    <div class="table-responsive">
        <table>
            <thead>
            <tr>
                <!-- <th><?= $this->Paginator->sort('id') ?></th> -->
                <th><?= $this->Paginator->sort('shortcut') ?></th>
                <!-- <th><?= $this->Paginator->sort('hourly_rate') ?></th> -->
                <!-- <th><?= $this->Paginator->sort('website') ?></th> -->
                <!-- <th><?= $this->Paginator->sort('color') ?></th> -->
                <!-- <th><?= $this->Paginator->sort('current') ?></th> -->
                <th><?= $this->Paginator->sort('name') ?></th>
                <!-- <th><?= $this->Paginator->sort('street') ?></th> -->
                <!-- <th><?= $this->Paginator->sort('zip_city') ?></th> -->
                <!-- <th><?= $this->Paginator->sort('country') ?></th> -->
                <th><?= $this->Paginator->sort('invoice_email') ?></th>
                <!-- <th><?= $this->Paginator->sort('created') ?></th> -->
                <th class="actions"><?= __('Actions') ?></th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($customers as $customer): ?>
                <tr>
                    <!-- <td><?= $this->Number->format($customer->id) ?></td> -->
                    <td style="color: <?= $customer->color ?>"><?= h($customer->shortcut) ?></td>
                    <!-- <td><?= $customer->hourly_rate === null ? '' : $this->Number->format($customer->hourly_rate) ?></td>
                    <td><?= h($customer->website) ?></td>
                    <td><?= h($customer->color) ?></td>
                    <td><?= h($customer->current) ?></td> -->
                    <td><?= $this->Html->link($customer->name, ['action' => 'view', $customer->id]) ?></td>
                    <!-- <td><?= h($customer->street) ?></td>
                    <td><?= h($customer->zip_city) ?></td>
                    <td><?= h($customer->country) ?></td>-->
                    <td><a href="mailto:<?= $customer->invoice_email ?>"><?= $customer->invoice_email ?></a></td>
                    <!--<td><?= h($customer->created) ?></td> -->
                    <td class="actions">
                        <?= $this->Html->link(__('Edit'), ['action' => 'edit', $customer->id]) ?>
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
