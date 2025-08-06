<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Project $project
 * @var string[]|\Cake\Collection\CollectionInterface $customers
 * @var string[]|\Cake\Collection\CollectionInterface $parentProjects
 * @var string[]|\Cake\Collection\CollectionInterface $projectStatuses
 */
?>
<div class="row">
    <aside class="column">
        <div class="actions row">
            <div class="col">
            <?= $this->Html->link(__('View Project'), ['action' => 'view',  $project->id]) ?>
            <?= $this->Html->link(__('View Invoice'), ['action' => 'invoice',  $project->id]) ?>
            <?= $this->Html->link(__('List Projects'), ['action' => 'index']) ?>
            </div>
            <div class="col-auto text-end">
            <?= $this->Form->postLink(
                __('Delete'),
                ['action' => 'delete', $project->id],
                ['confirm' => __('Are you sure you want to delete # {0}?', $project->id), 'class' => 'text-danger' ],
            ) ?>
            </div>
        </div>
    </aside>
    <div class="column-responsive column-80">
        <div class="projects form content">
            <?= $this->Form->create($project) ?>
            <fieldset>
                <legend><?= __('Edit Project') ?></legend>
                <div class="row">
                    <div class="col-md-6"><?php
                        echo $this->Form->control('name');
                        echo $this->Form->control('customer_id', ['options' => $customers, 'empty' => true]);
                        echo $this->Form->control('start', ['empty' => true]);
                        echo $this->Form->control('end_est', ['empty' => true]);
                        echo $this->Form->control('end', ['empty' => true]);
                        echo $this->Form->control('fixed_price');
                        echo $this->Form->control('hourly_rate');
                        echo $this->Form->control('invoice_number');
                        echo $this->Form->control('invoice_date', ['empty' => true]);
                        echo $this->Form->control('invoice_type');
                        echo $this->Form->control('paid_at', ['empty' => true]);
                        echo $this->Form->control('parent_id', ['options' => $parentProjects, 'empty' => true]);
                        echo $this->Form->control('project_status_id', ['options' => $projectStatuses, 'empty' => true]);

                        ?></div>
                    <div class="col"><?php
                        echo $this->Form->control('notes', ["rows" => 15]);
                        echo $this->Form->control('description', ["rows" => 15]);
                        ?></div>
                </div>
            </fieldset>
            <?= $this->Form->button(__('Submit')) ?>
            <?= $this->Form->end() ?>
        </div>
    </div>
</div>
