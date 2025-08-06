<?php
/** @var $project \App\Model\Entity\Project */
/** @var $invoiceStored boolean */

use Cake\I18n\FrozenDate;

$parsedown = new Parsedown();

$type = $this->request->getQuery('type', 'default');
$latestInvoiceNumber = $this->get('latestInvoiceNumber');
$asNumber = intval($latestInvoiceNumber);
$newInvoiceNumber = $asNumber + 1;

$foreign = $project->customer->country && $project->customer->country !== "Deutschland";
$lang = $foreign ? "en" : "de";
$fileName = FrozenDate::now()->i18nFormat('yyyy-MM-dd') .
    " " . ($lang == "de" ? "Angebot" : "Offer") . " " . $project->customer->shortcut . " " . $project->name;
$this->assign('title', $fileName);
?>
<h1 class="mb-4"><?= ($lang == "de" ? "Angebot" : "Offer") ?>, <?= $project->name ?></h1>
<div class="">
    <div class="mb-1"><?= ($lang == "de" ? "Von" : "From") ?>:</div>
    <address class="mb-3">
        Stefan Haack<br/>
        Wittinger Str. 140L<br/>
        29223 Celle
    </address>
    <div class="mb-1"><?= ($lang == "de" ? "An" : "To") ?>:</div>
    <address class="mb-2">
        <?= $project->customer->name ?><br/>
        <?= $project->customer->street ?><br/>
        <?= $project->customer->zip_city ?><br/>
        <?= $project->customer->country ? $project->customer->country . '' : '' ?><br/>
        <?php if ($project->customer->address_addition): ?>
            <div class="mt-1"><?= $project->customer->address_addition ?></div>
        <?php endif ?>
    </address>
</div>
<h2 class="mb-3">Angebotene Leistungen</h2>
<?= $parsedown->parse($project->description); ?>

<h2 class="mb-2 mt-4">Kostenkalkulation</h2>
<table class="mb-2">
    <thead>
    <tr>
        <th><?= ($lang == "de" ? "Leistung" : "Service") ?></th>
        <th class="text-end"><?= ($lang == "de" ? "Geplanter&nbsp;Aufwand" : "Planned&nbsp;Effort") ?></th>
        <th class="text-end"><?= ($lang == "de" ? "Kosten" : "Costs") ?></th>
    </tr>
    </thead>
    <tbody>
    <?php foreach ($project->services as $services) : ?>
        <tr>
            <td class="w-100"><?= h($services->name) ?></td>
            <td class="text-end ps-4"><?= $this->Number->precision($services->effortPlanned(), 2) ?> Std.</td>
            <td class="text-end ps-4"><?= $this->Number->currency($services->costsPlanned()) ?></td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
<table class="sum">
        <tr>
            <td class="text-end w-100">Summe Netto</td>
            <td class="text-end ps-4 fw-bold"><?= $this->Number->currency($project->costsPlanned()) ?></td>
        </tr>
</table>
<p>
    Alle Preise verstehen sich zuzüglich der gesetzlichen Mehrwertsteuer.
    In den Preisen sind Abstimmungen und eine Korrekturphase enthalten. Das Angebot ist 30 Tage gültig. Zusätzliche, in
    der obigen Aufwandsschätzung nicht berücksichtigte Leistungen werden mit einem Stundensatz von 80,- berechnet.
</p>
<p>
    Vielen Dank für Ihr Interesse an meinen Dienstleistungen<br/>
    Stefan Haack, Celle <?= FrozenDate::now()->i18nFormat('dd.MM.yyyy') ?>
</p>
