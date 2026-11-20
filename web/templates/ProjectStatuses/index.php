<?php
/**
 * Die Projektstatus als Ablauf, nach Phasen gruppiert. Die Id ist die
 * Reihenfolge, die Farbe dieselbe wie in den Projektlisten.
 *
 * @var \App\View\AppView $this
 * @var iterable<\App\Model\Entity\ProjectStatus> $projectStatuses
 * @var array<int, int> $projectCounts
 */
$this->assign('title', __('Project statuses'));

$phases = [
    ['label' => __('Offer'), 'from' => 0, 'to' => 14, 'tone' => 'blue'],
    ['label' => __('Running'), 'from' => 15, 'to' => 19, 'tone' => 'green'],
    ['label' => __('Billing'), 'from' => 20, 'to' => 34, 'tone' => 'amber'],
    ['label' => __('Deferred'), 'from' => 35, 'to' => 39, 'tone' => 'muted'],
    ['label' => __('Ended'), 'from' => 40, 'to' => PHP_INT_MAX, 'tone' => 'muted'],
];
// Gemahnt ist Abrechnung, aber dringlich
$tone = fn(int $id, string $phaseTone) => $id === 30 ? 'red' : $phaseTone;
$statuses = $projectStatuses->toList();
?>
<div class="projectStatuses index content">
    <header class="page-head">
        <h1 class="page-title"><?= __('Project statuses') ?></h1>
        <div class="page-actions">
            <?= $this->Html->link(
                $this->element('icon', ['name' => 'plus']) . '<span>' . __('New status') . '</span>',
                ['action' => 'add'],
                ['class' => 'btn btn-primary btn-icon', 'escape' => false]
            ) ?>
        </div>
    </header>
    <p class="page-intro"><?= __('A project moves through these statuses from top to bottom. The number decides the order.') ?></p>

    <div class="status-flow">
        <?php foreach ($phases as $phase) :
            $items = array_filter($statuses, fn($s) => $s->id >= $phase['from'] && $s->id <= $phase['to']);
            if (!$items) {
                continue;
            }
            ?>
            <section class="status-phase">
                <h2 class="status-phase-label"><?= h($phase['label']) ?></h2>
                <ul class="status-list">
                    <?php foreach ($items as $status) :
                        $count = (int)($projectCounts[$status->id] ?? 0);
                        ?>
                        <li class="status-row">
                            <span class="status-dot tone-<?= $tone($status->id, $phase['tone']) ?>" aria-hidden="true"></span>
                            <span class="status-number"><?= (int)$status->id ?></span>
                            <?= $this->Html->link(h($status->name), ['action' => 'view', $status->id], ['class' => 'status-name', 'escape' => false]) ?>
                            <span class="status-count">
                                <?= $count > 0
                                    ? $this->Html->link(__n('{0} project', '{0} projects', $count, $count), ['action' => 'view', $status->id])
                                    : __('no projects') ?>
                            </span>
                            <span class="status-actions">
                                <?= $this->Html->link(__('Edit'), ['action' => 'edit', $status->id]) ?>
                                <?php if ($count === 0) : ?>
                                    <?= $this->Form->postLink(__('Delete'), ['action' => 'delete', $status->id],
                                        ['confirm' => __('Delete the status "{0}"?', $status->name), 'class' => 'text-danger']) ?>
                                <?php endif; ?>
                            </span>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </section>
        <?php endforeach; ?>
    </div>
</div>
