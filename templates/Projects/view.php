<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Project $project
 */
?>
<div class="row">
    <aside class="column">
        <div class="side-nav">
            <h4 class="heading"><?= __('Actions') ?></h4>
            <?= $this->Html->link(__('Edit Project'), ['action' => 'edit', $project->Id], ['class' => 'side-nav-item']) ?>
            <?= $this->Form->postLink(__('Delete Project'), ['action' => 'delete', $project->Id], ['confirm' => __('Are you sure you want to delete # {0}?', $project->Id), 'class' => 'side-nav-item']) ?>
            <?= $this->Html->link(__('List Projects'), ['action' => 'index'], ['class' => 'side-nav-item']) ?>
            <?= $this->Html->link(__('New Project'), ['action' => 'add'], ['class' => 'side-nav-item']) ?>
        </div>
    </aside>
    <div class="column-responsive column-80">
        <div class="projects view content">
            <h3><?= h($project->title) ?></h3>
            <table>
                <tr>
                    <th><?= __('Id') ?></th>
                    <td><?= $this->Number->format($project->Id) ?></td>
                </tr>
                <tr>
                    <th><?= __('Customer') ?></th>
                    <td><?= $project->customer === null ? '' : $this->Number->format($project->customer) ?></td>
                </tr>
                <tr>
                    <th><?= __('Hourly Rate') ?></th>
                    <td><?= $project->hourly_rate === null ? '' : $this->Number->format($project->hourly_rate) ?></td>
                </tr>
                <tr>
                    <th><?= __('Advance Payment Of') ?></th>
                    <td><?= $project->advance_payment_of === null ? '' : $this->Number->format($project->advance_payment_of) ?></td>
                </tr>
                <tr>
                    <th><?= __('Start') ?></th>
                    <td><?= h($project->start) ?></td>
                </tr>
                <tr>
                    <th><?= __('End Est') ?></th>
                    <td><?= h($project->end_est) ?></td>
                </tr>
                <tr>
                    <th><?= __('End') ?></th>
                    <td><?= h($project->end) ?></td>
                </tr>
                <tr>
                    <th><?= __('Invoice Date') ?></th>
                    <td><?= h($project->invoice_date) ?></td>
                </tr>
                <tr>
                    <th><?= __('Paid At') ?></th>
                    <td><?= h($project->paid_at) ?></td>
                </tr>
            </table>
            <div class="text">
                <strong><?= __('Title') ?></strong>
                <blockquote>
                    <?= $this->Text->autoParagraph(h($project->title)); ?>
                </blockquote>
            </div>
            <div class="text">
                <strong><?= __('Fixed Price') ?></strong>
                <blockquote>
                    <?= $this->Text->autoParagraph(h($project->fixed_price)); ?>
                </blockquote>
            </div>
            <div class="text">
                <strong><?= __('Status') ?></strong>
                <blockquote>
                    <?= $this->Text->autoParagraph(h($project->status)); ?>
                </blockquote>
            </div>
            <div class="text">
                <strong><?= __('Notes') ?></strong>
                <blockquote>
                    <?= $this->Text->autoParagraph(h($project->notes)); ?>
                </blockquote>
            </div>
            <div class="text">
                <strong><?= __('Description') ?></strong>
                <blockquote>
                    <?= $this->Text->autoParagraph(h($project->description)); ?>
                </blockquote>
            </div>
            <div class="text">
                <strong><?= __('Invoice Number') ?></strong>
                <blockquote>
                    <?= $this->Text->autoParagraph(h($project->invoice_number)); ?>
                </blockquote>
            </div>
        </div>
    </div>
</div>
