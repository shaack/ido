<?php
use Cake\I18n\Number;

/**
 * @var \App\View\AppView $this
 * @var array<int, array{name: string, costs: float, hours: float, rate: float}> $customers
 */
$this->assign('title', 'Effektiver Stundensatz pro Kunde');

$maxRate = 0.0;
foreach ($customers as $c) {
    $maxRate = max($maxRate, $c['rate']);
}
?>
<div class="reports content">
    <h3 class="mt-5"><?= __('Effektiver Stundensatz pro Kunde') ?></h3>
    <p class="text-muted">
        <?= __('Abgerechneter Betrag geteilt durch die tatsächlich erfassten Stunden. '
            . 'Rundung auf Viertelstunden und Festpreise sind berücksichtigt. '
            . 'Interne Projekte sind ausgenommen.') ?>
    </p>

    <?php if (!$customers): ?>
        <p class="text-muted"><?= __('Keine abrechenbaren Zeiten erfasst.') ?></p>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table w-auto align-middle">
                <thead>
                <tr>
                    <th><?= __('Kunde') ?></th>
                    <th class="text-end"><?= __('Erfasst') ?></th>
                    <th class="text-end"><?= __('Abgerechnet') ?></th>
                    <th class="text-end"><?= __('Ø €/h') ?></th>
                    <th style="width:220px"></th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($customers as $c):
                    $pct = $maxRate > 0 ? $c['rate'] / $maxRate * 100 : 0; ?>
                    <tr>
                        <td><?= h($c['name']) ?></td>
                        <td class="text-end"><?= $this->Effort->hours($c['hours']) ?></td>
                        <td class="text-end"><?= Number::currency($c['costs'], 'EUR') ?></td>
                        <td class="text-end"><?= Number::currency($c['rate'], 'EUR') ?></td>
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
