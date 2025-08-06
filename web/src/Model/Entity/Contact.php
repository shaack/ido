<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Contact Entity
 *
 * @property int $id
 * @property string $name
 * @property string|null $role
 * @property string|null $telephone
 * @property string|null $email
 * @property string|null $notes
 * @property int $customer_id
 *
 * @property \App\Model\Entity\Customer $customer
 */
class Contact extends Entity
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
        'name' => true,
        'role' => true,
        'telephone' => true,
        'email' => true,
        'notes' => true,
        'customer_id' => true,
        'customer' => true,
    ];
}
