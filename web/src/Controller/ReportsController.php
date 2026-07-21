<?php
declare(strict_types=1);

namespace App\Controller;

use Cake\I18n\Date;

/**
 * Reports Controller
 *
 * Auswertungen über die erfassten Zeiten. Reine Lese-Reports, keine Entity
 * dahinter, daher die rohe Aggregat-Abfrage statt des ORM.
 */
class ReportsController extends AppController
{
    /**
     * Übersicht der verfügbaren Reports.
     *
     * @return void
     */
    public function index(): void
    {
    }

    /**
     * Gesamte Arbeitszeit je ISO-Kalenderwoche.
     *
     * X: Jahr-KW (z.B. 2025-20), Y: Summe der erfassten Stunden der Woche.
     * Wochen ohne Erfassung werden mit 0 aufgefüllt, damit die Zeitachse
     * lückenlos bleibt und Pausen als Pausen sichtbar sind.
     *
     * @return void
     */
    public function weeklyHours(): void
    {
        $connection = $this->fetchTable('TimeTrackings')->getConnection();

        // %x = ISO-Jahr, %v = ISO-Woche (01-53), beide passend zu YEARWEEK Modus 3.
        $rows = $connection->execute(
            "SELECT DATE_FORMAT(created, '%x-%v') AS label,
                    YEARWEEK(created, 3) AS yw,
                    SUM(duration) AS hours
             FROM time_trackings
             WHERE duration > 0
             GROUP BY label, yw
             ORDER BY yw",
        )->fetchAll('assoc');

        $weeks = [];
        $totalHours = 0.0;

        if ($rows) {
            $byLabel = [];
            foreach ($rows as $row) {
                $byLabel[$row['label']] = (float)$row['hours'];
            }

            // Vom Montag der ersten bis zur letzten erfassten Woche in
            // Wochenschritten laufen und jede Woche belegen, auch die leeren.
            $first = reset($rows);
            $last = end($rows);
            $cursor = (new Date())->setISODate(
                (int)substr($first['label'], 0, 4),
                (int)substr($first['label'], 5, 2),
            );
            $endLabel = $last['label'];

            // Obergrenze als Reißleine, falls die Endmarke wider Erwarten nie
            // exakt getroffen wird. Deckt gut 19 Jahre Wochen ab.
            $guard = 0;
            do {
                $label = $cursor->format('o-W');
                $hours = $byLabel[$label] ?? 0.0;
                $weeks[] = ['label' => $label, 'hours' => $hours];
                $totalHours += $hours;
                $cursor = $cursor->addDays(7);
            } while ($label !== $endLabel && ++$guard < 1000);
        }

        $weekCount = count($weeks);
        $averageHours = $weekCount ? $totalHours / $weekCount : 0.0;

        $this->set(compact('weeks', 'totalHours', 'averageHours', 'weekCount'));
    }
}
