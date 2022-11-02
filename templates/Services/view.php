<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Service $service
 */
?>
<div class="row">
    <aside class="column">
        <div class="side-nav">
            <h4 class="heading"><?= __('Actions') ?></h4>
            <?= $this->Html->link(__('Edit Service'), ['action' => 'edit', $service->Id], ['class' => 'side-nav-item']) ?>
            <?= $this->Form->postLink(__('Delete Service'), ['action' => 'delete', $service->Id], ['confirm' => __('Are you sure you want to delete # {0}?', $service->Id), 'class' => 'side-nav-item']) ?>
            <?= $this->Html->link(__('List Services'), ['action' => 'index'], ['class' => 'side-nav-item']) ?>
            <?= $this->Html->link(__('New Service'), ['action' => 'add'], ['class' => 'side-nav-item']) ?>
        </div>
    </aside>
    <div class="column-responsive column-80">
        <div class="services view content">
            <h3><?= h($service->title) ?></h3>
            <table>
                <tr>
                    <th><?= __('Id') ?></th>
                    <td><?= $this->Number->format($service->Id) ?></td>
                </tr>
                <tr>
                    <th><?= __('Project') ?></th>
                    <td><?= $service->project === null ? '' : $this->Number->format($service->project) ?></td>
                </tr>
                <tr>
                    <th><?= __('Effort Est') ?></th>
                    <td><?= $service->effort_est === null ? '' : $this->Number->format($service->effort_est) ?></td>
                </tr>
                <tr>
                    <th><?= __('Estimation Or Fixed Price') ?></th>
                    <td><?= $service->estimation_or_fixed_price === null ? '' : $this->Number->format($service->estimation_or_fixed_price) ?></td>
                </tr>
                <tr>
                    <th><?= __('Effort') ?></th>
                    <td><?= $service->effort === null ? '' : $this->Number->format($service->effort) ?></td>
                </tr>
                <tr>
                    <th><?= __('Costs') ?></th>
                    <td><?= $service->costs === null ? '' : $this->Number->format($service->costs) ?></td>
                </tr>
                <tr>
                    <th><?= __('Created') ?></th>
                    <td><?= h($service->created) ?></td>
                </tr>
            </table>
            <div class="text">
                <strong><?= __('Title') ?></strong>
                <blockquote>
                    <?= $this->Text->autoParagraph(h($service->title)); ?>
                </blockquote>
            </div>
            <div class="text">
                <strong><?= __('Done') ?></strong>
                <blockquote>
                    <?= $this->Text->autoParagraph(h($service->done)); ?>
                </blockquote>
            </div>
            <div class="text">
                <strong><?= __('Effort2') ?></strong>
                <blockquote>
                    <?= $this->Text->autoParagraph(h($service->effort2)); ?>
                </blockquote>
            </div>
            <div class="text">
                <strong><?= __('Notes') ?></strong>
                <blockquote>
                    <?= $this->Text->autoParagraph(h($service->notes)); ?>
                </blockquote>
            </div>
        </div>
    </div>
</div>
