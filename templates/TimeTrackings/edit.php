<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\TimeTracking $timeTracking
 * @var string[]|\Cake\Collection\CollectionInterface $tasks
 */

$this->assign('title', "⏱️ " . $timeTracking->task->name);
?>
<div class="actions">
    <?= $this->Form->postLink(
        __('Delete'),
        ['action' => 'delete', $timeTracking->id],
        ['confirm' => __('Are you sure you want to delete # {0}?', $timeTracking->id), 'class' => 'side-nav-item']
    ) ?>
    <?= $this->Html->link(__('List Time Trackings'), ['action' => 'index'], ['class' => 'side-nav-item']) ?>
    <?= $this->Html->link(__('View Task'), ['controller' => 'Tasks', 'action' => 'view', $timeTracking->task->id]) ?>
</div>
<div class="timeTrackings form content">
    <?= $this->Form->create($timeTracking) ?>
    <fieldset>
        <legend><?= __('Time Tracking') ?> <?= $timeTracking->task->name ?></legend>
        <div class="stopwatch mb-3">
            <?= $this->Form->control('stopwatch'); ?>
            <button class="btn btn-success btn-sm btn-start" onclick="window.stopwatch.start(); return false;">start
            </button>
            <button class="btn btn-warning btn-sm btn-stop" onclick="window.stopwatch.stop(); return false;">pause
            </button>
            <button class="btn btn-danger btn-sm btn-reset" onclick="window.stopwatch.reset(); return false;">reset
            </button>
            <button class="btn btn-outline-primary btn-sm btn-stop" onclick="window.stopAndAdd(); return false;">add and
                save
            </button>
        </div>
        <?php
        echo $this->Form->control('task_id', ['options' => $tasks]);
        echo $this->Form->control('start', ['empty' => true]);
        // echo $this->Form->control('pause');
        echo $this->Form->control('duration');
        ?>
    </fieldset>
    <?= $this->Form->button(__('Submit')) ?>
    <?= $this->Form->end() ?>
</div>
<script type="module">
    import {Stopwatch} from "/lib/cm-web-modules/stopwatch/Stopwatch.js";

    const stopwatchOutput = document.getElementById("stopwatch")
    const durationInput = document.getElementById("duration")
    const form = document.getElementsByTagName("form")
    window.stopwatch = new Stopwatch({
        onTimeChanged: () => {
            const minutesExpired = stopwatch.secondsExpired() / 60
            stopwatchOutput.value = Math.round(minutesExpired * 100) / 100
        }
    })
    window.stopAndAdd = () => {
        if (!durationInput.value) {
            durationInput.value = 0
        }
        if (!stopwatchOutput.value) {
            stopwatchOutput.value = 0
        }
        durationInput.value = "" + (parseFloat(durationInput.value.replace(",", ".")) + parseFloat(stopwatchOutput.value) / 60)
        stopwatchOutput.value = 0
        form.submit()
    }
    // stopwatch.start()
</script>
