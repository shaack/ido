<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\ProjectStatus $projectStatus
 */
?>
<div class="row">
    <aside class="column">
        <div class="actions">
            <?= $this->Html->link(__('List Project Statuses'), ['action' => 'index']) ?>
        </div>
    </aside>
    <div class="column-responsive column-80">
        <div class="projectStatuses form content">
            <?= $this->Form->create($projectStatus) ?>
            <fieldset>
                <legend><?= __('Add Project Status') ?></legend>
                <?php
                    echo $this->Form->control('name');
                ?>
            </fieldset>
            <?= $this->Form->button(__('Submit')) ?>
            <?= $this->Form->end() ?>
        </div>
    </div>
</div>
