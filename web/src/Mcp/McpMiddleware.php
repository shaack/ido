<?php
declare(strict_types=1);

namespace App\Mcp;

use Cake\Core\Configure;
use Cake\Http\Response;
use Cake\Log\Log;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * Der MCP-Endpunkt unter /mcp, über den ein Agent wie Claude Code Kunden,
 * Projekte, Tasks und Zeiten liest und Tasks und Zeiten anlegt.
 *
 * Er sitzt vor Routing und CSRF-Schutz, weil ein Agent weder Session noch
 * CSRF-Token hat. Statt dessen braucht jede Anfrage den Token im Header
 * `X-Ido-Token`. Gespeichert ist nur sein SHA-256-Hash, in app_local.php unter
 * `Mcp.tokenHash`. Fehlt der Eintrag, ist der Endpunkt aus.
 *
 * Auf dem Server liegt davor weiter die Basic Auth von Plesk. Den Header
 * `Authorization` belegt deshalb die Basic Auth, der Token steht im eigenen
 * Header. Lokal ohne Basic Auth geht auch `Authorization: Bearer …`.
 *
 * Transport ist Streamable HTTP ohne Sessions: ein POST mit einer
 * JSON-RPC-Nachricht, beantwortet mit JSON.
 */
class McpMiddleware implements MiddlewareInterface
{
    public const PATH = '/mcp';

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        if (rtrim($request->getUri()->getPath(), '/') !== self::PATH) {
            return $handler->handle($request);
        }
        $tokenHash = Configure::read('Mcp.tokenHash');
        if (!is_string($tokenHash) || $tokenHash === '') {
            return $this->respond(404, Server::error(null, -32000, 'The MCP endpoint is not configured'));
        }
        if (!$this->isSameOrigin($request)) {
            return $this->respond(403, Server::error(null, -32600, 'Requests from web pages of other origins are not allowed'));
        }
        if ($request->getMethod() !== 'POST') {
            // Kein Stream vom Server, keine Session zum Beenden
            return $this->respond(405, null)->withHeader('Allow', 'POST');
        }
        $token = $this->token($request);
        if ($token === null || !hash_equals($tokenHash, hash('sha256', $token))) {
            Log::warning('MCP: request without a valid token from ' . ($request->getServerParams()['REMOTE_ADDR'] ?? '?'));

            return $this->respond(401, Server::error(null, -32001, 'A valid token in the header X-Ido-Token is required'));
        }
        $message = json_decode((string)$request->getBody(), true);
        if ($message === null) {
            return $this->respond(400, Server::error(null, -32700, 'Parse error, the body is no JSON'));
        }
        $body = (new Server(new Tools()))->handle($message);

        return $this->respond($body === null ? 202 : 200, $body);
    }

    private function token(ServerRequestInterface $request): ?string
    {
        $token = trim($request->getHeaderLine('X-Ido-Token'));
        if ($token !== '') {
            return $token;
        }
        if (preg_match('/^Bearer\s+(\S+)$/i', trim($request->getHeaderLine('Authorization')), $matches)) {
            return $matches[1];
        }

        return null;
    }

    /**
     * Ein Browser schickt einen Origin-Header. Eine fremde Seite darf den
     * Endpunkt nicht erreichen, ein Agent schickt keinen oder den eigenen.
     */
    private function isSameOrigin(ServerRequestInterface $request): bool
    {
        $origin = $request->getHeaderLine('Origin');
        if ($origin === '') {
            return true;
        }
        $host = parse_url($origin, PHP_URL_HOST);
        $port = parse_url($origin, PHP_URL_PORT);

        return is_string($host) && strcasecmp($host . ($port ? ':' . $port : ''), $request->getHeaderLine('Host')) === 0;
    }

    private function respond(int $status, ?array $body): ResponseInterface
    {
        $response = (new Response())->withStatus($status)->withHeader('Cache-Control', 'no-store');
        if ($body === null) {
            return $response;
        }

        return $response->withType('application/json')
            ->withStringBody((string)json_encode($body, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
    }
}
