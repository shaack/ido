<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Task $task
 * @var \Cake\Collection\CollectionInterface|string[] $services
 */
?>
<div class="row">
    <div class="tasks form content">
        <?= $this->Form->create($task) ?>
        <fieldset>
            <legend><?= __('Add Task') ?></legend>
            <div class="row">
                <div class="col-md-8">
                    <?php
                    echo $this->Form->control('done');
                    echo $this->Form->control('marked');
                    echo $this->Form->control('name');
                    echo $this->Form->control('service_id', ['options' => $services, 'empty' => true]);
                    ?>
                </div>
                <div class="col">
                    <?php
                    ?>
                </div>
            </div>
        </fieldset>
        <?= $this->Form->button(__('Submit')) ?>
        <?= $this->Form->end() ?>
    </div>
</div>
