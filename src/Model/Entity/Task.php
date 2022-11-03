<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Task Entity
 *
 * @property int $Id
 * @property string|null $desktop
 * @property string|null $prio
 * @property string|null $title
 * @property \Cake\I18n\FrozenDate|null $start_est
 * @property \Cake\I18n\FrozenDate|null $deadline
 * @property int|null $duration_est
 * @property string|null $link
 * @property Service|null $service
 * @property int|null $customer
 * @property \Cake\I18n\FrozenDate|null $created
 * @property string|null $notes
 * @property string|null $status
 * @property \Cake\I18n\FrozenTime|null $start
 * @property \Cake\I18n\FrozenTime|null $done
 * @property float|null $duration
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
        'desktop' => true,
        'prio' => true,
        'title' => true,
        'start_est' => true,
        'deadline' => true,
        'duration_est' => true,
        'link' => true,
        'service' => true,
        'customer' => true,
        'created' => true,
        'notes' => true,
        'status' => true,
        'start' => true,
        'done' => true,
        'duration' => true,
    ];
}
