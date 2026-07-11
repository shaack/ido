<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Project $project
 * @var \Cake\Collection\CollectionInterface|string[] $customers
 * @var \Cake\Collection\CollectionInterface|string[] $projectStatuses
 * @var array<int, float|null> $customerRates
 */
?>
<div class="row">
    <aside class="column">
        <div class="side-nav">
            <h4 class="heading"><?= __('Actions') ?></h4>
            <?= $this->Html->link(__('List Projects'), ['action' => 'index'], ['class' => 'side-nav-item']) ?>
        </div>
    </aside>
    <div class="column-responsive column-80">
        <div class="projects form content">
            <?= $this->Form->create($project) ?>
            <fieldset>
                <legend><?= __('Add Project') ?></legend>
                <?php
                    echo $this->Form->control('name');
                    echo $this->Form->control('customer_id', ['options' => $customers, 'empty' => true]);
                    echo $this->Form->control('start', ['empty' => true]);
                    echo $this->Form->control('end', ['empty' => true]);
                    echo $this->Form->control('fixed_price');
                    echo $this->Form->control('hourly_rate');
                    echo $this->Form->control('notes');
                    echo $this->Form->control('description');
                    echo $this->Form->control('invoice_number');
                    echo $this->Form->control('invoice_date', ['empty' => true]);
                    echo $this->Form->control('paid_at', ['empty' => true]);
                    echo $this->Form->control('project_status_id', ['options' => $projectStatuses, 'empty' => true]);
                ?>
            </fieldset>
            <?= $this->Form->button(__('Submit')) ?>
            <?= $this->Form->end() ?>
        </div>
    </div>
</div>
<script>
    // Wechselt der Kunde, wird sein Stundensatz eingetragen. Ein danach von
    // Hand geänderter Satz bleibt stehen, bis wieder ein anderer Kunde gewählt
    // wird.
    (function () {
        const rates = <?= json_encode($customerRates, JSON_UNESCAPED_UNICODE) ?>;
        const customerField = document.getElementById("customer-id");
        const rateField = document.getElementById("hourly-rate");
        if (!customerField || !rateField) {
            return;
        }
        customerField.addEventListener("change", function () {
            const rate = rates[this.value];
            rateField.value = rate === undefined || rate === null ? "" : rate;
        });
    })();
</script>
