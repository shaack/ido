<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Customer $customer
 */
?>
<div class="row">
    <aside class="column">
        <div class="side-nav">
            <h4 class="heading"><?= __('Actions') ?></h4>
            <?= $this->Html->link(__('Edit Customer'), ['action' => 'edit', $customer->Id], ['class' => 'side-nav-item']) ?>
            <?= $this->Form->postLink(__('Delete Customer'), ['action' => 'delete', $customer->Id], ['confirm' => __('Are you sure you want to delete # {0}?', $customer->Id), 'class' => 'side-nav-item']) ?>
            <?= $this->Html->link(__('List Customers'), ['action' => 'index'], ['class' => 'side-nav-item']) ?>
            <?= $this->Html->link(__('New Customer'), ['action' => 'add'], ['class' => 'side-nav-item']) ?>
        </div>
    </aside>
    <div class="column-responsive column-80">
        <div class="customers view content">
            <h3><?= h($customer->name) ?></h3>
            <table>
                <tr>
                    <th><?= __('Id') ?></th>
                    <td><?= $this->Number->format($customer->Id) ?></td>
                </tr>
                <tr>
                    <th><?= __('Hourly Rate') ?></th>
                    <td><?= $customer->hourly_rate === null ? '' : $this->Number->format($customer->hourly_rate) ?></td>
                </tr>
            </table>
            <div class="text">
                <strong><?= __('Shortcut') ?></strong>
                <blockquote>
                    <?= $this->Text->autoParagraph(h($customer->shortcut)); ?>
                </blockquote>
            </div>
            <div class="text">
                <strong><?= __('Website') ?></strong>
                <blockquote>
                    <?= $this->Text->autoParagraph(h($customer->website)); ?>
                </blockquote>
            </div>
            <div class="text">
                <strong><?= __('Notes') ?></strong>
                <blockquote>
                    <?= $this->Text->autoParagraph(h($customer->notes)); ?>
                </blockquote>
            </div>
            <div class="text">
                <strong><?= __('Color') ?></strong>
                <blockquote>
                    <?= $this->Text->autoParagraph(h($customer->color)); ?>
                </blockquote>
            </div>
            <div class="text">
                <strong><?= __('Current') ?></strong>
                <blockquote>
                    <?= $this->Text->autoParagraph(h($customer->current)); ?>
                </blockquote>
            </div>
            <div class="text">
                <strong><?= __('Name') ?></strong>
                <blockquote>
                    <?= $this->Text->autoParagraph(h($customer->name)); ?>
                </blockquote>
            </div>
            <div class="text">
                <strong><?= __('Street') ?></strong>
                <blockquote>
                    <?= $this->Text->autoParagraph(h($customer->street)); ?>
                </blockquote>
            </div>
            <div class="text">
                <strong><?= __('Zip City') ?></strong>
                <blockquote>
                    <?= $this->Text->autoParagraph(h($customer->zip_city)); ?>
                </blockquote>
            </div>
            <div class="text">
                <strong><?= __('Country') ?></strong>
                <blockquote>
                    <?= $this->Text->autoParagraph(h($customer->country)); ?>
                </blockquote>
            </div>
            <div class="text">
                <strong><?= __('Invoice Email') ?></strong>
                <blockquote>
                    <?= $this->Text->autoParagraph(h($customer->invoice_email)); ?>
                </blockquote>
            </div>
        </div>
    </div>
</div>
