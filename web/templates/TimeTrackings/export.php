<?php
/**
 * @var \App\View\AppView $this
 * @var iterable<\App\Model\Entity\TimeTracking> $timeTrackings
 * @var \App\Model\Entity\Project $project
 */
$shortcut = $project->customer ? $project->customer->shortcut : '';
$fileName = trim("Time Trackings " . $shortcut . " " . $project->name);
$this->assign('title', $fileName);
?>
<div class="timeTrackings index content">
    <h3 class="mt-5">
        <?= __('Time Trackings') ?>
        <?php if ($shortcut): ?>
            <?= h($shortcut) ?> -
        <?php endif; ?>
        <?= h($project->name) ?>
    </h3>
    <p>Stefan Haack</p>
    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th><?= __('Created') ?></th>
                    <th><?= __('Task') ?></th>
                    <th><?= __('Service') ?></th>
                    <th><?= __('Project') ?></th>
                    <th class="text-end"><?= __('Duration') ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($timeTrackings as $timeTracking):
                    if(!$timeTracking->duration) {
                        continue;
                    }
                    $customer = $timeTracking->task->service->project->customer;
                    ?>
                <tr>
                    <td><?= $this->Html->link($timeTracking->created, ['action' => 'edit', $timeTracking->id]) ?></td>
                    <td class="<?= $timeTracking->task->name ? "" : "fst-italic" ?>"><?= $timeTracking->hasValue('task') ? $this->Html->link($timeTracking->task->name ?: 'Diverses', ['controller' => 'Tasks', 'action' => 'view', $timeTracking->task->id]) : '' ?></td>
                    <td><?= $this->Html->link($timeTracking->task->service->name, ['controller' => 'Services', 'action' => 'view', $timeTracking->task->service->id]) ?></td>
                    <td><?= $this->Html->link($timeTracking->task->service->project->name, ['controller' => 'Projects', 'action' => 'view', $timeTracking->task->service->project->id]) ?></td>
                    <td class="text-end"><?= $this->Effort->hours($timeTracking->duration) ?></td>
                </tr>
                <?php endforeach; ?>

                <?php if (isset($totalDuration) && $totalDuration !== null): ?>
                <tr>
                    <td colspan="4" class="text-end"><strong><?= __('Erfasst') ?></strong></td>
                    <td class="text-end"><strong><?= $this->Effort->hours($totalDuration) ?></strong></td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <?php if (!empty($project->services)) : ?>
        <h4 class="mt-5"><?= __('Abrechnung') ?></h4>
        <p>Nach Zeit abgerechnete Leistungen werden auf die nächste Viertelstunde gerundet.
            Die abgerechneten Stunden sind die Grundlage der zeitbasierten Positionen der Rechnung.
            Leistungen mit Festpreis sind als solche gekennzeichnet.</p>
        <div class="table-responsive">
            <table>
                <thead>
                <tr>
                    <th><?= __('Leistung') ?></th>
                    <th class="text-end"><?= __('Erfasst') ?></th>
                    <th class="text-end"><?= __('Abgerechnet') ?></th>
                </tr>
                </thead>
                <tbody>
                <?php
                $trackedSum = 0.0;
                $billedSum = 0.0;
                foreach ($project->services as $service):
                    $tracked = $service->effortTracked();
                    // Bildet Project::costs() und Service::costs() nach. Nur wo
                    // beides fehlt, ein Festpreis am Projekt und einer an der
                    // Leistung, wird die Zeit in Rechnung gestellt. Sonst zählt
                    // der Festbetrag und die Stunden sind nur Nachweis.
                    $billedByTime = !$project->fixed_price && !$service->estimation_or_fixed_price;
                    if (!$tracked && !$billedByTime) {
                        continue;
                    }
                    $trackedSum += $tracked;
                    if ($billedByTime) {
                        $billedSum += $service->effort();
                    }
                    ?>
                    <tr>
                        <td><?= h($service->name) ?></td>
                        <td class="text-end"><?= $this->Effort->hours($tracked) ?></td>
                        <td class="text-end">
                            <?php if ($billedByTime): ?>
                                <?= $this->Effort->hours($service->effort()) ?>
                            <?php else: ?>
                                <span class="text-muted">Festpreis</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <tr>
                    <td class="text-end"><strong><?= __('Summe') ?></strong></td>
                    <td class="text-end border-top border-2"><?= $this->Effort->hours($trackedSum) ?></td>
                    <td class="text-end border-top border-2"><strong><?= $this->Effort->hours($billedSum) ?></strong></td>
                </tr>
                </tbody>
            </table>
        </div>
    <?php endif; ?>

    <?php if (isset($showPagination) && $showPagination): ?>
    <div class="paginator">
        <?php if ($this->Paginator->hasPage(2)) { ?>
            <ul class="pagination">
                <?= $this->Paginator->first('<< ' . __('first')) ?>
                <?= $this->Paginator->prev('< ' . __('previous')) ?>
                <?= $this->Paginator->numbers() ?>
                <?= $this->Paginator->next(__('next') . ' >') ?>
                <?= $this->Paginator->last(__('last') . ' >>') ?>
            </ul>
        <?php } ?>
        <p><?= $this->Paginator->counter(__('Page {{page}} of {{pages}}, showing {{current}} record(s) out of {{count}} total')) ?></p>
    </div>
    <?php endif; ?>
</div>
