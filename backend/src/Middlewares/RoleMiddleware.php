<?php

declare(strict_types=1);

namespace App\Middlewares;

use App\Utils\Session\SessionUtils;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response as SlimResponse;

class RoleMiddleware
{
  public function __invoke(Request $request, RequestHandler $handler): Response
  {
    SessionUtils::startSession();

    $uri = $request->getUri();
    $path = $uri->getPath();

    $shouldValidate = preg_match('/fulfill|reports/', $path) && $request->getMethod() !== 'OPTIONS';

    if ($shouldValidate && (!isset($_SESSION['userType']) || $_SESSION['userType'] !== 'manager')) {
      $response = new SlimResponse();

      $response->getBody()->write(json_encode(['message' => 'forbidden']) ?: 'forbidden');

      return $response->withStatus(403);
    }

    return $handler->handle($request);
  }
}
