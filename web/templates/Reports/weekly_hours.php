<?php
/**
 * @var \App\View\AppView $this
 * @var array<int, array{label: string, hours: float}> $weeks
 * @var float $totalHours
 * @var float $averageHours
 * @var int $weekCount
 * @var bool $includeInternal
 */
$this->assign('title', 'Arbeitszeit pro Woche');

// Geometrie des Balkendiagramms. Die Zeichnung ist ein reines Inline-SVG,
// damit kein zusätzliches Chart-Paket nötig ist und der Report auch im
// Druck-Layout funktioniert.
$plotH = 300;      // Höhe der Zeichenfläche
$barW = 2;         // Balkenbreite
$gap = 1;          // Abstand zwischen zwei Balken
$step = $barW + $gap; // Abstand Balkenmitte zu Balkenmitte
$padLeft = 48;     // Platz für die Y-Beschriftung
$padTop = 16;
$padBottom = 64;   // Platz für die gedrehte X-Beschriftung

$count = count($weeks);
$maxHours = 0.0;
foreach ($weeks as $w) {
    $maxHours = max($maxHours, $w['hours']);
}
// Y-Achse auf das nächste Vielfache von 5 aufrunden, mindestens 5.
$axisMax = max(5.0, ceil($maxHours / 5) * 5);
$yScale = $plotH / $axisMax;

$width = $padLeft + max(1, $count) * $step + 16;
$height = $padTop + $plotH + $padBottom;
$baseline = $padTop + $plotH;

// Nicht jede Woche beschriften, sonst überlappen die Labels. Ziel: ~40 Marken.
$labelEvery = max(1, (int)ceil($count / 40));

$gridSteps = 5;

// Gleitender Mittelwert als Trendkurve. Zentriertes Fenster von $avgWindow
// Wochen, an den Rändern auf die vorhandenen Wochen beschnitten. Glättet die
// wöchentlichen Ausschläge zu einer lesbaren Kurve.
$avgWindow = 20;
$avgPoints = [];
foreach ($weeks as $i => $w) {
    $from = max(0, $i - (int)floor($avgWindow / 2));
    $to = min($count - 1, $i + (int)ceil($avgWindow / 2) - 1);
    $sum = 0.0;
    for ($j = $from; $j <= $to; $j++) {
        $sum += $weeks[$j]['hours'];
    }
    $mean = $sum / ($to - $from + 1);
    $x = $padLeft + $i * $step + $step / 2;
    $y = $baseline - $mean * $yScale;
    $avgPoints[] = round($x, 1) . ',' . round($y, 1);
}
?>
<div class="reports content">
    <h3 class="mt-5"><?= __('Arbeitszeit pro Woche') ?></h3>

    <div class="form-check form-switch mb-3">
        <input class="form-check-input" type="checkbox" role="switch" id="internalToggle"
               <?= $includeInternal ? 'checked' : '' ?>
               onchange="location.search = this.checked ? '?internal=1' : '?internal=0'">
        <label class="form-check-label" for="internalToggle"><?= __('Interne Projekte') ?></label>
    </div>

    <?php if (!$count): ?>
        <p class="text-muted"><?= __('Noch keine Zeiten erfasst.') ?></p>
    <?php else: ?>
        <p class="text-muted">
            <?= $weekCount ?> <?= __('Wochen') ?> &middot;
            <?= __('Summe') ?> <?= $this->Effort->hours($totalHours) ?> h &middot;
            <?= __('Durchschnitt') ?> <?= $this->Effort->hours($averageHours) ?> h/<?= __('Woche') ?>
            &middot;
            <span style="color:var(--bs-orange)">&#9473;</span>
            <?= __('gleitender Mittelwert') ?> (<?= $avgWindow ?> <?= __('Wochen') ?>)
        </p>

        <div class="table-responsive">
            <svg viewBox="0 0 <?= $width ?> <?= $height ?>" width="<?= $width ?>" height="<?= $height ?>"
                 role="img" aria-label="<?= __('Arbeitszeit pro Woche') ?>"
                 style="max-width:none; font-size:11px;">
                <?php // Gridlines und Y-Beschriftung ?>
                <?php for ($g = 0; $g <= $gridSteps; $g++):
                    $value = $axisMax * $g / $gridSteps;
                    $y = $baseline - $value * $yScale; ?>
                    <line x1="<?= $padLeft ?>" y1="<?= round($y, 1) ?>"
                          x2="<?= $width - 16 ?>" y2="<?= round($y, 1) ?>"
                          stroke="var(--bs-border-color)" stroke-width="1"/>
                    <text x="<?= $padLeft - 6 ?>" y="<?= round($y + 3, 1) ?>"
                          text-anchor="end" fill="currentColor" opacity="0.7">
                        <?= rtrim(rtrim(number_format($value, 1, ',', ''), '0'), ',') ?>
                    </text>
                <?php endfor; ?>

                <?php // Balken ?>
                <?php foreach ($weeks as $i => $w):
                    $x = $padLeft + $i * $step + ($step - $barW) / 2;
                    $barH = $w['hours'] * $yScale;
                    $y = $baseline - $barH; ?>
                    <rect x="<?= round($x, 1) ?>" y="<?= round($y, 1) ?>"
                          width="<?= $barW ?>" height="<?= round($barH, 1) ?>"
                          fill="var(--bs-primary)">
                        <title><?= h($w['label']) ?>: <?= $this->Effort->hours($w['hours']) ?> h</title>
                    </rect>
                <?php endforeach; ?>

                <?php // Mittelwertkurve (gleitender Mittelwert) über den Balken ?>
                <polyline points="<?= implode(' ', $avgPoints) ?>"
                          fill="none" stroke="var(--bs-orange)" stroke-width="2"
                          stroke-linejoin="round" stroke-linecap="round"/>

                <?php // X-Beschriftung, gedreht ?>
                <?php foreach ($weeks as $i => $w):
                    if ($i % $labelEvery !== 0) {
                        continue;
                    }
                    $x = $padLeft + $i * $step + $step / 2; ?>
                    <text x="<?= round($x, 1) ?>" y="<?= $baseline + 12 ?>"
                          fill="currentColor" opacity="0.7"
                          text-anchor="end"
                          transform="rotate(-60 <?= round($x, 1) ?> <?= $baseline + 12 ?>)">
                        <?= h($w['label']) ?>
                    </text>
                <?php endforeach; ?>

                <?php // Basislinie ?>
                <line x1="<?= $padLeft ?>" y1="<?= $baseline ?>"
                      x2="<?= $width - 16 ?>" y2="<?= $baseline ?>"
                      stroke="currentColor" stroke-width="1" opacity="0.5"/>
            </svg>
        </div>

        <details class="mt-4">
            <summary class="text-muted"><?= __('Werte als Tabelle') ?></summary>
            <div class="table-responsive mt-2">
                <table class="table w-auto">
                    <thead>
                    <tr>
                        <th><?= __('Woche') ?></th>
                        <th class="text-end"><?= __('Stunden') ?></th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach (array_reverse($weeks) as $w): ?>
                        <tr>
                            <td><?= h($w['label']) ?></td>
                            <td class="text-end"><?= $this->Effort->hours($w['hours']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </details>
    <?php endif; ?>
</div>
