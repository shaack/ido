<?php
declare(strict_types=1);

namespace App\Backup;

use Cake\Database\Connection;
use Cake\Datasource\ConnectionManager;
use Cake\I18n\DateTime;
use InvalidArgumentException;
use RuntimeException;

/**
 * Sicherungen der Datenbank als gzip-komprimierter SQL-Dump in `db-backups/`.
 *
 * Der Dump entsteht in PHP und nicht über mysqldump, weil auf dem Plesk-Host
 * die Konsole ein anderes PHP hat als der Webserver und so Web, MCP und
 * `bin/cake backup` denselben Weg nehmen. Er liest alle Tabellen in einem
 * konsistenten Snapshot. Jede Anweisung steht auf einer eigenen Zeile, damit
 * restore() die Datei zeilenweise einspielen kann. Die Datei läuft ebenso mit
 * `gunzip -c … | mysql`.
 *
 * Ausgelöst werden Sicherungen von Hand (Seite Backups, `bin/cake backup`,
 * MCP-Tool create_backup) und automatisch über ensureRecent(): einmal am Tag
 * beim ersten Aufruf der App und vor jeder Schreibaktion über MCP.
 *
 * Aufbewahrung, siehe prune(): alles aus den letzten 7 Tagen, danach die
 * jüngste Sicherung je Tag bis 90 Tage, danach die jüngste je Monat.
 */
class DatabaseBackup
{
    private const PREFIX = 'ido-';
    private const SUFFIX = '.sql.gz';
    private const NAME_PATTERN = '/^ido-(\d{4}-\d{2}-\d{2})-(\d{6})-([a-z0-9-]+)\.sql\.gz$/';
    private const ROWS_PER_INSERT = 200;

    private Connection $connection;
    private string $dir;

    public function __construct(?Connection $connection = null, ?string $dir = null)
    {
        /** @var \Cake\Database\Connection $default */
        $default = ConnectionManager::get('default');
        $this->connection = $connection ?? $default;
        $this->dir = rtrim($dir ?? ROOT . DS . 'db-backups', DS);
    }

    /**
     * Legt eine Sicherung an und gibt ihre Daten zurück, siehe info().
     *
     * @param string $reason kurzer Anlass im Dateinamen, "manual", "daily", "mcp"
     */
    public function create(string $reason): array
    {
        $this->ensureDir();
        $reason = trim(preg_replace('/[^a-z0-9]+/', '-', strtolower($reason)) ?? '', '-') ?: 'manual';
        $name = self::PREFIX . DateTime::now()->format('Y-m-d-His') . '-' . $reason . self::SUFFIX;
        $path = $this->dir . DS . $name;
        $tmp = $path . '.part';

        $gz = gzopen($tmp, 'wb6');
        if ($gz === false) {
            throw new RuntimeException("Cannot write $tmp");
        }
        try {
            $this->dump(fn(string $line) => gzwrite($gz, $line . "\n"));
        } catch (\Throwable $e) {
            gzclose($gz);
            @unlink($tmp);
            throw $e;
        }
        gzclose($gz);
        if (!rename($tmp, $path)) {
            throw new RuntimeException("Cannot move $tmp to $path");
        }
        $this->prune();

        return $this->info($name);
    }

    /**
     * Legt eine Sicherung an, wenn die jüngste älter ist als $maxAgeSeconds.
     * Ein Lock verhindert, dass zwei gleichzeitige Aufrufe beide sichern.
     *
     * @return array|null die neue Sicherung oder null, wenn keine nötig war
     */
    public function ensureRecent(int $maxAgeSeconds, string $reason): ?array
    {
        $this->ensureDir();
        $lock = fopen($this->dir . DS . '.lock', 'c');
        if ($lock === false || !flock($lock, LOCK_EX)) {
            throw new RuntimeException('Cannot lock the backup directory');
        }
        try {
            $newest = $this->list()[0] ?? null;
            if ($newest !== null && $newest['created']->getTimestamp() > time() - $maxAgeSeconds) {
                return null;
            }

            return $this->create($reason);
        } finally {
            flock($lock, LOCK_UN);
            fclose($lock);
        }
    }

    /**
     * Alle Sicherungen, die jüngste zuerst.
     */
    public function list(): array
    {
        $backups = [];
        foreach (glob($this->dir . DS . self::PREFIX . '*' . self::SUFFIX) ?: [] as $path) {
            if (preg_match(self::NAME_PATTERN, basename($path))) {
                $backups[] = $this->info(basename($path));
            }
        }
        usort($backups, fn($a, $b) => strcmp($b['name'], $a['name']));

        return $backups;
    }

    /**
     * Der Pfad einer Sicherung. Wirft bei einem unbekannten oder ungültigen
     * Namen, so kommt kein Pfad von außen an der Prüfung vorbei.
     */
    public function path(string $name): string
    {
        $path = $this->dir . DS . $name;
        if (!preg_match(self::NAME_PATTERN, $name) || !is_file($path)) {
            throw new InvalidArgumentException("No backup named '$name'");
        }

        return $path;
    }

