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
        <legend><?= __('Time Tracking') ?>: <?= $timeTracking->task->name ?></legend>
        <div class="stopwatch mb-3">
            <?= $this->Form->control('stopwatch'); ?>
            <div class="progress mb-2">
                <div id="progress-bar" class="progress-bar bg-success" role="progressbar" aria-label="Basic example" style=""></div>
            </div>
            <button class="btn btn-success btn-sm btn-start" onclick="window.start(); return false;">start
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
    import {Notifications} from "../../webroot/lib/cm-web-modules/notifications/Notifications.js";

    const stopwatchOutput = document.getElementById("stopwatch")
    const durationInput = document.getElementById("duration")
    const form = document.getElementsByTagName("form")
    let notificationShown = false
    const notifications = new Notifications()
    const progressBar = document.getElementById("progress-bar")
    const pomodoroMinutes = 25
    window.stopwatch = new Stopwatch({
        onTimeChanged: () => {
            const minutesExpired = stopwatch.secondsExpired() / 60
            if(minutesExpired >= pomodoroMinutes && !notificationShown) {
                notificationShown = true
                notifications.show(pomodoroMinutes + " Minutes expired", "<?= h($timeTracking->task->name) ?>")
            }
            progressBar.style.width = minutesExpired / pomodoroMinutes * 100 + "%"
            stopwatchOutput.value = Math.round(minutesExpired * 100) / 100
        }
    })
    window.start = () => {
        notifications.requestPermission()
        notificationShown = false
        window.stopwatch.start()
    }
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
