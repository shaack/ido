<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Customer $customer
 */
$this->assign('title', h($customer->shortcut));
$parsedown = new Parsedown();
?>
<div class="row">
    <aside class="column">
        <div class="actions">
            <?= $this->Html->link(__('Edit Customer'), ['action' => 'edit', $customer->id], ['class' => 'side-nav-item']) ?>
            <?= $this->Form->postLink(__('Delete Customer'), ['action' => 'delete', $customer->id], ['confirm' => __('Are you sure you want to delete # {0}?', $customer->id), 'class' => 'side-nav-item']) ?>
            <?= $this->Html->link(__('List Customers'), ['action' => 'index'], ['class' => 'side-nav-item']) ?>
            <?= $this->Html->link(__('New Customer'), ['action' => 'add'], ['class' => 'side-nav-item']) ?>
        </div>
    </aside>
    <div class="column-responsive column-80">
        <div class="customers view content">
            <h3><?= h($customer->name) ?></h3>
            <div class="related">
                <h4><?= __('Running Projects') ?></h4>
                <div class="actions">
                    <a class="button-clear" href="/projects/add?customer_id=<?= $customer->id ?>">Add Project</a>
                </div>
                <?php if (!empty($customer->projects)) : ?>
                    <div class="table-responsive">
                        <table>
                            <thead>
                            <tr>
                                <th><?= __('Name') ?></th>
                                <th><?= __('Start') ?></th>
                                <th><?= __('Invoice Number') ?></th>
                                <th><?= __('Project Status Id') ?></th>
                                <th class="actions"><?= __('Actions') ?></th>
                            </tr>
                            </thead>
                            <?php foreach ($customer->projects as $projects) :
                                if($projects->project_status->id !== 15) {
                                    continue;
                                }
                                ?>
                                <tr>
                                    <td><?= $this->Html->link($projects->name, ['controller' => 'Projects', 'action' => 'view', $projects->id]) ?></td>
                                    <td><?= h($projects->start) ?></td>
                                    <td><?= h($projects->invoice_number) ?></td>
                                    <td class="project-status-<?= $projects->project_status->id ?>">
                                        <?= $this->Html->link($projects->project_status->name, ['controller' => 'ProjectStatuses', 'action' => 'view', $projects->project_status->id]) ?>
                                    </td>
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
            <div class="related">
                <h4><?= __('Related Contacts') ?></h4>
                <div class="actions">
                    <a class="button-clear" href="/contacts/add?customer_id=<?= $customer->id ?>">Add Contact</a>
                </div>
                <?php if (!empty($customer->contacts)) : ?>
                    <div class="table-responsive">
                        <table>
                            <thead>
                            <tr>
                                <!-- <th><?= __('Id') ?></th> -->
                                <th><?= __('Name') ?></th>
                                <th><?= __('Role') ?></th>
                                <th><?= __('Telephone') ?></th>
                                <th><?= __('Email') ?></th>
                                <!-- <th><?= __('Notes') ?></th>
                            <th><?= __('Customer Id') ?></th> -->
                                <th class="actions"><?= __('Actions') ?></th>
                            </tr>
                            </thead>
                            <?php foreach ($customer->contacts as $contacts) : ?>
                                <tr>
                                    <!-- <td><?= h($contacts->id) ?></td> -->
                                    <td><?= $this->Html->link($contacts->name, ['controller' => 'Contacts', 'action' => 'view', $contacts->id]) ?></td>
                                    <td><?= h($contacts->role) ?></td>
                                    <td><a href="tel:<?= h($contacts->telephone) ?>"><?= h($contacts->telephone) ?></a>
                                    </td>
                                    <td><a href="mailto:<?= h($contacts->email) ?>"><?= h($contacts->email) ?></a></td>
                                    <!-- <td><?= h($contacts->notes) ?></td>
                            <td><?= h($contacts->customer_id) ?></td> -->
                                    <td class="actions">
                                        <?= $this->Html->link(__('Edit'), ['controller' => 'Contacts', 'action' => 'edit', $contacts->id]) ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <h4><?= __('Data') ?></h4>
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
                            <th><?= __('Address addition') ?></th>
                            <td><?= h($customer->address_addition) ?></td>
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
                        <tr>
                            <th><?= __('Internal') ?></th>
                            <td><?= $customer->internal ? __('Yes') : __('No'); ?></td>
                        </tr>
                    </table>
                </div>
                <div class="col">
                    <?php if ($customer->notes) { ?>
                        <div class="text">
                            <strong><?= __('Notes') ?></strong>
                            <blockquote>
                                <?= $parsedown->parse(h($customer->notes)); ?>
                            </blockquote>
                        </div>
                    <?php } ?>
                </div>
            </div>
            <div class="related">
                <h4><?= __('Related Projects') ?></h4>
                <div class="actions">
                    <a class="button-clear" href="/projects/add?customer_id=<?= $customer->id ?>">Add Project</a>
                </div>
                <?php if (!empty($customer->projects)) : ?>
                    <div class="table-responsive">
                        <table>
                            <thead>
                            <tr>
                                <th><?= __('Name') ?></th>
                                <th><?= __('Start') ?></th>
                                <th><?= __('Invoice Number') ?></th>
                                <th><?= __('Project Status Id') ?></th>
                                <th class="actions"><?= __('Actions') ?></th>
                            </tr>
                            </thead>
                            <?php foreach ($customer->projects as $projects) : ?>
                                <tr>
                                    <td><?= $this->Html->link($projects->name, ['controller' => 'Projects', 'action' => 'view', $projects->id]) ?></td>
                                    <td><?= h($projects->start) ?></td>
                                    <td><?= h($projects->invoice_number) ?></td>
                                    <td class="project-status-<?= $projects->project_status->id ?>">
                                        <?= $this->Html->link($projects->project_status->name, ['controller' => 'ProjectStatuses', 'action' => 'view', $projects->project_status->id]) ?>
                                    </td>
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
