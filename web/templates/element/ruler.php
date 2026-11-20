<?php
/**
 * Das Viertelstunden-Lineal: erfasste Stunden auf einer Skala von 0 bis
 * $max Stunden, mit einem Strich je Viertelstunde, weil in Viertelstunden
 * abgerechnet wird. Groß auf dem Dashboard, klein in der Seitenleiste.
 *
 * @var \App\View\AppView $this
 * @var float $hours
 * @var int|null $max
 * @var bool|null $small
 */
$max = $max ?? 8;
$small = $small ?? false;
$hours = max(0.0, (float)$hours);
$fill = min($hours, $max) / $max * 100;
?>
<div class="ruler<?= $small ? ' ruler-sm' : '' ?><?= $hours > $max ? ' ruler-over' : '' ?>"
     style="--ruler-fill: <?= round($fill, 2) ?>%; --ruler-steps: <?= $max ?>"
     role="meter" aria-valuemin="0" aria-valuemax="<?= $max ?>" aria-valuenow="<?= round($hours, 2) ?>"
     aria-label="<?= __('{0} Stunden erfasst', $this->Effort->hours($hours)) ?>">
    <div class="ruler-track"><div class="ruler-fill"></div></div>
    <?php if (!$small) : ?>
        <div class="ruler-scale" aria-hidden="true">
            <?php for ($h = 0; $h <= $max; $h++) : ?>
                <span style="left: <?= $h / $max * 100 ?>%"><?= $h ?></span>
            <?php endfor; ?>
        </div>
    <?php endif; ?>
</div>
