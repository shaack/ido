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
     * Durchschnittliche Arbeitszeit je Wochentag (Mo-So) über die letzten
     * 90 Tage.
     *
     * Der Durchschnitt teilt die je Wochentag erfassten Stunden durch die
     * Anzahl, wie oft dieser Wochentag im Fenster vorkommt. Tage ganz ohne
     * Erfassung zählen also als 0 mit, sonst wären Wochenenden künstlich hoch.
     *
     * @return void
     */
    public function weekdayHours(): void
    {
        $connection = $this->fetchTable('TimeTrackings')->getConnection();

        // Gleicher Umschalter wie im Wochenreport. Standard: interne aus.
        $includeInternal = $this->request->getQuery('internal', '0') === '1';
        $internalFilter = $includeInternal
            ? ''
            : ' AND (c.internal = 0 OR c.internal IS NULL)';

        $windowDays = 90;
        $end = new Date();                          // heute
        $start = $end->subDays($windowDays - 1);    // 90 Tage inkl. heute

        // WEEKDAY(): 0 = Montag … 6 = Sonntag.
        $rows = $connection->execute(
            "SELECT WEEKDAY(tt.created) AS wd, SUM(tt.duration) AS hours
             FROM time_trackings tt
             JOIN tasks tk ON tk.id = tt.task_id
             JOIN services s ON s.id = tk.service_id
             JOIN projects p ON p.id = s.project_id
             LEFT JOIN customers c ON c.id = p.customer_id
             WHERE tt.duration > 0
               AND DATE(tt.created) BETWEEN :start AND :end" . $internalFilter . "
             GROUP BY wd",
            ['start' => $start->format('Y-m-d'), 'end' => $end->format('Y-m-d')],
        )->fetchAll('assoc');

        $sumByWd = array_fill(0, 7, 0.0);
        foreach ($rows as $row) {
            $sumByWd[(int)$row['wd']] = (float)$row['hours'];
        }

        // Vorkommen jedes Wochentags im Fenster zählen, für den Nenner.
        $countByWd = array_fill(0, 7, 0);
        $cursor = $start;
        for ($i = 0; $i < $windowDays; $i++) {
            $countByWd[(int)$cursor->format('N') - 1]++;
            $cursor = $cursor->addDays(1);
        }

        $names = ['Mo', 'Di', 'Mi', 'Do', 'Fr', 'Sa', 'So'];
        $weekdays = [];
        foreach ($names as $wd => $name) {
            $count = $countByWd[$wd];
            $weekdays[] = [
                'name' => $name,
                'weekend' => $wd >= 5,
                'hours' => $count ? $sumByWd[$wd] / $count : 0.0,
            ];
        }

        $this->set(compact('weekdays', 'windowDays', 'start', 'end', 'includeInternal'));
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

        // Interne Projekte (Kunde mit internal=1, z.B. "Eigene") lassen sich
        // per Umschalter einblenden. Standard: ausgeblendet.
        $includeInternal = $this->request->getQuery('internal', '0') === '1';
        $internalFilter = $includeInternal
            ? ''
            : ' AND (c.internal = 0 OR c.internal IS NULL)';

        // %x = ISO-Jahr, %v = ISO-Woche (01-53), beide passend zu YEARWEEK Modus 3.
        // Join über die ganze Kette bis zum Kunden, nur für den internal-Filter.
        $rows = $connection->execute(
            "SELECT DATE_FORMAT(tt.created, '%x-%v') AS label,
                    YEARWEEK(tt.created, 3) AS yw,
                    SUM(tt.duration) AS hours
             FROM time_trackings tt
             JOIN tasks tk ON tk.id = tt.task_id
             JOIN services s ON s.id = tk.service_id
             JOIN projects p ON p.id = s.project_id
             LEFT JOIN customers c ON c.id = p.customer_id
             WHERE tt.duration > 0" . $internalFilter . "
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

        $this->set(compact('weeks', 'totalHours', 'averageHours', 'weekCount', 'includeInternal'));
    }

    /**
     * Effektiver Stundensatz pro Kunde.
     *
     * Abgerechneter Betrag geteilt durch die tatsächlich erfassten Stunden. Die
     * Rechnung läuft über die Entity-Methoden (costs()/effortTracked()), damit
     * Viertelstunden-Rundung und Festpreise genauso einfliessen wie in der
     * echten Rechnung. Interne Kunden werden nicht abgerechnet und bleiben
     * darum aussen vor.
     *
     * @return void
     */
    public function effectiveRate(): void
    {
        $projects = $this->fetchTable('Projects')->find()
            ->contain(['Customers', 'Services.Tasks.TimeTrackings'])
            ->all();

        $byCustomer = [];
        foreach ($projects as $project) {
            $customer = $project->customer;
            if ($customer && $customer->internal) {
                continue;
            }
            $hours = $project->effortTracked();
            if ($hours <= 0) {
                continue;
            }
            $key = $customer ? $customer->id : 0;
            if (!isset($byCustomer[$key])) {
                $byCustomer[$key] = [
                    'name' => $customer ? ($customer->shortcut ?: $customer->name) : '—',
                    'costs' => 0.0,
                    'hours' => 0.0,
                ];
            }
            $byCustomer[$key]['costs'] += $project->costs();
            $byCustomer[$key]['hours'] += $hours;
        }

        $customers = [];
        foreach ($byCustomer as $c) {
            $c['rate'] = $c['hours'] > 0 ? $c['costs'] / $c['hours'] : 0.0;
            $customers[] = $c;
        }
        usort($customers, fn($a, $b) => $b['rate'] <=> $a['rate']);

        $this->set(compact('customers'));
    }

    /**
     * Umsatz (netto) pro Monat, gruppiert nach Rechnungsdatum.
     *
     * Nur berechnete Projekte (invoice_date gesetzt). Der Betrag kommt aus
     * costs(), also netto und exakt wie auf der Rechnung. Monate ohne Rechnung
     * werden mit 0 aufgefüllt, damit die Zeitachse lückenlos ist.
     *
     * @return void
     */
    public function revenuePerMonth(): void
    {
        $projects = $this->fetchTable('Projects')->find()
            ->where(['invoice_date IS NOT' => null])
            ->contain(['Services.Tasks.TimeTrackings'])
            ->all();

        $byMonth = [];
        foreach ($projects as $project) {
            $month = $project->invoice_date->i18nFormat('yyyy-MM');
            $byMonth[$month] = ($byMonth[$month] ?? 0.0) + $project->costs();
        }
        ksort($byMonth);

        $months = [];
        $totalRevenue = 0.0;
        // Nur Monate mit berechnetem Umsatz spannen die Achse auf. Rechnungen aus
        // der Zeit vor der Zeiterfassung ergeben über costs() 0 und würden sonst
        // einen langen leeren Vorlauf erzeugen.
        $nonZero = array_keys(array_filter($byMonth, fn($v) => $v > 0));
        if ($nonZero) {
            [$y, $m] = array_map('intval', explode('-', reset($nonZero)));
            [$ey, $em] = array_map('intval', explode('-', end($nonZero)));
            while ($y < $ey || ($y === $ey && $m <= $em)) {
                $label = sprintf('%04d-%02d', $y, $m);
                $revenue = $byMonth[$label] ?? 0.0;
                $months[] = ['label' => $label, 'revenue' => $revenue];
                $totalRevenue += $revenue;
                if (++$m > 12) {
                    $m = 1;
                    $y++;
                }
            }
        }

        $this->set(compact('months', 'totalRevenue'));
    }

    /**
     * Offene Forderungen: berechnete, aber noch nicht bezahlte Projekte
     * (invoice_date gesetzt, paid_at leer).
     *
     * @return void
     */
    public function receivables(): void
    {
        $projects = $this->fetchTable('Projects')->find()
            ->where(['invoice_date IS NOT' => null, 'paid_at IS' => null])
            ->contain(['Customers', 'Services.Tasks.TimeTrackings'])
            ->orderBy(['invoice_date' => 'ASC'])
            ->all();

        $today = new \DateTimeImmutable((new Date())->format('Y-m-d'));
        $items = [];
        $totalNet = 0.0;
        $totalGross = 0.0;
        foreach ($projects as $project) {
            $net = $project->costs();
            // Rechnungen aus der Zeit vor der Zeiterfassung ergeben über costs()
            // 0 (der historische Betrag steckt in nicht mehr gelesenen Spalten).
            // Solche Nullzeilen wären nur Rauschen.
            if ($net <= 0.005) {
                continue;
            }
            $gross = $project->total();
            $invoiceDate = new \DateTimeImmutable($project->invoice_date->format('Y-m-d'));
            $items[] = [
                'customer' => $project->customer
                    ? ($project->customer->shortcut ?: $project->customer->name)
                    : '—',
                'project' => $project->name,
                'number' => $project->invoice_number,
                'date' => $project->invoice_date,
                'days' => (int)$today->diff($invoiceDate)->days,
                'net' => $net,
                'gross' => $gross,
            ];
            $totalNet += $net;
            $totalGross += $gross;
        }

        $this->set(compact('items', 'totalNet', 'totalGross'));
    }

    /**
     * Erfasste Stunden pro Kunde in einem Zeitfenster (90 Tage, 1 Jahr oder
     * gesamt). Rohe Summe der erfassten Zeit, daher als SQL-Aggregat.
     *
     * @return void
     */
    public function hoursPerCustomer(): void
    {
        $connection = $this->fetchTable('TimeTrackings')->getConnection();

        $includeInternal = $this->request->getQuery('internal', '0') === '1';
        $internalFilter = $includeInternal
            ? ''
            : ' AND (c.internal = 0 OR c.internal IS NULL)';

        $periods = ['90' => 90, '365' => 365];
        $period = (string)$this->request->getQuery('period', '365');
        $dateFilter = '';
        $params = [];
        if (isset($periods[$period])) {
            $start = (new Date())->subDays($periods[$period] - 1);
            $dateFilter = ' AND DATE(tt.created) >= :start';
            $params['start'] = $start->format('Y-m-d');
        } else {
            $period = 'all';
        }

        $rows = $connection->execute(
            "SELECT c.id AS cid,
                    COALESCE(NULLIF(c.shortcut, ''), c.name, '—') AS name,
                    SUM(tt.duration) AS hours
             FROM time_trackings tt
             JOIN tasks tk ON tk.id = tt.task_id
             JOIN services s ON s.id = tk.service_id
             JOIN projects p ON p.id = s.project_id
             LEFT JOIN customers c ON c.id = p.customer_id
             WHERE tt.duration > 0" . $dateFilter . $internalFilter . "
             GROUP BY cid, name
             ORDER BY hours DESC",
            $params,
        )->fetchAll('assoc');

        $customers = [];
        $totalHours = 0.0;
        foreach ($rows as $row) {
            $hours = (float)$row['hours'];
            $customers[] = ['name' => $row['name'], 'hours' => $hours];
            $totalHours += $hours;
        }

        $this->set(compact('customers', 'totalHours', 'period', 'includeInternal'));
    }
}
