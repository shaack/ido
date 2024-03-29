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
            <?= $this->Html->link(__('List Customers'), ['action' => 'index'], ['class' => 'side-nav-item']) ?>
        </div>
    </aside>
    <div class="column-responsive column-80">
        <div class="customers form content">
            <?= $this->Form->create($customer) ?>
            <fieldset>
                <legend><?= __('Add Customer') ?></legend>
                <?php
                    echo $this->Form->control('shortcut');
                    echo $this->Form->control('hourly_rate');
                    echo $this->Form->control('website');
                    echo $this->Form->control('notes');
                    echo $this->Form->control('color');
                    echo $this->Form->control('current');
                    echo $this->Form->control('internal');
                    echo $this->Form->control('name');
                    echo $this->Form->control('street');
                    echo $this->Form->control('zip_city');
                    echo $this->Form->control('country');
                    echo $this->Form->control('invoice_email');
                ?>
            </fieldset>
            <?= $this->Form->button(__('Submit')) ?>
            <?= $this->Form->end() ?>
        </div>
    </div>
</div>
