<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\TimeTracking $timeTracking
 * @var \Cake\Collection\CollectionInterface|string[] $tasks
 */
?>
<div class="row">
    <aside class="column">
        <div class="side-nav">
            <h4 class="heading"><?= __('Actions') ?></h4>
            <?= $this->Html->link(__('List Time Trackings'), ['action' => 'index'], ['class' => 'side-nav-item']) ?>
        </div>
    </aside>
    <div class="column-responsive column-80">
        <div class="timeTrackings form content">
            <?= $this->Form->create($timeTracking) ?>
            <fieldset>
                <legend><?= __('Add Time Tracking') ?></legend>
                <?php
                    echo $this->Form->control('task_id', ['options' => $tasks]);
                    echo $this->Form->control('start', ['empty' => true]);
                    echo $this->Form->control('pause');
                    echo $this->Form->control('duration');
                ?>
            </fieldset>
            <?= $this->Form->button(__('Submit')) ?>
            <?= $this->Form->end() ?>
        </div>
    </div>
</div>
