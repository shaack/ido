<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Service $service
 */

$this->assign('title', "Zeiterfassung " . h($service->name));
?>
<h1 class='mt-5 mb-1'>Zeiterfassung <?= h($service->name) ?></h1>
<p>Stefan Haack</p>
<div class="row">
    <div class="column-responsive column-80">
        <div class="services view content">
            <div class="related">
                <?php if (!empty($service->tasks)) : ?>
                <div class="table-responsive">
                    <table>
                        <tr>
                            <th><?= __('Name') ?></th>
                            <th class="text-end"><?= __('Duration') ?></th>
                        </tr>
                        <?php $durationSum = 0 ?>
                        <?php foreach ($service->tasks as $task) :
                            $durationSum += $task->duration();
                            ?>
                        <tr>
                            <td><?= $this->Html->link($task->name ?: "[Task]", ['controller' => 'Tasks', 'action' => 'view', $task->id]) ?></td>
                            <td class="text-end font-monospace"><?= $task->duration() ?></td>
                        </tr>
                        <?php endforeach; ?>
                        <tr>
                            <td class="text-end fw-bold">Summe</td>
                            <td class="text-end fw-bold font-monospace"><b><?= $durationSum ?></b></td>
                        </tr>
                    </table>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
