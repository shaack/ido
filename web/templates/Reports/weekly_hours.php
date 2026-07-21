<?php
/**
 * @var \App\View\AppView $this
 * @var array<int, array{label: string, hours: float}> $weeks
 * @var float $totalHours
 * @var float $averageHours
 * @var int $weekCount
 */
$this->assign('title', 'Arbeitszeit pro Woche');

// Geometrie des Balkendiagramms. Die Zeichnung ist ein reines Inline-SVG,
// damit kein zusätzliches Chart-Paket nötig ist und der Report auch im
// Druck-Layout funktioniert.
$plotH = 300;      // Höhe der Zeichenfläche
$step = 22;        // Abstand Balkenmitte zu Balkenmitte
$barW = 15;        // Balkenbreite
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
?>
<div class="reports content">
    <h3 class="mt-5"><?= __('Arbeitszeit pro Woche') ?></h3>

    <?php if (!$count): ?>
        <p class="text-muted"><?= __('Noch keine Zeiten erfasst.') ?></p>
    <?php else: ?>
        <p class="text-muted">
            <?= $weekCount ?> <?= __('Wochen') ?> &middot;
            <?= __('Summe') ?> <?= $this->Effort->hours($totalHours) ?> h &middot;
            <?= __('Durchschnitt') ?> <?= $this->Effort->hours($averageHours) ?> h/<?= __('Woche') ?>
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
                          fill="var(--bs-primary)" rx="2">
                        <title><?= h($w['label']) ?>: <?= $this->Effort->hours($w['hours']) ?> h</title>
                    </rect>
                <?php endforeach; ?>

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
