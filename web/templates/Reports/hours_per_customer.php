<?php
/**
 * @var \App\View\AppView $this
 * @var array<int, array{name: string, hours: float}> $customers
 * @var float $totalHours
 * @var string $period
 * @var bool $includeInternal
 */
$this->assign('title', 'Stunden pro Kunde');

$maxHours = 0.0;
foreach ($customers as $c) {
    $maxHours = max($maxHours, $c['hours']);
}

$periodLabels = ['90' => __('90 Tage'), '365' => __('1 Jahr'), 'all' => __('Gesamt')];

// Umschalter behalten den jeweils anderen Zustand bei, damit Zeitraum und
// interner Filter unabhängig voneinander umschaltbar sind.
$internalParam = $includeInternal ? '1' : '0';
?>
<div class="reports content">
    <h3 class="mt-5"><?= __('Stunden pro Kunde') ?></h3>

    <div class="d-flex flex-wrap gap-3 align-items-center mb-3">
        <div class="btn-group btn-group-sm" role="group">
            <?php foreach ($periodLabels as $key => $label): ?>
                <?= $this->Html->link(
                    $label,
                    ['?' => ['period' => (string)$key, 'internal' => $internalParam]],
                    ['class' => 'btn btn-outline-secondary' . ((string)$key === $period ? ' active' : '')],
                ) ?>
            <?php endforeach; ?>
        </div>
        <div class="form-check form-switch mb-0">
            <input class="form-check-input" type="checkbox" role="switch" id="internalToggle"
                   <?= $includeInternal ? 'checked' : '' ?>
                   onchange="location.search = '?period=<?= h($period) ?>&internal=' + (this.checked ? '1' : '0')">
            <label class="form-check-label" for="internalToggle"><?= __('Interne Projekte') ?></label>
        </div>
    </div>

    <?php if (!$customers): ?>
        <p class="text-muted"><?= __('Keine Zeiten im Zeitraum erfasst.') ?></p>
    <?php else: ?>
        <p class="text-muted"><?= __('Summe') ?> <?= $this->Effort->hours($totalHours) ?> h</p>
        <div class="table-responsive">
            <table class="table w-auto align-middle">
                <thead>
                <tr>
                    <th><?= __('Kunde') ?></th>
                    <th class="text-end"><?= __('Stunden') ?></th>
                    <th style="width:260px"></th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($customers as $c):
                    $pct = $maxHours > 0 ? $c['hours'] / $maxHours * 100 : 0; ?>
                    <tr>
                        <td><?= h($c['name']) ?></td>
                        <td class="text-end"><?= $this->Effort->hours($c['hours']) ?></td>
                        <td>
                            <div style="background:var(--bs-primary); height:12px; border-radius:2px;
                                        width:<?= round(max(0, $pct), 1) ?>%"></div>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>
