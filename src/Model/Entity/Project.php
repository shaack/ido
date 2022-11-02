<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Project Entity
 *
 * @property int $Id
 * @property string|null $title
 * @property int|null $customer
 * @property \Cake\I18n\FrozenDate|null $start
 * @property \Cake\I18n\FrozenDate|null $end_est
 * @property \Cake\I18n\FrozenDate|null $end
 * @property string|null $fixed_price
 * @property float|null $hourly_rate
 * @property string|null $status
 * @property string|null $notes
 * @property string|null $description
 * @property string|null $invoice_number
 * @property \Cake\I18n\FrozenDate|null $invoice_date
 * @property \Cake\I18n\FrozenDate|null $paid_at
 * @property int|null $advance_payment_of
 */
class Project extends Entity
{
    /**
     * Fields that can be mass assigned using newEntity() or patchEntity().
     *
     * Note that when '*' is set to true, this allows all unspecified fields to
     * be mass assigned. For security purposes, it is advised to set '*' to false
     * (or remove it), and explicitly make individual fields accessible as needed.
     *
     * @var array<string, bool>
     */
    protected $_accessible = [
        'title' => true,
        'customer' => true,
        'start' => true,
        'end_est' => true,
        'end' => true,
        'fixed_price' => true,
        'hourly_rate' => true,
        'status' => true,
        'notes' => true,
        'description' => true,
        'invoice_number' => true,
        'invoice_date' => true,
        'paid_at' => true,
        'advance_payment_of' => true,
    ];
}