    /**
     * Spielt eine Sicherung ein und ersetzt damit alle Tabellen, die sie
     * enthält. Vorher wird der aktuelle Stand gesichert.
     *
     * @return array die Sicherung des Stands vor dem Einspielen
     */
    public function restore(string $name): array
    {
        $path = $this->path($name);
        $before = $this->create('before-restore');
        $gz = gzopen($path, 'rb');
        if ($gz === false) {
            throw new RuntimeException("Cannot read $path");
        }
        try {
            while (($line = gzgets($gz)) !== false) {
                $line = rtrim($line, "\n");
                if ($line !== '' && !str_starts_with($line, '--')) {
                    $this->connection->execute($line);
                }
            }
        } finally {
            gzclose($gz);
        }

        return $before;
    }

    public function info(string $name): array
    {
        preg_match(self::NAME_PATTERN, $name, $m);

        return [
            'name' => $name,
            'created' => DateTime::createFromFormat('Y-m-d His', $m[1] . ' ' . $m[2]),
            'reason' => $m[3],
            'size' => (int)@filesize($this->dir . DS . $name),
        ];
    }

    /**
     * Schreibt den Dump zeilenweise an $write. Alle Tabellen werden in einem
     * konsistenten Snapshot gelesen.
     */
    private function dump(callable $write): void
    {
        $driver = $this->connection->getDriver();
        $charset = $this->connection->execute('SELECT @@character_set_client')->fetchColumn(0);
        $database = $this->connection->execute('SELECT DATABASE()')->fetchColumn(0);

        $write('-- ido database backup, ' . DateTime::now()->format('Y-m-d H:i:s T') . ", database $database");
        $write("SET NAMES $charset;");
        // timestamp-Spalten stehen in der Zeitzone dieser Session im Dump,
        // mit mysql eingespielt sonst in der des Clients.
        $write('SET time_zone = ' . $driver->quote((string)$this->connection->execute('SELECT @@session.time_zone')->fetchColumn(0)) . ';');
        $write('SET FOREIGN_KEY_CHECKS=0;');
        $write("SET SQL_MODE='NO_AUTO_VALUE_ON_ZERO';");

        $this->connection->execute('SET SESSION TRANSACTION ISOLATION LEVEL REPEATABLE READ');
        $this->connection->execute('START TRANSACTION WITH CONSISTENT SNAPSHOT');
        try {
            $tables = $this->connection->execute("SHOW FULL TABLES WHERE Table_type = 'BASE TABLE'")->fetchAll('num');
            foreach ($tables as [$table]) {
                $quoted = $driver->quoteIdentifier($table);
                $create = $this->connection->execute("SHOW CREATE TABLE $quoted")->fetch('num')[1];
                $write('');
                $write("-- $table");
                $write("DROP TABLE IF EXISTS $quoted;");
                $write(str_replace(["\r", "\n"], ' ', $create) . ';');

                $statement = $this->connection->execute("SELECT * FROM $quoted");
                $values = [];
                while (($row = $statement->fetch('num')) !== false) {
                    $values[] = '(' . implode(',', array_map(fn($v) => $this->literal($v), $row)) . ')';
                    if (count($values) === self::ROWS_PER_INSERT) {
                        $write("INSERT INTO $quoted VALUES " . implode(',', $values) . ';');
                        $values = [];
                    }
                }
                if ($values) {
                    $write("INSERT INTO $quoted VALUES " . implode(',', $values) . ';');
                }
            }
        } finally {
            $this->connection->execute('COMMIT');
        }
        $write('');
        $write('SET FOREIGN_KEY_CHECKS=1;');
    }

    /**
     * Ein Wert als SQL-Literal. PDO liefert Zahlen seit PHP 8.1 als int und
     * float. Ein (string) auf den float rundet auf die ini-Einstellung
     * precision (14 Stellen) und verfälscht double-Spalten wie die Dauer,
     * var_export() schreibt ihn verlustfrei.
     */
    private function literal(mixed $value): string
    {
        return match (true) {
            $value === null => 'NULL',
            is_int($value) => (string)$value,
            is_float($value) => var_export($value, true),
            default => $this->connection->getDriver()->quote((string)$value),
        };
    }

    /**
     * Räumt alte Sicherungen ab: alles aus den letzten 7 Tagen bleibt, danach
     * die jüngste je Tag bis 90 Tage, danach die jüngste je Monat. Die jüngste
     * Sicherung bleibt immer.
     */
    private function prune(): void
    {
        $now = time();
        $seen = [];
        foreach ($this->list() as $i => $backup) {
            $age = $now - $backup['created']->getTimestamp();
            if ($i === 0 || $age < 7 * 86400) {
                continue;
            }
            $bucket = $age < 90 * 86400 ? $backup['created']->format('Y-m-d') : $backup['created']->format('Y-m');
            if (isset($seen[$bucket])) {
                @unlink($this->dir . DS . $backup['name']);
            } else {
                $seen[$bucket] = true;
            }
        }
    }

    /**
     * Legt `db-backups/` an, nur für den Eigentümer lesbar und per .htaccess
     * gesperrt, falls das Verzeichnis je im Docroot landet.
     */
    private function ensureDir(): void
    {
        if (!is_dir($this->dir) && !mkdir($this->dir, 0700, true) && !is_dir($this->dir)) {
            throw new RuntimeException("Cannot create {$this->dir}");
        }
        if (!is_file($this->dir . DS . '.htaccess')) {
            file_put_contents($this->dir . DS . '.htaccess', "Require all denied\n");
        }
    }
}
