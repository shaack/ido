<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\TimeTracking $timeTracking
 * @var string[]|\Cake\Collection\CollectionInterface $tasks
 */

$taskName = $timeTracking->task->name ?: $timeTracking->task->service->name;
$this->assign('title', "⏱️" . $this->Text->truncate($taskName, 20));
?>
<!--
<div class="actions">
    <?= $this->Html->link(__('List Time Trackings'), ['action' => 'index'], ['class' => 'side-nav-item']) ?>
    <?= $this->Html->link(__('View Task'), ['controller' => 'Tasks', 'action' => 'view', $timeTracking->task->id]) ?>
    <?= $this->Html->link(__('Tasks List'), ['controller' => 'Tasks', 'action' => 'index']) ?>
</div>
-->
<div class="row">
    <div class="col">
        <h2>
            <i class="fa-solid fa-stopwatch"></i>️ <?= $timeTracking->task->name ?: "<i>" . $timeTracking->task->service->name . "</i>" ?>
        </h2>
    </div>
    <div class="col-auto"><?= $this->Form->postLink(
            __('Delete'),
            ['action' => 'delete', $timeTracking->id],
            ['confirm' => __('Are you sure you want to delete #{0}?', $timeTracking->id), 'class' => 'text-danger']
        ) ?></div>
</div>
<div class="timeTrackings form content">
    <?= $this->Form->create($timeTracking, ["id" => "time-tracking-form"]) ?>
    <fieldset>
        <div class="stopwatch mb-3">
            <div class="input-group mb-3" style="max-width: 320px">
                <input id="stopwatch" type="text" class="form-control text-end" placeholder="" name="stopwatch"/>
                <button type="button" id="button-start" class="btn btn-success">start
                </button>
                <button type="button" class="btn btn-warning btn-stop" onclick="window.stopwatch.stop(); return false;">
                    pause
                </button>
                <button type="button" class="btn btn-danger btn-reset"
                        onclick="window.stopwatch.stop(); window.stopwatch.reset(); return false;">reset
                </button>
            </div>
            <div class="progress mb-2">
                <div id="progress-bar" class="progress-bar bg-secondary" role="progressbar" aria-label="Basic example"
                     style=""></div>
            </div>
        </div>
        <div class="row">
            <div class="col"><?= $this->Form->control('duration') ?></div>
        </div>
    </fieldset>
    <fieldset>
        <?php
        echo $this->Form->control('task.notes', ['label' => 'Task Notes', 'rows' => 16, 'class' => 'font-monospace']);
        ?>
    </fieldset>
    <div class="mb-3">
        <button class="btn btn-secondary">Submit</button>
        <button class="btn btn-primary" onclick="window.stopAndAdd(); return false;">
            Add Stopwatch and Submit
        </button>
    </div>
    <?= $this->Form->end() ?>
</div>
<script type="module">
    import {Stopwatch} from "/lib/cm-web-modules/stopwatch/Stopwatch.js";
    import {Notifications} from "../../webroot/lib/cm-web-modules/notifications/Notifications.js";

    const stopwatchOutput = document.getElementById("stopwatch")
    const durationInput = document.getElementById("duration")
    const form = document.getElementById("time-tracking-form")
    let notificationShown = false
    const notifications = new Notifications()
    const progressBar = document.getElementById("progress-bar")
    const pomodoroMinutes = 25
    const title = document.title
    window.stopwatch = new Stopwatch({
        onTimeChanged: () => {
            const minutesExpired = stopwatch.secondsExpired() / 60
            document.title = title + " " + Math.round(minutesExpired * 100) / 100
            if (minutesExpired >= pomodoroMinutes && !notificationShown) {
                notificationShown = true
                notifications.show(pomodoroMinutes + " Minutes expired", "<?= h($timeTracking->task->name) ?>")
            }
            progressBar.style.width = minutesExpired / pomodoroMinutes * 100 + "%"
            stopwatchOutput.value = Math.round(minutesExpired * 100) / 100
        },
        onStateChanged: (running) => {
            if (running) {
                if (!progressBar.classList.contains("bg-primary")) {
                    progressBar.classList.add("bg-primary")
                    progressBar.classList.add("progress-bar-striped")
                    progressBar.classList.add("progress-bar-animated")
                    progressBar.classList.remove("bg-secondary")
                    progressBar.classList.remove("bg-secondary")
                }
            } else {
                if (progressBar.classList.contains("bg-primary")) {
                    progressBar.classList.remove("bg-primary")
                    progressBar.classList.remove("progress-bar-striped")
                    progressBar.classList.remove("progress-bar-animated")
                    progressBar.classList.add("bg-secondary")
                }
            }
        }
    })
    document.getElementById("button-start").addEventListener("click", (event) => {
        event.preventDefault()
        start()
    })

    function start() {
        try {
            notifications.requestPermission()
        } catch (e) {
            console.log(e)
        }
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
        const duration = (parseFloat(durationInput.value.replace(",", ".")) + parseFloat(stopwatchOutput.value) / 60).toFixed(2)
        durationInput.value = "" + duration
        console.log(durationInput.value)
        stopwatchOutput.value = 0
        form.submit()
    }
    stopwatch.start()
    /*
    if (<?= $timeTracking->duration > 0 ? "false" : "true" ?>) {
        stopwatch.start()
    }
    */
    // Allow TAB
    const textarea = document.querySelector('textarea')
    textarea.addEventListener('keydown', function (e) {
        if (e.key === 'Tab') {
            e.preventDefault();
            var start = this.selectionStart;
            var end = this.selectionEnd;

            // set textarea value to: text before caret + tab + text after caret
            this.value = this.value.substring(0, start) +
                "\t" + this.value.substring(end);

            // put caret at right position again
            this.selectionStart =
                this.selectionEnd = start + 1;
        }
    })
    textarea.style.tabSize = "4"
</script>
