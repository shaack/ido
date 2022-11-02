<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Service Entity
 *
 * @property int $Id
 * @property string|null $title
 * @property string|null $done
 * @property \Cake\I18n\FrozenDate|null $created
 * @property int|null $project
 * @property float|null $effort_est
 * @property float|null $estimation_or_fixed_price
 * @property float|null $effort
 * @property string|null $effort2
 * @property float|null $costs
 * @property string|null $notes
 */
class Service extends Entity
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
        'done' => true,
        'created' => true,
        'project' => true,
        'effort_est' => true,
        'estimation_or_fixed_price' => true,
        'effort' => true,
        'effort2' => true,
        'costs' => true,
        'notes' => true,
    ];
}
