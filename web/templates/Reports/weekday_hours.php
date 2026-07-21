<?php
/**
 * @var \App\View\AppView $this
 * @var array<int, array{name: string, weekend: bool, hours: float}> $weekdays
 * @var int $windowDays
 * @var \Cake\I18n\Date $start
 * @var \Cake\I18n\Date $end
 * @var bool $includeInternal
 */
$this->assign('title', 'Arbeitszeit pro Wochentag');

// Geometrie des Säulendiagramms. Wie der Wochenreport ein reines Inline-SVG,
// damit kein Chart-Paket nötig ist und der Druck funktioniert.
$plotH = 260;
$step = 96;        // Abstand Säulenmitte zu Säulenmitte
$barW = 56;        // Säulenbreite
$padLeft = 40;
$padTop = 24;      // Platz für die Wertebeschriftung über den Säulen
$padBottom = 32;   // Platz für die Wochentagsnamen

$maxHours = 0.0;
foreach ($weekdays as $d) {
    $maxHours = max($maxHours, $d['hours']);
}
// Ganzzahlige Stunden-Gitterlinien, höchstens gut fünf Stufen.
$axisMax = max(1, (int)ceil($maxHours));
$labelStep = (int)ceil($axisMax / 5);
$gridSteps = (int)ceil($axisMax / $labelStep);
$axisMax = $labelStep * $gridSteps;
$yScale = $plotH / $axisMax;

$count = count($weekdays);
$width = $padLeft + $count * $step + 16;
$height = $padTop + $plotH + $padBottom;
$baseline = $padTop + $plotH;
?>
<div class="reports content">
    <h3 class="mt-5"><?= __('Arbeitszeit pro Wochentag') ?></h3>

    <div class="form-check form-switch mb-3">
        <input class="form-check-input" type="checkbox" role="switch" id="internalToggle"
               <?= $includeInternal ? 'checked' : '' ?>
               onchange="location.search = this.checked ? '?internal=1' : '?internal=0'">
        <label class="form-check-label" for="internalToggle"><?= __('Interne Projekte') ?></label>
    </div>

    <p class="text-muted">
        <?= __('Durchschnitt der letzten {0} Tage', $windowDays) ?>
        (<?= $start->i18nFormat('dd.MM.yyyy') ?> &ndash; <?= $end->i18nFormat('dd.MM.yyyy') ?>)
    </p>

    <div class="table-responsive">
        <svg viewBox="0 0 <?= $width ?> <?= $height ?>" width="<?= $width ?>" height="<?= $height ?>"
             role="img" aria-label="<?= __('Arbeitszeit pro Wochentag') ?>"
             style="max-width:100%; font-size:12px;">
            <?php // Gridlines und Y-Beschriftung ?>
            <?php for ($g = 0; $g <= $gridSteps; $g++):
                $value = $labelStep * $g;
                $y = $baseline - $value * $yScale; ?>
                <line x1="<?= $padLeft ?>" y1="<?= round($y, 1) ?>"
                      x2="<?= $width - 16 ?>" y2="<?= round($y, 1) ?>"
                      stroke="var(--bs-border-color)" stroke-width="1"/>
                <text x="<?= $padLeft - 6 ?>" y="<?= round($y + 4, 1) ?>"
                      text-anchor="end" fill="currentColor" opacity="0.7"><?= $value ?></text>
            <?php endfor; ?>

            <?php // Säulen mit Wert darüber und Wochentag darunter ?>
            <?php foreach ($weekdays as $i => $d):
                $x = $padLeft + $i * $step + ($step - $barW) / 2;
                $cx = $padLeft + $i * $step + $step / 2;
                $barH = $d['hours'] * $yScale;
                $y = $baseline - $barH; ?>
                <rect x="<?= round($x, 1) ?>" y="<?= round($y, 1) ?>"
                      width="<?= $barW ?>" height="<?= round($barH, 1) ?>"
                      fill="var(--bs-<?= $d['weekend'] ? 'secondary' : 'primary' ?>)" rx="2">
                    <title><?= h($d['name']) ?>: <?= $this->Effort->hours($d['hours']) ?> h</title>
                </rect>
                <text x="<?= round($cx, 1) ?>" y="<?= round($y - 6, 1) ?>"
                      text-anchor="middle" fill="currentColor" opacity="0.7">
                    <?= $this->Effort->hours($d['hours']) ?>
                </text>
                <text x="<?= round($cx, 1) ?>" y="<?= $baseline + 20 ?>"
                      text-anchor="middle" fill="currentColor"><?= h($d['name']) ?></text>
            <?php endforeach; ?>

            <?php // Basislinie ?>
            <line x1="<?= $padLeft ?>" y1="<?= $baseline ?>"
                  x2="<?= $width - 16 ?>" y2="<?= $baseline ?>"
                  stroke="currentColor" stroke-width="1" opacity="0.5"/>
        </svg>
    </div>
</div>
