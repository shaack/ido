<?php
use Cake\I18n\Number;

/**
 * @var \App\View\AppView $this
 * @var array<int, array{label: string, revenue: float}> $months
 * @var float $totalRevenue
 */
$this->assign('title', 'Umsatz pro Monat');

$plotH = 300;
$barW = 18;
$gap = 8;
$step = $barW + $gap;
$padLeft = 64;     // Platz für die Euro-Beschriftung
$padTop = 16;
$padBottom = 64;

$count = count($months);
$maxRevenue = 0.0;
foreach ($months as $m) {
    $maxRevenue = max($maxRevenue, $m['revenue']);
}
// Y-Achse auf eine runde Zahl aufrunden.
$axisMax = 100.0;
if ($maxRevenue > 0) {
    $magnitude = 10 ** floor(log10($maxRevenue));
    $axisMax = ceil($maxRevenue / $magnitude) * $magnitude;
}
$yScale = $plotH / $axisMax;

$width = $padLeft + max(1, $count) * $step + 16;
$height = $padTop + $plotH + $padBottom;
$baseline = $padTop + $plotH;

$labelEvery = max(1, (int)ceil($count / 40));
$gridSteps = 5;
?>
<div class="reports content">
    <h3 class="mt-5"><?= __('Umsatz pro Monat') ?></h3>

    <?php if (!$count): ?>
        <p class="text-muted"><?= __('Noch keine Rechnungen erfasst.') ?></p>
    <?php else: ?>
        <p class="text-muted">
            <?= __('Netto, nach Rechnungsdatum') ?> &middot;
            <?= __('Summe') ?> <?= Number::currency($totalRevenue, 'EUR') ?>
        </p>
        <p class="text-muted small">
            <?= __('Beträge aus erfassten Zeiten und Festpreisen. Rechnungen aus '
                . 'der Zeit vor der Zeiterfassung fliessen mangels berechenbarem '
                . 'Betrag nicht ein.') ?>
        </p>

        <div class="table-responsive">
            <svg viewBox="0 0 <?= $width ?> <?= $height ?>" width="<?= $width ?>" height="<?= $height ?>"
                 role="img" aria-label="<?= __('Umsatz pro Monat') ?>"
                 style="max-width:none; font-size:11px;">
                <?php for ($g = 0; $g <= $gridSteps; $g++):
                    $value = $axisMax * $g / $gridSteps;
                    $y = $baseline - $value * $yScale; ?>
                    <line x1="<?= $padLeft ?>" y1="<?= round($y, 1) ?>"
                          x2="<?= $width - 16 ?>" y2="<?= round($y, 1) ?>"
                          stroke="var(--bs-border-color)" stroke-width="1"/>
                    <text x="<?= $padLeft - 6 ?>" y="<?= round($y + 3, 1) ?>"
                          text-anchor="end" fill="currentColor" opacity="0.7">
                        <?= Number::currency($value, 'EUR', ['precision' => 0]) ?>
                    </text>
                <?php endfor; ?>

                <?php foreach ($months as $i => $m):
                    $x = $padLeft + $i * $step + ($step - $barW) / 2;
                    $barH = $m['revenue'] * $yScale;
                    $y = $baseline - $barH; ?>
                    <rect x="<?= round($x, 1) ?>" y="<?= round($y, 1) ?>"
                          width="<?= $barW ?>" height="<?= round($barH, 1) ?>"
                          fill="var(--bs-primary)" rx="2">
                        <title><?= h($m['label']) ?>: <?= Number::currency($m['revenue'], 'EUR') ?></title>
                    </rect>
                <?php endforeach; ?>

                <?php foreach ($months as $i => $m):
                    if ($i % $labelEvery !== 0) {
                        continue;
                    }
                    $x = $padLeft + $i * $step + $step / 2; ?>
                    <text x="<?= round($x, 1) ?>" y="<?= $baseline + 12 ?>"
                          fill="currentColor" opacity="0.7" text-anchor="end"
                          transform="rotate(-60 <?= round($x, 1) ?> <?= $baseline + 12 ?>)">
                        <?= h($m['label']) ?>
                    </text>
                <?php endforeach; ?>

                <line x1="<?= $padLeft ?>" y1="<?= $baseline ?>"
                      x2="<?= $width - 16 ?>" y2="<?= $baseline ?>"
                      stroke="currentColor" stroke-width="1" opacity="0.5"/>
            </svg>
        </div>
    <?php endif; ?>
</div>
