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
 * @property \Cake\I18n\Date|null $start
 * @property \Cake\I18n\Date|null $end
 * @property bool|null $fixed_price
 * @property float $hourly_rate
 * @property string|null $notes
 * @property string|null $description
 * @property string|null $invoice_number
 * @property \Cake\I18n\Date|null $invoice_date
 * @property \Cake\I18n\Date|null $paid_at
 * @property int|null $parent_id
 * @property int|null $project_status_id
 * @property \Cake\I18n\DateTime|null $created
 *
 * @property \App\Model\Entity\Customer $customer
 * @property \App\Model\Entity\ProjectStatus $project_status
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
    protected array $_accessible = [
        'name' => true,
        'customer_id' => true,
        'start' => true,
        'end' => true,
        'hourly_rate' => true,
        'description' => true,
        'invoice_number' => true,
        'invoice_date' => true,
        'paid_at' => true,
        'project_status_id' => true,
        'created' => true,
        'customer' => true,
        'project_status' => true,
        'services' => true,
    ];

    /**
     * Die tatsächlich erfasste Zeit des Projekts, ungerundet.
     */
    function effortTracked()
    {
        $services = $this->services;
        $sum = 0.0;
        foreach ($services as $service) {
            $sum += $service->effortTracked();
        }
        return $sum;
    }

    /**
     * Die abgerechnete Zeit des Projekts, also die Summe der je Leistung auf
     * Viertelstunden gerundeten Werte. Das entspricht dem Rechnungsbetrag
     * geteilt durch den Stundensatz.
     */
    function effort()
    {
        $services = $this->services;
        $sum = 0.0;
        foreach ($services as $service) {
            $sum += $service->effort();
        }
        return round($sum * 4) / 4;
    }

    function effortPlanned()
    {
        $services = $this->services;
        $sum = 0.0;
        foreach ($services as $service) {
            $sum += $service->effortPlanned();
        }
        return round($sum * 4) / 4;
    }

    function costs()
    {
        $services = $this->services;
        $sum = 0.0;
        foreach ($services as $service) {
            $sum += $service->costs($this->hourly_rate);
        }
        return $sum;
    }

    function costsPlanned()
    {
        return $this->effortPlanned() * $this->hourly_rate;
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
