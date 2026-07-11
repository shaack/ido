<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * TimeTracking Entity
 *
 * @property int $id
 * @property int $task_id
 * @property \Cake\I18n\DateTime|null $created
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
    protected array $_accessible = [
        'task_id' => true,
        'duration' => true,
        'task' => true,
    ];
}
