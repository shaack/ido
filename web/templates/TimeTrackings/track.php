<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\TimeTracking $timeTracking
 * @var double $doneToday
 * @var double $doneTask
 */

$this->assign('title', $this->Text->truncate($timeTracking->task->smartName, 20));
$customer = $timeTracking->task->service->project->customer;
$project = $timeTracking->task->service->project;
?>
<header class="page-head">
    <div>
        <p class="track-context">
            <?php if ($customer) : ?>
                <span class="customer-tag" style="--customer: <?= h($customer->color ?: 'currentColor') ?>"><?= h($customer->shortcut) ?></span>
            <?php endif; ?>
            <?= h($project->name) ?>
        </p>
        <h1 class="page-title"><?= $timeTracking->task->name ? h($timeTracking->task->name) : '<i>' . h($timeTracking->task->smartName) . '</i>' ?></h1>
    </div>
</header>
<div class="timeTrackings form content track">
    <?= $this->Form->create($timeTracking, ["id" => "time-tracking-form"]) ?>
    <fieldset>
        <div class="stopwatch">
            <label class="stopwatch-display" for="stopwatch">
                <input id="stopwatch" type="text" name="stopwatch" value="0" inputmode="decimal" autocomplete="off"/>
                <span><?= __('minutes') ?></span>
            </label>
            <div class="stopwatch-buttons">
                <button type="button" id="btn-start" class="btn btn-success btn-lg"><?= __('Start') ?></button>
                <button type="button" class="btn btn-outline-warning btn-lg btn-pause" id="btn-pause"><?= __('Pause') ?></button>
                <button type="button" class="btn btn-outline-secondary btn-modify" id="btn-minus" aria-label="<?= __('5 minutes less') ?>">−5</button>
                <button type="button" class="btn btn-outline-secondary btn-modify" id="btn-plus" aria-label="<?= __('5 minutes more') ?>">+5</button>
                <button type="button" class="btn btn-link btn-reset" id="btn-reset"><?= __('Reset') ?></button>
            </div>
            <div class="progress" role="presentation" title="<?= __('Fills every 30 minutes') ?>">
                <div id="progress-bar" class="progress-bar bg-secondary" style=""></div>
            </div>
        </div>
        <div class="track-booking">
            <div class="track-duration"><?= $this->Form->control('duration', ['label' => __('Booked on this entry, hours')]) ?></div>
            <dl class="track-figures">
                <div><dt><?= __('Today') ?></dt><dd><?= $this->Effort->hours((float)$doneToday) ?> h</dd></div>
                <div><dt><?= __('This task') ?></dt><dd><?= $this->Effort->hours((float)$doneTask) ?> h</dd></div>
            </dl>
        </div>
    </fieldset>
    <button class="btn btn-primary btn-lg" onclick="window.stopAndAdd(); return false;">
        <?= __('Add stopwatch time and save') ?>
    </button>
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
