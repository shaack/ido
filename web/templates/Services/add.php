<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Service $service
 * @var \Cake\Collection\CollectionInterface|string[] $projects
 */
?>
<div class="actions">
    <div class="row">
        <div class="col">
            <?= $this->Html->link(__('View Service'), ['action' => 'view', $service->id]) ?>
            <?= $this->Html->link(__('View Project'), ['action' => 'view', 'controller' => 'projects', $service->project_id]) ?>
        </div>
        <div class="col-auto">
            <?= $this->Form->postLink(
                __('Delete'),
                ['action' => 'delete', $service->id],
                ['confirm' => __('Are you sure you want to delete # {0}?', $service->id)]
            ) ?>
        </div>
    </div>
</div>
<div class="row">
    <div class="column-responsive column-80">
        <div class="services form content">
            <?= $this->Form->create($service) ?>
            <fieldset>
                <legend><?= __('Add Service') ?></legend>
                <?php
                    echo $this->Form->control('name');
                    echo $this->Form->control('project_id', ['options' => $projects]);
                    echo $this->Form->control('effort_est');
                    echo $this->Form->control('fixed_price');
                    echo $this->Form->control('sort');
                ?>
            </fieldset>
            <?= $this->Form->button(__('Submit')) ?>
            <?= $this->Form->end() ?>
        </div>
    </div>
</div>
