<?php
declare(strict_types=1);

namespace App\View\Helper;

use Cake\I18n\Number;
use Cake\View\Helper;

/**
 * Effort helper
 *
 * Renders hours. Two methods, because the app knows two kinds of hours:
 *
 * - effort()   the estimated or aggregated work of a project, service or task.
 *              Rounded to quarter hours, the same way Project::effort() and
 *              Service::effort() already do it in the entities.
 * - hours()    time that was actually tracked. Never rounded, a 0.15 h entry
 *              stays 0.15 h. Rounding these would misstate the billing basis.
 *
 * Both render with two decimals in the current locale (de_DE: 0,50).
 */
class EffortHelper extends Helper
{
    /**
     * Hours as effort: rounded to the nearest quarter, two decimals.
     *
     * @param float|int|null $hours The hours.
     * @return string
     */
    public function effort(float|int|null $hours): string
    {
        if ($hours === null) {
            return '';
        }

        return Number::precision(round($hours * 4) / 4, 2);
    }

    /**
     * Hours as tracked: two decimals, no rounding.
     *
     * @param float|int|null $hours The hours.
     * @return string
     */
    public function hours(float|int|null $hours): string
    {
        if ($hours === null) {
            return '';
        }

        return Number::precision($hours, 2);
    }
}
