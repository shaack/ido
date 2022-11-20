<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Task Entity
 *
 * @property int $id
 * @property bool $marked
 * @property int|null $prio
 * @property string|null $name
 * @property \Cake\I18n\FrozenDate|null $start_est
 * @property \Cake\I18n\FrozenDate|null $deadline
 * @property int|null $duration_est
 * @property string|null $link
 * @property int|null $service_id
 * @property \Cake\I18n\FrozenTime|null $created
 * @property \Cake\I18n\FrozenTime|null $modified
 * @property string|null $notes
 * @property bool $done
 * @property \Cake\I18n\FrozenTime|null $done_at
 * @property float|null $duration
 *
 * @property \App\Model\Entity\Service $service
 * @property \App\Model\Entity\TimeTracking[] $time_trackings
 */
class Task extends Entity
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
        'marked' => true,
        'prio' => true,
        'name' => true,
        'start_est' => true,
        'deadline' => true,
        'duration_est' => true,
        'link' => true,
        'service_id' => true,
        'created' => true,
        'modified' => true,
        'notes' => true,
        'done' => true,
        'done_at' => true,
        'duration' => true,
        'service' => true,
        'time_trackings' => true
    ];

    function duration()
    {
        $timeTrackings = $this->time_trackings;
        $sum = 0.0;
        foreach ($timeTrackings as $timeTracking) {
            $sum += $timeTracking->duration;
        }
        return round($sum * 4) / 4;
    }
}
