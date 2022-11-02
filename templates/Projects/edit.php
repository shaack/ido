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
            <?= $this->Form->postLink(
                __('Delete'),
                ['action' => 'delete', $project->Id],
                ['confirm' => __('Are you sure you want to delete # {0}?', $project->Id), 'class' => 'side-nav-item']
            ) ?>
            <?= $this->Html->link(__('List Projects'), ['action' => 'index'], ['class' => 'side-nav-item']) ?>
        </div>
    </aside>
    <div class="column-responsive column-80">
        <div class="projects form content">
            <?= $this->Form->create($project) ?>
            <fieldset>
                <legend><?= __('Edit Project') ?></legend>
                <?php
                    echo $this->Form->control('title');
                    echo $this->Form->control('customer');
                    echo $this->Form->control('start', ['empty' => true]);
                    echo $this->Form->control('end_est', ['empty' => true]);
                    echo $this->Form->control('end', ['empty' => true]);
                    echo $this->Form->control('fixed_price');
                    echo $this->Form->control('hourly_rate');
                    echo $this->Form->control('status');
                    echo $this->Form->control('notes');
                    echo $this->Form->control('description');
                    echo $this->Form->control('invoice_number');
                    echo $this->Form->control('invoice_date', ['empty' => true]);
                    echo $this->Form->control('paid_at', ['empty' => true]);
                    echo $this->Form->control('advance_payment_of');
                ?>
            </fieldset>
            <?= $this->Form->button(__('Submit')) ?>
            <?= $this->Form->end() ?>
        </div>
    </div>
</div>
