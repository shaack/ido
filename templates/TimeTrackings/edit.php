<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\TimeTracking $timeTracking
 * @var string[]|\Cake\Collection\CollectionInterface $tasks
 */
?>
<div class="row">
    <aside class="column">
        <div class="side-nav">
            <h4 class="heading"><?= __('Actions') ?></h4>
            <?= $this->Form->postLink(
                __('Delete'),
                ['action' => 'delete', $timeTracking->id],
                ['confirm' => __('Are you sure you want to delete # {0}?', $timeTracking->id), 'class' => 'side-nav-item']
            ) ?>
            <?= $this->Html->link(__('List Time Trackings'), ['action' => 'index'], ['class' => 'side-nav-item']) ?>
        </div>
    </aside>
    <div class="column-responsive column-80">
        <div class="timeTrackings form content">
            <?= $this->Form->create($timeTracking) ?>
            <fieldset>
                <legend><?= __('Edit Time Tracking') ?></legend>
                <div class="stopwatch">
                    <?= $this->Form->control('stopwatch'); ?>
                    <button class="btn btn-success btn-start" onclick="window.stopwatch.start(); return false;">start</button>
                    <button class="btn btn-warning btn-stop" onclick="window.stopwatch.stop(); return false;">pause</button>
                    <button class="btn btn-danger btn-reset" onclick="window.stopwatch.reset(); return false;">reset</button>
                    <button class="btn btn-primary btn-stop" onclick="window.stopAndAdd(); return false;">stop and add</button>
                </div>
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
<script type="module">
    import {Stopwatch} from "/lib/cm-web-modules/stopwatch/Stopwatch.js";

    const stopwatchOutput = document.getElementById("stopwatch")
    const durationInput = document.getElementById("duration")
    window.stopwatch = new Stopwatch({
        onTimeChanged: () => {
            const minutesExpired = stopwatch.secondsExpired() / 60
            stopwatchOutput.value = Math.round(minutesExpired * 100) / 100
        }
    })
    window.stopAndAdd = () => {
        if(!durationInput.value) {
            durationInput.value = 0
        }
        durationInput.value = "" + (parseFloat(durationInput.value.replace(",", ".")) + parseFloat(stopwatchOutput.value) / 60)
        stopwatchOutput.value = 0
        window.stopwatch.stop()
        window.stopwatch.reset()
    }
    stopwatch.start()
</script>
