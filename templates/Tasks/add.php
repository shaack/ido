<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Task $task
 * @var \Cake\Collection\CollectionInterface|string[] $services
 */
?>
<div class="row">
    <aside class="column">
        <div class="side-nav">
            <h4 class="heading"><?= __('Actions') ?></h4>
            <?= $this->Html->link(__('List Tasks'), ['action' => 'index'], ['class' => 'side-nav-item']) ?>
        </div>
    </aside>
    <div class="column-responsive column-80">
        <div class="tasks form content">
            <?= $this->Form->create($task) ?>
            <fieldset>
                <legend><?= __('Add Task') ?></legend>
                <?php
                    echo $this->Form->control('marked');
                    echo $this->Form->control('prio');
                    echo $this->Form->control('name');
                    echo $this->Form->control('start_est', ['empty' => true]);
                    echo $this->Form->control('deadline', ['empty' => true]);
                    echo $this->Form->control('duration_est');
                    echo $this->Form->control('link');
                    echo $this->Form->control('service_id', ['options' => $services, 'empty' => true]);
                    echo $this->Form->control('notes');
                    echo $this->Form->control('done');
                    echo $this->Form->control('done_at', ['empty' => true]);
                    echo $this->Form->control('duration');
                ?>
            </fieldset>
            <?= $this->Form->button(__('Submit')) ?>
            <?= $this->Form->end() ?>
        </div>
    </div>
</div>
