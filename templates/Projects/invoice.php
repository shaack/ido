<?php

$latestInvoiceNumber = $this->get('latestInvoiceNumber');
$asNumber = intval($latestInvoiceNumber);
$newInvoiceNumber = $asNumber + 1;

/** @var $project \App\Model\Entity\Project */
?>

<div class="small">
    <address class="font-monospace mb-5 small">
        Stefan Haack<br/>
        Wittinger Str. 140L<br/>
        29223 Celle<br/>
        Tel. +49 (0)5141 403 95 11<br/>
        shaack.com<br/>
    </address>
    <div class="mb-1">An:</div>
    <address class="mb-5">
        <?= $project->customer->name ?><br/>
        <?= $project->customer->street ?><br/>
        <?= $project->customer->zip_city ?><br/>
        <?= $project->customer->country ? $project->customer->country . '' : '' ?><br/>
    </address>
</div>
<h1>Rechnung</h1>
<h2><?= $project->name ?></h2>
<?= $this->Form->create($project) ?>
<table class="table">
    <tr>
        <th><label for="invoice_number">Rechnungsnummer</label></th><td>
            <?= $this->Form->control('invoice_number'); ?>
        </td>
    </tr>
    <tr>
        <th><label for="invoice_number">Datum</label></th><td>
            <?= $this->Form->control('invoice_date'); ?>
        </td>
    </tr>
    <tr><th>Steuernummer</th><td>17/116/11120</td></tr>
    <tr><th>Ust-Id Nr.</th><td>DE246560796</td></tr>
    <tr><th>Zeitraum der Leistung</th><td><?= $project->start ?> -
            <?= $project->end ? $project->end : '<span class="text-danger">project end date missing</span>' ?></td></tr>
</table>
<?= $this->Form->button(__('Submit')) ?>
<?= $this->Form->end() ?>
<p>Vielen Dank für Ihren Auftrag, den wir wie folgt abrechnen.</p>
<table class="table">
    <thead>
    <tr>
        <th>Leistung</th><th class="text-end">Aufwand</th><th class="text-end">Betrag</th>
    </tr>
    </thead>
    <tbody>
    <?php $effortSum = 0;
    foreach ($project->services as $services) :
        $effortSum += $services->effort();
        ?>
        <tr>
            <td><?= h($services->name) ?></td>
            <td class="text-end"><?= h($services->effort()) ?></td>
            <td class="text-end"><?= $this->Number->currency($services->costs()) ?></td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
