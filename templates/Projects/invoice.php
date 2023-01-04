<?php
/** @var $project \App\Model\Entity\Project */
/** @var $invoiceStored boolean */

$type = $this->request->getQuery('type', 'default');
$latestInvoiceNumber = $this->get('latestInvoiceNumber');
$asNumber = intval($latestInvoiceNumber);
$newInvoiceNumber = $asNumber + 1;

$foreign = $project->customer->country && $project->customer->country !== "Deutschland";
$lang = $foreign ? "en" : "de";
$fileName = $project->invoice_date->i18nFormat('yyyy-MM-dd') .
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
    <div class="mb-1"><?= ($lang == "de" ? "An" : "To") ?>:</div>
    <address class="mb-5">
        <?= $project->customer->name ?><br/>
        <?= $project->customer->street ?><br/>
        <?= $project->customer->zip_city ?><br/>
        <?= $project->customer->country ? $project->customer->country . '' : '' ?><br/>
        <?php if ($project->customer->address_addition): ?>
            <div class="mt-1"><?= $project->customer->address_addition ?></div>
        <?php endif ?>
    </address>
</div>
<h1 class="mb-1"><?= ($lang == "de" ? "Rechnung" : "Invoice") ?></h1>
<h2 class="mb-4"><?= $project->name ?></h2>
<?= $this->Form->create($project) ?>
<div class="row">
    <div class="col-7">
        <table class="data m-0">
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
                <td><?= $project->start->format("d.m.y") ?> -
                    <?= $invoiceStored ? $project->end->format("d.m.y") : $this->Form->control('end'); ?></td>
            </tr>
        </table>
    </div>
    <div class="col-5">
        <table class="data m-0">
            <tr>
                <th><label for="invoice_number"><?= ($lang == "de" ? "Rechnungsnummer" : "Invoice number") ?></label></th>
                <td>
                    <?= $invoiceStored ? $project->invoice_number : $this->Form->control('invoice_number'); ?>
                </td>
            </tr>
            <tr>
                <th><label for="invoice_number"><?= ($lang == "de" ? "Datum" : "Date") ?></label></th>
                <td>
                    <?= $invoiceStored ? $project->invoice_date->format("d.m.Y") : $this->Form->control('invoice_date'); ?>
                </td>
            </tr>
        </table>
    </div>
</div>

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
            <td class="text-end code ps-4"><?= $this->Number->precision($services->effort(), 2) ?></td>
            <td class="text-end code ps-4"><?= $this->Number->currency($services->costs()) ?></td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
<table class="sum">
    <?php if($foreign): ?>
        <tr>
            <td class="text-end w-100"><b>Total invoice amount</b></td>
            <td class="text-end code ps-4"><b><?= $this->Number->currency($project->costs()) ?></b></td>
        </tr>
        <tr>
            <td class="text-end pt-2" colspan="2">
                Umsatzsteuerschuldnerschaft des Leistungsempfängers<br/>
                VAT liability of the recipient of the service
            </td>
        </tr>
    <?php else: ?>
        <tr>
            <td class="text-end w-100">Summe Netto</td>
            <td class="text-end code ps-4"><?= $this->Number->currency($project->costs()) ?></td>
        </tr>
        <tr>
            <td class="text-end w-100">Umsatzsteuer 19%</td>
            <td class="text-end code ps-4"><?= $this->Number->currency($project->vat()) ?></td>
        </tr>
        <tr>
            <td class="text-end w-100"><b>Rechnungsbetrag</b></td>
            <td class="text-end code ps-4"><b><?= $this->Number->currency($project->total()) ?></b></td>
        </tr>
    <?php endif ?>
</table>
<p>
    <?php if ($lang == "de"): ?>
        Zahlbar sofort nach Rechnungserhalt ohne Abzug. Bitte überweisen Sie den Betrag unter Angabe der Rechnungsnummer auf
        folgendes Konto.
    <?php else: ?>
        Payable immediately upon receipt of invoice without deduction. Please transfer the amount to the following account stating the invoice number.
    <?php endif ?>
</p>
<table class="data">
    <tr>
        <th><?= ($lang == "de" ? "Inhaber" : "Owner") ?></th>
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
    <?= ($lang == "de" ? "Viele Grüße" : "Kind regards,") ?><br/>
    Stefan Haack
</p>
