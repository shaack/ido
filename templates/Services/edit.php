<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Service $service
 */
?>
<div class="row">
    <aside class="column">
        <div class="side-nav">
            <h4 class="heading"><?= __('Actions') ?></h4>
            <?= $this->Form->postLink(
                __('Delete'),
                ['action' => 'delete', $service->Id],
                ['confirm' => __('Are you sure you want to delete # {0}?', $service->Id), 'class' => 'side-nav-item']
            ) ?>
            <?= $this->Html->link(__('List Services'), ['action' => 'index'], ['class' => 'side-nav-item']) ?>
        </div>
    </aside>
    <div class="column-responsive column-80">
        <div class="services form content">
            <?= $this->Form->create($service) ?>
            <fieldset>
                <legend><?= __('Edit Service') ?></legend>
                <?php
                    echo $this->Form->control('title');
                    echo $this->Form->control('done');
                    echo $this->Form->control('project');
                    echo $this->Form->control('effort_est');
                    echo $this->Form->control('estimation_or_fixed_price');
                    echo $this->Form->control('effort');
                    echo $this->Form->control('effort2');
                    echo $this->Form->control('costs');
                    echo $this->Form->control('notes');
                ?>
            </fieldset>
            <?= $this->Form->button(__('Submit')) ?>
            <?= $this->Form->end() ?>
        </div>
    </div>
</div>
