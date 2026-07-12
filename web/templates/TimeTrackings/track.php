<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\TimeTracking $timeTracking
 * @var double $doneToday
 * @var double $doneTask
 */

$this->assign('title', $this->Text->truncate($timeTracking->task->smartName, 20));
$customer = $timeTracking->task->service->project->customer;

function doneClass($doneTime)
{
    $doneTodayClass = "text-muted opacity-50";
    if ($doneTime > 0) {
        $doneTodayClass = "text-muted";
        if ($doneTime >=3 && $doneTime < 4) {
            $doneTodayClass = "text-success";
        } else  if ($doneTime >= 4) {
            $doneTodayClass = "text-info";
        }
    }
    return $doneTodayClass;
}

?>
<div class="row">
    <div class="col">
        <h2 class="mb-3">
            <i class="fa-solid fa-stopwatch"></i>️ <?= $timeTracking->task->name ?: "<i>" . $timeTracking->task->smartName . "</i>" ?>
        </h2>
    </div>
    <!--
    <div class="col-auto"><?= $this->Form->postLink(
            __('Delete'),
            ['action' => 'delete', $timeTracking->id],
            ['confirm' => __('Are you sure you want to delete #{0}?', $timeTracking->id), 'class' => 'text-danger']
        ) ?></div>-->
</div>
<div class="timeTrackings form content">
    <?= $this->Form->create($timeTracking, ["id" => "time-tracking-form"]) ?>
    <fieldset>
        <div class="stopwatch mb-3">
            <div class="input-group mb-3" style="max-width: 380px">
                <input id="stopwatch" type="text" class="form-control text-end" placeholder="" name="stopwatch"
                       value="0"/>
                <button type="button" id="btn-start" class="btn btn-success">start
                </button>
                <button type="button" class="btn btn-warning btn-pause" id="btn-pause">
                    pause
                </button>
                <button type="button" class="btn btn-danger btn-reset" id="btn-reset">reset
                </button>
                <button type="button" class="btn btn-outline-secondary btn-modify" id="btn-minus">-5
                </button>
                <button type="button" class="btn btn-outline-secondary btn-modify" id="btn-plus">+5
                </button>
            </div>
            <div class="progress mb-2">
                <div id="progress-bar" class="progress-bar bg-secondary" role="progressbar" aria-label="Basic example"
                     style=""></div>
            </div>
        </div>
        <div class="row">
            <div class="col"><?= $this->Form->control('duration') ?></div>
            <div class="col-auto" style="margin-top: 1.9rem">
                <span class="<?= doneClass($doneToday) ?> me-2">
                Day <?= $this->Number->format($doneToday, ["places" => 2]) ?></span>
                Task <?= $this->Number->format($doneTask, ["places" => 2]) ?>
            </div>
        </div>
    </fieldset>
    <div class="mb-3">
        <!-- <button class="btn btn-secondary">Submit</button> -->
        <button class="btn btn-primary" onclick="window.stopAndAdd(); return false;">
            Add Stopwatch and Submit
        </button>
    </div>
    <?= $this->Form->end() ?>
