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
            <?= $this->Form->postLink(
                __('Delete'),
                ['action' => 'delete', $customer->id],
                ['confirm' => __('Are you sure you want to delete # {0}?', $customer->id), 'class' => 'side-nav-item']
            ) ?>
            <?= $this->Html->link(__('List Customers'), ['action' => 'index'], ['class' => 'side-nav-item']) ?>
        </div>
    </aside>
    <div class="column-responsive column-80">
        <div class="customers form content">
            <?= $this->Form->create($customer) ?>
            <fieldset>
                <legend><?= __('Edit Customer') ?></legend>
                <div class="row">
                    <div class="col-md-6">
                        <?php
                        echo $this->Form->control('shortcut');
                        echo $this->Form->control('hourly_rate');
                        echo $this->Form->control('website');
                        echo $this->Form->control('color');
                        echo $this->Form->control('current');
                        echo $this->Form->control('name');
                        echo $this->Form->control('street');
                        echo $this->Form->control('zip_city');
                        echo $this->Form->control('country');
                        echo $this->Form->control('address_addition');
                        echo $this->Form->control('invoice_email');
                        ?>
                    </div>
                    <div class="col">
                        <?php
                            echo $this->Form->control('notes');
                        ?>
                    </div>
                </div>
            </fieldset>
            <?= $this->Form->button(__('Submit')) ?>
            <?= $this->Form->end() ?>
        </div>
    </div>
</div>
