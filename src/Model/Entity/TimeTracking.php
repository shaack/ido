<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * TimeTracking Entity
 *
 * @property int $id
 * @property int $task_id
 * @property \Cake\I18n\FrozenTime|null $start
 * @property float|null $pause
 * @property float|null $duration
 *
 * @property \App\Model\Entity\Task $task
 */
class TimeTracking extends Entity
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
        'task_id' => true,
        'start' => true,
        'pause' => true,
        'duration' => true,
        'task' => true,
    ];
}