</div>
<script type="module">
    import {Stopwatch} from "cm-web-modules/src/stopwatch/Stopwatch.js";
    import {Notifications} from "cm-web-modules/src/notifications/Notifications.js";

    const stopwatchOutput = document.getElementById("stopwatch")
    const durationInput = document.getElementById("duration")
    const form = document.getElementById("time-tracking-form")
    // Alle 30 Minuten eine Benachrichtigung, wie lange die Erfassung schon
    // läuft. Vorher gab es genau zwei, bei 25 und bei 60 Minuten, danach nie
    // wieder. Wer eine Erfassung vergaß, wurde also nicht mehr erinnert.
    const notifyEveryMinutes = 30
    let nextNotificationAt = notifyEveryMinutes
    const notifications = new Notifications()
    const progressBar = document.getElementById("progress-bar")
    const title = document.title
    let additionalMinutes = 0

    function updateTimerOutput() {
        const minutesExpired = stopwatch.secondsExpired() / 60 + additionalMinutes
        document.title = title + " ⏱️ " + (Math.round(minutesExpired * 100) / 100).toFixed(2)

        // Nur wenn die Uhr läuft. Sonst meldete auch der +5-Knopf eine "laufende"
        // Erfassung, sobald man sich damit über eine Schwelle hochklickt.
        //
        // while, nicht if: Ein schlafender Tab kann mehrere Schwellen auf einmal
        // überspringen. Gemeldet wird dann nur die zuletzt erreichte, statt eine
        // Flut nachzuschieben.
        if (stopwatch.running() && minutesExpired >= nextNotificationAt) {
            while (minutesExpired >= nextNotificationAt) {
                nextNotificationAt += notifyEveryMinutes
            }
            const reached = nextNotificationAt - notifyEveryMinutes
            notifications.show(
                "Running for " + reached + " minutes",
                "<?= h($timeTracking->task->name) ?>"
            )
        }

        // Der Balken füllt sich innerhalb des laufenden 30-Minuten-Abschnitts und
        // beginnt bei jeder Benachrichtigung von vorn. Die Farbe zeigt, in
        // welchem Abschnitt man ist.
        const minutesInBlock = ((minutesExpired % notifyEveryMinutes) + notifyEveryMinutes) % notifyEveryMinutes
        progressBar.style.width = (minutesInBlock / notifyEveryMinutes * 100) + "%"
        if (stopwatch.running()) {
            progressBar.classList.remove("bg-primary", "bg-success", "bg-warning")
            if (minutesExpired < notifyEveryMinutes) {
                progressBar.classList.add("bg-primary")
            } else if (minutesExpired < notifyEveryMinutes * 2) {
                progressBar.classList.add("bg-success")
            } else {
                progressBar.classList.add("bg-warning")
            }
        }

        stopwatchOutput.value = (Math.round(minutesExpired * 100) / 100).toFixed(2)
    }

    window.stopwatch = new Stopwatch({
        onTimeChanged: () => {
            updateTimerOutput()
        },
        onStateChanged: (running) => {
            if (running) {
                if (!progressBar.classList.contains("progress-bar-striped")) {
                    progressBar.classList.add("progress-bar-striped")
                    progressBar.classList.add("progress-bar-animated")
                    progressBar.classList.remove("bg-secondary")
                    // Die Farbe setzt updateTimerOutput passend zum laufenden
                    // 30-Minuten-Abschnitt. Hier stand sie doppelt, mit den alten
                    // Schwellen 25 und 60.
                    updateTimerOutput()
                }
            } else {
                if (progressBar.classList.contains("progress-bar-striped")) {
                    progressBar.className = ""
                    progressBar.classList.add("bg-secondary")
                }
            }
        }
    })

    document.getElementById("btn-start").addEventListener("click", (event) => {
        event.preventDefault()
        start()
    })
    document.getElementById("btn-pause").addEventListener("click", (event) => {
        event.preventDefault()
        window.stopwatch.stop()
        window.armIdleAlert()
    })
    document.getElementById("btn-reset").addEventListener("click", (event) => {
        event.preventDefault()
        additionalMinutes = 0
        // Die Uhr steht wieder auf null, also auch der nächste Meldezeitpunkt.
        nextNotificationAt = notifyEveryMinutes
        window.stopwatch.reset()
        window.armIdleAlert()
    })
    document.getElementById("btn-minus").addEventListener("click", (event) => {
        event.preventDefault()
        additionalMinutes = additionalMinutes - 5
        updateTimerOutput()
    })
    document.getElementById("btn-plus").addEventListener("click", (event) => {
        event.preventDefault()
        additionalMinutes = additionalMinutes + 5
        updateTimerOutput()
    })

    function start() {
        try {
            notifications.requestPermission()
        } catch (e) {
            console.log(e)
        }
        // Kein Zurücksetzen des Meldezeitpunkts: Nach einer Pause läuft die Zeit
        // weiter, die nächste Schwelle liegt also ohnehin voraus. Vorher wurde
        // hier pomodoroExpired zurückgesetzt, was nach jeder Pause erneut bei 25
        // Minuten meldete, obwohl die Uhr längst darüber stand.
        //
        // Die Untätigkeits-Erinnerung wird abgeräumt. Vorher blieb ihr Timeout
        // stehen und feuerte blind weiter. Dass er nichts anrichtete, lag nur an
        // der Prüfung in seinem Rumpf.
        window.clearIdleAlert()
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
    updateTimerOutput()

    // Erinnerung alle 15 Minuten, solange die Stoppuhr steht. Vorher war es ein
    // einmaliger setTimeout nach 5 Minuten: Wer die Uhr pausierte und vergaß,
    // bekam genau eine Erinnerung und danach nie wieder eine.
    const idleEveryMinutes = 15

    window.armIdleAlert = () => {
        clearInterval(window.idleInterval)
        window.idleInterval = setInterval(() => {
            if (stopwatch.running()) {
                // Sicherheitsnetz. Sollte das Abräumen beim Start je ausbleiben,
                // beendet sich das Intervall hier selbst, statt eine laufende
                // Uhr als untätig zu melden.
                clearInterval(window.idleInterval)
                return
            }
            notifications.show("No timer running", "<?= h($timeTracking->task->name) ?>")
        }, 1000 * 60 * idleEveryMinutes)
    }

    window.clearIdleAlert = () => {
        clearInterval(window.idleInterval)
    }

    notifications.requestPermission()
    window.armIdleAlert()
</script>
