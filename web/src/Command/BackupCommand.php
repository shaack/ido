<?php
declare(strict_types=1);

namespace App\Command;

use App\Backup\DatabaseBackup;
use Cake\Command\Command;
use Cake\Console\Arguments;
use Cake\Console\ConsoleIo;
use Cake\Console\ConsoleOptionParser;

/**
 * Sicherungen der Datenbank von der Konsole, siehe DatabaseBackup.
 *
 *   bin/cake backup                 Sicherung anlegen
 *   bin/cake backup --list          Sicherungen auflisten
 *   bin/cake backup --restore NAME  Sicherung einspielen, vorher wird der
 *                                   aktuelle Stand gesichert
 *
 * Einspielen gibt es bewusst nur hier und nicht im Web oder über MCP.
 */
class BackupCommand extends Command
{
    public static function defaultName(): string
    {
        return 'backup';
    }

    public function buildOptionParser(ConsoleOptionParser $parser): ConsoleOptionParser
    {
        return $parser
            ->setDescription('Creates, lists and restores backups of the database in db-backups/.')
            ->addOption('list', ['short' => 'l', 'boolean' => true, 'help' => 'List the backups'])
            ->addOption('restore', ['help' => 'Restore the backup with this name, the current state is backed up first'])
            ->addOption('yes', ['short' => 'y', 'boolean' => true, 'help' => 'Restore without asking']);
    }

    public function execute(Arguments $args, ConsoleIo $io): int
    {
        $backup = new DatabaseBackup();

        if ($args->getOption('list')) {
            foreach ($backup->list() as $b) {
                $io->out(sprintf('%s  %7.1f KB', $b['name'], $b['size'] / 1024));
            }

            return static::CODE_SUCCESS;
        }

        $restore = $args->getOption('restore');
        if (is_string($restore) && $restore !== '') {
            $backup->path($restore); // wirft bei unbekanntem Namen, bevor gefragt wird
            if (!$args->getOption('yes')
                && $io->askChoice("Replace all tables with the content of $restore?", ['y', 'n'], 'n') !== 'y') {
                $io->out('Nothing restored.');

                return static::CODE_SUCCESS;
            }
            $before = $backup->restore($restore);
            $io->success("Restored $restore. The state before is in {$before['name']}.");
            $io->out('Clear tmp/cache/models/ if the restored schema differs.');

            return static::CODE_SUCCESS;
        }

        $created = $backup->create('manual');
        $io->success(sprintf('Created %s (%.1f KB)', $created['name'], $created['size'] / 1024));

        return static::CODE_SUCCESS;
    }
}
