<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Customer Entity
 *
 * @property int $Id
 * @property string|null $shortcut
 * @property float|null $hourly_rate
 * @property string|null $website
 * @property string|null $notes
 * @property string|null $color
 * @property string|null $current
 * @property string|null $name
 * @property string|null $street
 * @property string|null $zip_city
 * @property string|null $country
 * @property string|null $invoice_email
 */
class Customer extends Entity
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
        'shortcut' => true,
        'hourly_rate' => true,
        'website' => true,
        'notes' => true,
        'color' => true,
        'current' => true,
        'name' => true,
        'street' => true,
        'zip_city' => true,
        'country' => true,
        'invoice_email' => true,
    ];
}