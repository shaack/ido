<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Task $task
 * @var \Cake\Collection\CollectionInterface|string[] $services
 */
?>
<div class="actions">
    <?= $this->Html->link(__('List Tasks'), ['action' => 'index']) ?>
</div>
<div class="row">
    <div class="column-responsive column-80">
        <div class="tasks form content">
            <?= $this->Form->create($task) ?>
            <fieldset>
                <legend><?= __('Add Task') ?></legend>
                <?php
                    echo $this->Form->control('done');
                    echo $this->Form->control('marked');
                    echo $this->Form->control('name');
                    echo $this->Form->control('service_id', ['options' => $services, 'empty' => true]);
                ?>
            </fieldset>
            <?= $this->Form->button(__('Submit')) ?>
            <?= $this->Form->end() ?>
        </div>
    </div>
</div>
