<?php
/**
 * Das Kundenkürzel als Pill in der Farbe des Kunden, immer als Link auf den
 * Kunden. Ohne Kunden gibt es nichts aus.
 *
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Customer|null $customer
 */
if (empty($customer) || !$customer->id) {
    return;
}
echo $this->Html->link((string)$customer->shortcut, ['controller' => 'Customers', 'action' => 'view', $customer->id], [
    'class' => 'customer-tag',
    'style' => '--customer: ' . ($customer->color ?: 'currentColor'),
    'title' => (string)$customer->name,
]);
