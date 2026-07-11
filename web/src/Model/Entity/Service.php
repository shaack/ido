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
 * @property \Cake\I18n\DateTime $created
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
    protected array $_accessible = [
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

    /**
     * Die tatsächlich erfasste Zeit, ungerundet.
     */
    function effortTracked()
    {
        $tasks = $this->tasks;
        $sum = 0.0;
        foreach ($tasks as $task) {
            $sum += $task->duration();
        }
        return $sum;
    }

    /**
     * Die abgerechnete Zeit: erfasste Zeit, auf Viertelstunden gerundet.
     *
     * costs() multipliziert diesen Wert mit dem Stundensatz, die Rundung geht
     * also direkt in die Rechnung ein. Damit der Stundennachweis nicht eine
     * andere Zahl ausweist als die Rechnung, stellt er beide Werte
     * nebeneinander, erfasst und abgerechnet.
     */
    function effort()
    {
        return round($this->effortTracked() * 4) / 4;
    }

    function effortPlanned() {
        // effort_est ist nullable. CakePHP 4 hat null in den Number-Helfern noch
        // als 0 durchgehen lassen, CakePHP 5 wirft dafür einen TypeError.
        return $this->effort_est ?? 0;
    }

    function costsPlanned() {
        return $this->effort_est * $this->project->hourly_rate;
    }

    function costs($hourlyRate = null)
    {
        if($this->estimation_or_fixed_price) {
            return $this->estimation_or_fixed_price;
        } else {
            return $this->effort() * ($hourlyRate !== null ? $hourlyRate : $this->project->hourly_rate);
        }
    }

    function countTasks()
    {
        $tasks = $this->tasks;
        $count = 0;
        $todoCount = 0;
        foreach ($tasks as $task) {
            $count++;
            if(!$task->done) {
                $todoCount++;
            }
        }
        return ["count" => $count, "todo" => $todoCount];
    }

    function _getSmartName() {
        if($this->name) {
            return $this->name;
        } else {
            return $this->project->name;
        }
    }
}
