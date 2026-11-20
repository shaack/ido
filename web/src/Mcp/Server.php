<?php
declare(strict_types=1);

namespace App\Mcp;

use Cake\Log\Log;
use InvalidArgumentException;
use stdClass;
use Throwable;

/**
 * Beantwortet die JSON-RPC-Nachrichten des Model Context Protocol, ohne HTTP
 * zu kennen. Zustandslos: keine Sessions, keine Notifications an den Client,
 * kein Stream. Nach dem Vorbild des MCP-AddOns von Reboot CMS.
 */
class Server
{
    /** Unterstützte Versionen des Protokolls, die neueste zuerst. */
    public const PROTOCOL_VERSIONS = ['2025-11-25', '2025-06-18', '2025-03-26', '2024-11-05'];

    private const INSTRUCTIONS = <<<'TXT'
ido is the personal project management, todo, time tracking and billing tool of Stefan Haack, a self-employed developer. Single user, the data is real and drives his invoices.

Model: Customer (shortcut like "ASO") -> Project (has a status and an hourly rate) -> Service (a billable position) -> Task -> TimeTracking (hours). Tasks and time always hang off a service, so to log work find the project with list_projects or get_project and pick the service.

Billing rules: the billed effort of a service is its tracked hours rounded to quarter hours, times the hourly rate of the project. A service with fixed_price set is billed with that amount, tracked hours do not change it (negative amounts are deductions for installments already paid). effort_est is the estimate for the offer only.

Project statuses: 5 Requested, 10 Offer sent, 15 Runs, 20 Finished/send invoice, 25 Invoice sent, 30 Invoice dunned, 35 Deferred, 40 and above ended (paid, canceled, free of charge).

Write tools create tasks, services and time trackings and mark tasks done. Ask before logging time the user did not state explicitly. Nothing can be deleted through this server, and the database is backed up before every change (list_backups). Restoring a backup is only possible on the console of the server, tell the user if one is needed.
TXT;

    private Tools $tools;

    public function __construct(Tools $tools)
    {
        $this->tools = $tools;
    }

    /**
     * Die Antwort auf eine Nachricht, null für eine Notification oder eine
     * Antwort des Clients, die keine Antwort brauchen.
     */
    public function handle(mixed $message): ?array
    {
        if (!is_array($message) || ($message['jsonrpc'] ?? null) !== '2.0') {
            return self::error(null, -32600, 'Invalid request, expected a JSON-RPC 2.0 message');
        }
        $id = $message['id'] ?? null;
        $method = $message['method'] ?? null;
        if (!is_string($method)) {
            return null; // eine Antwort des Clients
        }
        if (!array_key_exists('id', $message)) {
            return null; // eine Notification, etwa notifications/initialized
        }
        $params = is_array($message['params'] ?? null) ? $message['params'] : [];

        return match ($method) {
            'initialize' => self::result($id, $this->initialize($params)),
            'ping' => self::result($id, new stdClass()),
            'tools/list' => self::result($id, ['tools' => $this->tools->definitions()]),
            'tools/call' => self::result($id, $this->callTool($params)),
            default => self::error($id, -32601, "Method '$method' not found"),
        };
    }

    private function initialize(array $params): array
    {
        $requested = $params['protocolVersion'] ?? null;

        return [
            'protocolVersion' => in_array($requested, self::PROTOCOL_VERSIONS, true) ? $requested : self::PROTOCOL_VERSIONS[0],
            'capabilities' => ['tools' => ['listChanged' => false]],
            'serverInfo' => ['name' => 'ido', 'title' => 'ido', 'version' => '1.0.0'],
            'instructions' => self::INSTRUCTIONS,
        ];
    }

    private function callTool(array $params): array
    {
        $name = (string)($params['name'] ?? '');
        $arguments = is_array($params['arguments'] ?? null) ? $params['arguments'] : [];
        try {
            $result = $this->tools->call($name, $arguments);
        } catch (InvalidArgumentException $e) {
            return self::toolError($e->getMessage());
        } catch (Throwable $e) {
            Log::error("MCP: tool '$name' failed: " . $e->getMessage());

            return self::toolError("The tool '$name' failed, see logs/error.log of ido");
        }
        if (!$this->tools->isReadOnly($name)) {
            Log::info("MCP: $name " . json_encode($arguments, JSON_UNESCAPED_UNICODE));
        }

        return [
            'content' => [['type' => 'text', 'text' => json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE)]],
            'structuredContent' => $result,
            'isError' => false,
        ];
    }

    private static function toolError(string $message): array
    {
        return ['content' => [['type' => 'text', 'text' => $message]], 'isError' => true];
    }

    private static function result(mixed $id, mixed $result): array
    {
        return ['jsonrpc' => '2.0', 'id' => $id, 'result' => $result];
    }

    public static function error(mixed $id, int $code, string $message): array
    {
        return ['jsonrpc' => '2.0', 'id' => $id, 'error' => ['code' => $code, 'message' => $message]];
    }
}
