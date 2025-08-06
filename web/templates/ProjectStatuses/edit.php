<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\ProjectStatus $projectStatus
 */
?>
<div class="row">
    <aside class="column">
        <div class="side-nav">
            <h4 class="heading"><?= __('Actions') ?></h4>
            <?= $this->Form->postLink(
                __('Delete'),
                ['action' => 'delete', $projectStatus->id],
                ['confirm' => __('Are you sure you want to delete # {0}?', $projectStatus->id), 'class' => 'side-nav-item']
            ) ?>
            <?= $this->Html->link(__('List Project Statuses'), ['action' => 'index'], ['class' => 'side-nav-item']) ?>
        </div>
    </aside>
    <div class="column-responsive column-80">
        <div class="projectStatuses form content">
            <?= $this->Form->create($projectStatus) ?>
            <fieldset>
                <legend><?= __('Edit Project Status') ?></legend>
                <?php
                    echo $this->Form->control('name');
                ?>
            </fieldset>
            <?= $this->Form->button(__('Submit')) ?>
            <?= $this->Form->end() ?>
        </div>
    </div>
</div>
