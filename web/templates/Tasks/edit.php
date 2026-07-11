<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Task $task
 * @var string[]|\Cake\Collection\CollectionInterface $services
 */
?>
<div class="actions">
    <div class="row">
        <div class="col">
            <?= $this->Html->link(__('View Task'), ['action' => 'view', $task->id]) ?>
            <?= $this->Html->link(__('List Tasks'), ['action' => 'index']) ?>
        </div>
        <div class="col-auto">
            <?= $this->Form->postLink(
                __('Delete'),
                ['action' => 'delete', $task->id],
                ['confirm' => __('Are you sure you want to delete # {0}?', $task->id), 'class' => 'text-danger']
            ) ?>
        </div>
    </div>
</div>
<div class="row">
    <div class="column-responsive column-80">
        <div class="tasks form content">
            <?= $this->Form->create($task) ?>
            <fieldset>
                <legend><?= __('Edit Task') ?></legend>
                <?php
                    echo $this->Form->control('done');
                    echo $this->Form->control('marked');
                    echo $this->Form->control('name');
                    echo $this->Form->control('service_id', ['options' => $services]);
                ?>
            </fieldset>
            <?= $this->Form->button(__('Submit')) ?>
            <?= $this->Form->end() ?>
        </div>
    </div>
</div>
