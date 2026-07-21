<?php
use Cake\I18n\Number;

/**
 * @var \App\View\AppView $this
 * @var array<int, array{customer: string, project: string, number: ?string, date: \Cake\I18n\Date, days: int, net: float, gross: float}> $items
 * @var float $totalNet
 * @var float $totalGross
 */
$this->assign('title', 'Offene Forderungen');
?>
<div class="reports content">
    <h3 class="mt-5"><?= __('Offene Forderungen') ?></h3>
    <p class="text-muted">
        <?= __('Berechnet, aber noch nicht als bezahlt markiert. Beträge aus '
            . 'erfassten Zeiten und Festpreisen; Rechnungen aus der Zeit vor der '
            . 'Zeiterfassung sind ohne berechenbaren Betrag hier ausgelassen.') ?>
    </p>

    <?php if (!$items): ?>
        <p class="text-muted"><?= __('Keine offenen Forderungen.') ?></p>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table align-middle">
                <thead>
                <tr>
                    <th><?= __('Kunde') ?></th>
                    <th><?= __('Projekt') ?></th>
                    <th><?= __('Rechnung') ?></th>
                    <th class="text-end"><?= __('Datum') ?></th>
                    <th class="text-end"><?= __('Offen seit') ?></th>
                    <th class="text-end"><?= __('Netto') ?></th>
                    <th class="text-end"><?= __('Brutto') ?></th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($items as $item): ?>
                    <tr>
                        <td><?= h($item['customer']) ?></td>
                        <td><?= h($item['project']) ?></td>
                        <td><?= h($item['number']) ?></td>
                        <td class="text-end"><?= $item['date']->i18nFormat('dd.MM.yyyy') ?></td>
                        <td class="text-end<?= $item['days'] > 30 ? ' text-danger' : '' ?>">
                            <?= __('{0} Tage', $item['days']) ?>
                        </td>
                        <td class="text-end"><?= Number::currency($item['net'], 'EUR') ?></td>
                        <td class="text-end"><?= Number::currency($item['gross'], 'EUR') ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
                <tfoot>
                <tr>
                    <td colspan="5" class="text-end"><strong><?= __('Summe') ?></strong></td>
                    <td class="text-end border-top border-2"><?= Number::currency($totalNet, 'EUR') ?></td>
                    <td class="text-end border-top border-2">
                        <strong><?= Number::currency($totalGross, 'EUR') ?></strong>
                    </td>
                </tr>
                </tfoot>
            </table>
        </div>
    <?php endif; ?>
</div>
