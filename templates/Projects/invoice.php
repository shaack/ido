<?php
/** @var $project \App\Model\Entity\Project */
/** @var $invoiceStored boolean */

$type = $this->request->getQuery('type', 'default');
$latestInvoiceNumber = $this->get('latestInvoiceNumber');
$asNumber = intval($latestInvoiceNumber);
$newInvoiceNumber = $asNumber + 1;

$foreign = $project->customer->country && $project->customer->country !== "Deutschland";
$lang = $foreign ? "en" : "de";
$fileName =  $project->invoice_date->i18nFormat('yyyy-MM-dd') .
    " " . ($lang == "de" ? "Rechnung" : "Invoice") . " " . $project->invoice_number . " " . $project->customer->shortcut . " " . $project->name;
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
    <div class=""><?= ($lang == "de" ? "An" : "To") ?>:</div>
    <address class="mb-5">
        <?= $project->customer->name ?><br/>
        <?= $project->customer->street ?><br/>
        <?= $project->customer->zip_city ?><br/>
        <?= $project->customer->country ? $project->customer->country . '' : '' ?><br/>
    </address>
</div>
<h1 class="mb-0"><?= ($lang == "de" ? "Rechnung" : "Invoice") ?></h1>
<h2 class="mb-4"><?= $project->name ?></h2>
<?= $this->Form->create($project) ?>
<table class="data">
    <tr>
        <th><label for="invoice_number"><?= ($lang == "de" ? "Rechnungsnummer" : "Invoice number") ?></label></th>
        <td>
            <?= $invoiceStored ? $project->invoice_number : $this->Form->control('invoice_number'); ?>
        </td>
    </tr>
    <tr>
        <th><label for="invoice_number"><?= ($lang == "de" ? "Datum" : "Date") ?></label></th>
        <td>
            <?= $invoiceStored ? $project->invoice_date : $this->Form->control('invoice_date'); ?>
        </td>
    </tr>
    <tr>
        <th><?= ($lang == "de" ? "Steuernummer" : "Tax number") ?></th>
        <td>17/116/11120</td>
    </tr>
    <tr>
        <th><?= ($lang == "de" ? "Ust-Id Nr." : "VAT ID No.") ?></th>
        <td>DE246560796</td>
    </tr>
    <tr>
        <th><?= ($lang == "de" ? "Zeitraum der Leistung" : "Period of the service") ?></th>
        <td><?= $project->start ?> -
            <?= $invoiceStored ? $project->end : $this->Form->control('end'); ?></td>
    </tr>
</table>
<?= !$invoiceStored ? $this->Form->button(__('Submit')) : "" ?>
<?= $this->Form->end() ?>
<p><?= ($lang == "de" ? "Vielen Dank für Ihren Auftrag, den wir wie folgt abrechnen." : "Thank you for your order, which we will invoice as follows.") ?></p>
<table class="mb-2">
    <thead>
    <tr>
        <th><?= ($lang == "de" ? "Leistung" : "Service") ?></th>
        <th class="text-end"><?= ($lang == "de" ? "Aufwand" : "Effort") ?></th>
        <th class="text-end"><?= ($lang == "de" ? "Betrag" : "Amount") ?></th>
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
