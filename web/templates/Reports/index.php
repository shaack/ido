<?php
/**
 * @var \App\View\AppView $this
 */
$this->assign('title', 'Reports');
?>
<div class="reports content">
    <h3 class="mt-5"><?= __('Reports') ?></h3>
    <ul>
        <li>
            <?= $this->Html->link(__('Arbeitszeit pro Woche'), ['action' => 'weeklyHours']) ?>
        </li>
    </ul>
</div>
