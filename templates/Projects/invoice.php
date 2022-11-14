<?php
/** @var $project \App\Model\Entity\Project */
/** @var $invoiceStored boolean */

$latestInvoiceNumber = $this->get('latestInvoiceNumber');
$asNumber = intval($latestInvoiceNumber);
$newInvoiceNumber = $asNumber + 1;

$fileName =  $project->invoice_date->i18nFormat('yyyy-MM-dd') .
    " Rechnung " . $project->invoice_number . " " . $project->customer->shortcut . " " . $project->name;

$this->assign('title', $fileName);
?>

<div class="">
    <address class="font-monospace mb-5 small">
        Stefan Haack<br/>
        Wittinger Str. 140L<br/>
        29223 Celle<br/>
        Tel. +49 (0)5141 403 95 11<br/>
        shaack.com<br/>
    </address>
    <div class="">An:</div>
    <address class="mb-5">
        <?= $project->customer->name ?><br/>
        <?= $project->customer->street ?><br/>
        <?= $project->customer->zip_city ?><br/>
        <?= $project->customer->country ? $project->customer->country . '' : '' ?><br/>
    </address>
</div>
<h1 class="mb-0">Rechnung</h1>
<h2 class="mb-4"><?= $project->name ?></h2>
<?= $this->Form->create($project) ?>
<table class="data">
    <tr>
        <th><label for="invoice_number">Rechnungsnummer</label></th>
        <td>
            <?= $invoiceStored ? $project->invoice_number : $this->Form->control('invoice_number'); ?>
        </td>
    </tr>
    <tr>
        <th><label for="invoice_number">Datum</label></th>
        <td>
            <?= $invoiceStored ? $project->invoice_date : $this->Form->control('invoice_date'); ?>
        </td>
    </tr>
    <tr>
        <th>Steuernummer</th>
        <td>17/116/11120</td>
    </tr>
    <tr>
        <th>Ust-Id Nr.</th>
        <td>DE246560796</td>
    </tr>
    <tr>
        <th>Zeitraum der Leistung</th>
        <td><?= $project->start ?> -
            <?= $invoiceStored ? $project->end : $this->Form->control('end'); ?></td>
    </tr>
</table>
<?= !$invoiceStored ? $this->Form->button(__('Submit')) : "" ?>
<?= $this->Form->end() ?>
<p>Vielen Dank für Ihren Auftrag, den wir wie folgt abrechnen.</p>
<table class="mb-2">
    <thead>
    <tr>
        <th>Leistung</th>
        <th class="text-end">Aufwand</th>
        <th class="text-end">Betrag</th>
    </tr>
    </thead>
    <tbody>
    <?php foreach ($project->services as $services) : ?>
        <tr>
            <td class="w-100"><?= h($services->name) ?></td>
            <td class="text-end code ps-4"><?= $this->Number->format($services->effort()) ?></td>
            <td class="text-end code ps-4"><?= $this->Number->currency($services->costs()) ?></td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
<table class="sum">
    <tr>
        <td class="text-end w-100">Summe Netto</td>
        <td class="text-end code ps-4"><?= $this->Number->currency($project->costs()) ?></td>
    </tr>
    <tr>
        <td class="text-end w-100">Umsatzsteuer 19%</td>
        <td class="text-end code ps-4"><?= $this->Number->currency($project->vat()) ?></td>
    </tr>
    <tr class="">
        <td class="text-end w-100"><b>Rechnungsbetrag</b></td>
        <td class="text-end code ps-4"><b><?= $this->Number->currency($project->total()) ?></b></td>
    </tr>
</table>
<p>Zahlbar sofort nach Rechnungserhalt ohne Abzug. Bitte überweisen Sie den Betrag unter Angabe der Rechnungsnummer auf
    folgendes Konto.</p>
<table class="data">
    <tr>
        <th>Inhaber</th>
        <td class="code">Stefan Haack</td>
    </tr>
    <tr>
        <th>IBAN</th>
        <td class="code">DE71100100100537918109</td>
    </tr>
    <tr>
        <th>BIC</th>
        <td class="code">PBNKDEFF100 (Postbank)</td>
    </tr>
</table>
<p>
    Viele Grüße<br/>
    Stefan Haack
</p>
