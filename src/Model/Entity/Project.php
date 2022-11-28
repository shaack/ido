<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Project Entity
 *
 * @property int $id
 * @property string $name
 * @property int|null $customer_id
 * @property \Cake\I18n\FrozenDate|null $start
 * @property \Cake\I18n\FrozenDate|null $end_est
 * @property \Cake\I18n\FrozenDate|null $end
 * @property bool|null $fixed_price
 * @property float $hourly_rate
 * @property string|null $notes
 * @property string|null $description
 * @property string|null $invoice_number
 * @property \Cake\I18n\FrozenDate|null $invoice_date
 * @property \Cake\I18n\FrozenDate|null $paid_at
 * @property int|null $parent_id
 * @property int|null $project_status_id
 * @property \Cake\I18n\FrozenTime|null $created
 *
 * @property \App\Model\Entity\Customer $customer
 * @property \App\Model\Entity\Project $parent_project
 * @property \App\Model\Entity\ProjectStatus $project_status
 * @property \App\Model\Entity\Project[] $child_projects
 * @property \App\Model\Entity\Service[] $services
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
        'name' => true,
        'customer_id' => true,
        'start' => true,
        'end_est' => true,
        'end' => true,
        'fixed_price' => true,
        'hourly_rate' => true,
        'notes' => true,
        'description' => true,
        'invoice_number' => true,
        'invoice_date' => true,
        'paid_at' => true,
        'parent_id' => true,
        'project_status_id' => true,
        'created' => true,
        'customer' => true,
        'parent_project' => true,
        'project_status' => true,
        'child_projects' => true,
        'services' => true,
    ];

    function effort()
    {
        $services = $this->services;
        $sum = 0.0;
        foreach ($services as $service) {
            $sum += $service->effort();
        }
        return round($sum * 4) / 4;
    }

    function costs()
    {
        return $this->effort() * $this->customer->hourly_rate;
    }

    function vat()
    {
        return $this->costs() * 0.19;
    }

    function total()
    {
        return $this->costs() + $this->vat();
    }
}
