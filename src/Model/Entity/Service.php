<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Service Entity
 *
 * @property int $id
 * @property string|null $name
 * @property int $project_id
 * @property float|null $effort_est
 * @property float|null $estimation_or_fixed_price
 * @property float|null $effort
 * @property float|null $costs
 * @property string|null $notes
 * @property int|null $sort
 * @property \Cake\I18n\FrozenTime $created
 *
 * @property \App\Model\Entity\Project $project
 * @property \App\Model\Entity\Task[] $tasks
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
        'name' => true,
        'project_id' => true,
        'effort_est' => true,
        'estimation_or_fixed_price' => true,
        'effort' => true,
        'costs' => true,
        'notes' => true,
        'sort' => true,
        'created' => true,
        'project' => true,
        'tasks' => true,
    ];
}
