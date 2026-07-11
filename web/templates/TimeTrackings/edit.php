<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\TimeTracking $timeTracking
 * @var string[]|\Cake\Collection\CollectionInterface $tasks
 */
$this->assign('title', __('Edit Time Tracking'));
?>
<div class="row">
    <aside class="column">
        <div class="side-nav">
            <h4 class="heading"><?= __('Actions') ?></h4>
            <?= $this->Html->link(__('Track'), ['action' => 'track', $timeTracking->id], ['class' => 'side-nav-item']) ?>
            <?= $this->Form->postLink(
                __('Delete'),
                ['action' => 'delete', $timeTracking->id],
                ['confirm' => __('Are you sure you want to delete # {0}?', $timeTracking->id), 'class' => 'side-nav-item text-danger']
            ) ?>
            <?= $this->Html->link(__('List Time Trackings'), ['action' => 'index'], ['class' => 'side-nav-item']) ?>
        </div>
    </aside>
    <div class="column-responsive column-80">
        <div class="timeTrackings form content">
            <?= $this->Form->create($timeTracking) ?>
            <fieldset>
                <legend><?= __('Edit Time Tracking') ?></legend>
                <?php
                    echo $this->Form->control('task_id', ['options' => $tasks]);
                    echo $this->Form->control('created', ['type' => 'datetime-local']);
                    echo $this->Form->control('duration', ['step' => 'any', 'label' => __('Duration (hours)')]);
                ?>
            </fieldset>
            <?= $this->Form->button(__('Submit')) ?>
            <?= $this->Form->end() ?>
        </div>
    </div>
</div>
