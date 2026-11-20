<?php
/**
 * @var \App\View\AppView $this
 * @var array $backups
 */
$this->assign('title', 'Backups');
?>
<div class="backups content">
    <?= $this->Form->postButton(__('Backup erstellen'), ['action' => 'add'], ['class' => 'btn btn-primary float-end mt-5']) ?>
    <h3 class="mt-5"><?= __('Backups') ?></h3>
    <p class="text-muted">
        <?= __('Die Datenbank wird einmal am Tag beim ersten Aufruf gesichert und zusätzlich vor jeder Änderung über MCP, dort höchstens einmal pro Stunde. Behalten werden alle Sicherungen der letzten 7 Tage, danach eine je Tag bis 90 Tage, danach eine je Monat. Einspielen geht mit bin/cake backup --restore.') ?>
    </p>
    <table class="table table-sm">
        <thead>
        <tr>
            <th><?= __('Datei') ?></th>
            <th><?= __('Erstellt') ?></th>
            <th><?= __('Anlass') ?></th>
            <th class="text-end"><?= __('Größe') ?></th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($backups as $backup) : ?>
            <tr>
                <td><?= $this->Html->link($backup['name'], ['action' => 'download', $backup['name']]) ?></td>
                <td><?= h($backup['created']->format('d.m.Y H:i')) ?></td>
                <td><?= h($backup['reason']) ?></td>
                <td class="text-end"><?= $this->Number->format($backup['size'] / 1024, ['precision' => 1]) ?> KB</td>
            </tr>
        <?php endforeach; ?>
        <?php if (!$backups) : ?>
            <tr><td colspan="4" class="text-muted"><?= __('Noch keine Sicherung.') ?></td></tr>
        <?php endif; ?>
        </tbody>
    </table>
</div>
