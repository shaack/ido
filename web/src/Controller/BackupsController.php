<?php
declare(strict_types=1);

namespace App\Controller;

use App\Backup\DatabaseBackup;
use Cake\Http\Exception\NotFoundException;
use Cake\Http\Response;
use InvalidArgumentException;

/**
 * Backups Controller
 *
 * Listet die Sicherungen der Datenbank, legt eine von Hand an und lädt eine
 * herunter. Einspielen geht bewusst nur über `bin/cake backup --restore`.
 */
class BackupsController extends AppController
{
    public function index(): void
    {
        $backups = (new DatabaseBackup())->list();
        $this->set(compact('backups'));
    }

    public function add(): ?Response
    {
        $this->request->allowMethod(['post']);
        $created = (new DatabaseBackup())->create('manual');
        $this->Flash->success(__('Backup {0} created.', $created['name']));

        return $this->redirect(['action' => 'index']);
    }

    public function download(?string $name = null): Response
    {
        try {
            $path = (new DatabaseBackup())->path((string)$name);
        } catch (InvalidArgumentException $e) {
            throw new NotFoundException($e->getMessage());
        }

        return $this->response->withFile($path, ['download' => true, 'name' => (string)$name]);
    }
}
