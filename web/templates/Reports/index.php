<?php
/**
 * @var \App\View\AppView $this
 */
$this->assign('title', 'Reports');
?>
<div class="reports content">
    <h3 class="mt-5"><?= __('Reports') ?></h3>

    <h5 class="mt-4 text-muted"><?= __('Zeit') ?></h5>
    <ul>
        <li><?= $this->Html->link(__('Arbeitszeit pro Woche'), ['action' => 'weeklyHours']) ?></li>
        <li><?= $this->Html->link(__('Arbeitszeit pro Wochentag'), ['action' => 'weekdayHours']) ?></li>
        <li><?= $this->Html->link(__('Stunden pro Kunde'), ['action' => 'hoursPerCustomer']) ?></li>
    </ul>

    <h5 class="mt-4 text-muted"><?= __('Geld') ?></h5>
    <ul>
        <li><?= $this->Html->link(__('Umsatz pro Monat'), ['action' => 'revenuePerMonth']) ?></li>
        <li><?= $this->Html->link(__('Effektiver Stundensatz pro Kunde'), ['action' => 'effectiveRate']) ?></li>
        <li><?= $this->Html->link(__('Offene Forderungen'), ['action' => 'receivables']) ?></li>
    </ul>
</div>
