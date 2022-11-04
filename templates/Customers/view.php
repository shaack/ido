<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Customer $customer
 */
?>
<div class="row">
    <aside class="column">
        <div class="side-nav">
            <h4 class="heading"><?= __('Actions') ?></h4>
            <?= $this->Html->link(__('Edit Customer'), ['action' => 'edit', $customer->id], ['class' => 'side-nav-item']) ?>
            <?= $this->Form->postLink(__('Delete Customer'), ['action' => 'delete', $customer->id], ['confirm' => __('Are you sure you want to delete # {0}?', $customer->id), 'class' => 'side-nav-item']) ?>
            <?= $this->Html->link(__('List Customers'), ['action' => 'index'], ['class' => 'side-nav-item']) ?>
            <?= $this->Html->link(__('New Customer'), ['action' => 'add'], ['class' => 'side-nav-item']) ?>
        </div>
    </aside>
    <div class="column-responsive column-80">
        <div class="customers view content">
            <h3><?= h($customer->name) ?></h3>
            <table>
                <tr>
                    <th><?= __('Shortcut') ?></th>
                    <td><?= h($customer->shortcut) ?></td>
                </tr>
                <tr>
                    <th><?= __('Website') ?></th>
                    <td><?= h($customer->website) ?></td>
                </tr>
                <tr>
                    <th><?= __('Color') ?></th>
                    <td><?= h($customer->color) ?></td>
                </tr>
                <tr>
                    <th><?= __('Name') ?></th>
                    <td><?= h($customer->name) ?></td>
                </tr>
                <tr>
                    <th><?= __('Street') ?></th>
                    <td><?= h($customer->street) ?></td>
                </tr>
                <tr>
                    <th><?= __('Zip City') ?></th>
                    <td><?= h($customer->zip_city) ?></td>
                </tr>
                <tr>
                    <th><?= __('Country') ?></th>
                    <td><?= h($customer->country) ?></td>
                </tr>
                <tr>
                    <th><?= __('Invoice Email') ?></th>
                    <td><?= h($customer->invoice_email) ?></td>
                </tr>
                <tr>
                    <th><?= __('Id') ?></th>
                    <td><?= $this->Number->format($customer->id) ?></td>
                </tr>
                <tr>
                    <th><?= __('Hourly Rate') ?></th>
                    <td><?= $customer->hourly_rate === null ? '' : $this->Number->format($customer->hourly_rate) ?></td>
                </tr>
                <tr>
                    <th><?= __('Created') ?></th>
                    <td><?= h($customer->created) ?></td>
                </tr>
                <tr>
                    <th><?= __('Current') ?></th>
                    <td><?= $customer->current ? __('Yes') : __('No'); ?></td>
                </tr>
            </table>
            <div class="text">
                <strong><?= __('Notes') ?></strong>
                <blockquote>
                    <?= $this->Text->autoParagraph(h($customer->notes)); ?>
                </blockquote>
            </div>
            <div class="related">
                <h4><?= __('Related Contacts') ?></h4>
                <?php if (!empty($customer->contacts)) : ?>
                <div class="table-responsive">
                    <table>
                        <tr>
                            <th><?= __('Id') ?></th>
                            <th><?= __('Name') ?></th>
                            <th><?= __('Role') ?></th>
                            <th><?= __('Telephone') ?></th>
                            <th><?= __('Email') ?></th>
                            <th><?= __('Notes') ?></th>
                            <th><?= __('Customer Id') ?></th>
                            <th class="actions"><?= __('Actions') ?></th>
                        </tr>
                        <?php foreach ($customer->contacts as $contacts) : ?>
                        <tr>
                            <td><?= h($contacts->id) ?></td>
                            <td><?= h($contacts->name) ?></td>
                            <td><?= h($contacts->role) ?></td>
                            <td><?= h($contacts->telephone) ?></td>
                            <td><?= h($contacts->email) ?></td>
                            <td><?= h($contacts->notes) ?></td>
                            <td><?= h($contacts->customer_id) ?></td>
                            <td class="actions">
                                <?= $this->Html->link(__('View'), ['controller' => 'Contacts', 'action' => 'view', $contacts->id]) ?>
                                <?= $this->Html->link(__('Edit'), ['controller' => 'Contacts', 'action' => 'edit', $contacts->id]) ?>
                                <?= $this->Form->postLink(__('Delete'), ['controller' => 'Contacts', 'action' => 'delete', $contacts->id], ['confirm' => __('Are you sure you want to delete # {0}?', $contacts->id)]) ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </table>
                </div>
                <?php endif; ?>
            </div>
            <div class="related">
                <h4><?= __('Related Projects') ?></h4>
                <?php if (!empty($customer->projects)) : ?>
                <div class="table-responsive">
                    <table>
                        <tr>
                            <!-- <th><?= __('Id') ?></th> -->
                            <th><?= __('Name') ?></th>
                            <!-- <th><?= __('Customer Id') ?></th> -->
                            <th><?= __('Start') ?></th>
                            <!-- <th><?= __('End Est') ?></th> -->
                            <!-- <th><?= __('End') ?></th>
                            <th><?= __('Fixed Price') ?></th>
                            <th><?= __('Hourly Rate') ?></th>
                            <th><?= __('Notes') ?></th>
                            <th><?= __('Description') ?></th> -->
                            <th><?= __('Invoice Number') ?></th>
                            <!-- <th><?= __('Invoice Date') ?></th>
                            <th><?= __('Paid At') ?></th>
                            <th><?= __('Parent Id') ?></th> -->
                            <th><?= __('Project Status Id') ?></th>
                            <!-- <th><?= __('Created') ?></th> -->
                            <th class="actions"><?= __('Actions') ?></th>
                        </tr>
                        <?php foreach ($customer->projects as $projects) : ?>
                        <tr>
                            <!-- <td><?= h($projects->id) ?></td> -->
                            <td><?= h($projects->name) ?></td>
                            <!-- <td><?= h($projects->customer_id) ?></td> -->
                            <td><?= h($projects->start) ?></td>
                            <!-- <td><?= h($projects->end_est) ?></td> -->
                            <!-- <td><?= h($projects->end) ?></td>
                            <td><?= h($projects->fixed_price) ?></td>
                            <td><?= h($projects->hourly_rate) ?></td>
                            <td><?= h($projects->notes) ?></td>
                            <td><?= h($projects->description) ?></td> -->
                            <td><?= h($projects->invoice_number) ?></td>
                            <!-- <td><?= h($projects->invoice_date) ?></td>
                            <td><?= h($projects->paid_at) ?></td>
                            <td><?= h($projects->parent_id) ?></td> -->
                            <td><?= h($projects->project_status_id) ?></td>
                            <!-- <td><?= h($projects->created) ?></td> -->
                            <td class="actions">
                                <?= $this->Html->link(__('View'), ['controller' => 'Projects', 'action' => 'view', $projects->id]) ?>
                                <?= $this->Html->link(__('Edit'), ['controller' => 'Projects', 'action' => 'edit', $projects->id]) ?>
                                <?= $this->Form->postLink(__('Delete'), ['controller' => 'Projects', 'action' => 'delete', $projects->id], ['confirm' => __('Are you sure you want to delete # {0}?', $projects->id)]) ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </table>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
