<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Task $task
 * @var string[]|\Cake\Collection\CollectionInterface $services
 */
?>
<div class="actions">
    <?= $this->Form->postLink(
        __('Delete'),
        ['action' => 'delete', $task->id],
        ['confirm' => __('Are you sure you want to delete # {0}?', $task->id), 'class' => 'side-nav-item']
    ) ?>
    <?= $this->Html->link(__('View Task'), ['action' => 'view', $task->id]) ?>
    <?= $this->Html->link(__('List Tasks'), ['action' => 'index'], ['class' => 'side-nav-item']) ?>
</div>
<div class="tasks form content">
    <?= $this->Form->create($task) ?>
    <fieldset>
        <legend><?= __('Edit Task') ?></legend>
        <div class="row">
            <div class="col-md">
                <?php
                echo $this->Form->control('done');
                echo $this->Form->control('marked');
                echo $this->Form->control('name');
                echo $this->Form->control('prio');
                echo $this->Form->control('start_est', ['empty' => true]);
                echo $this->Form->control('deadline', ['empty' => true]);
                echo $this->Form->control('duration_est');
                // echo $this->Form->control('link');
                echo $this->Form->control('service_id', ['options' => $services, 'empty' => true]);
                // echo $this->Form->control('done_at', ['empty' => true]);
                // echo $this->Form->control('duration');
                ?>
            </div>
            <div class="col-md">
                <?php
                echo $this->Form->control('notes', ['rows' => '15', 'class' => 'markdown']);
                ?>
            </div>
        </div>
    </fieldset>
    <?= $this->Form->button(__('Submit')) ?>
    <?= $this->Form->end() ?>
</div>
