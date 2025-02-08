<?php

declare(strict_types=1);

namespace App\Middlewares;

use App\Utils\Session\SessionUtils;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response as SlimResponse;

class AuthMiddleware
{
  public function __invoke(Request $request, RequestHandler $handler): Response
  {
    $uri = $request->getUri();
    $path = $uri->getPath();

    $shouldSkipValidation = preg_match('/login/', $path) || $request->getMethod() === 'OPTIONS';

    if ($shouldSkipValidation) {
      return $handler->handle($request);
    }

    SessionUtils::startSession();

    if (empty($_SESSION)) {
      $response = new SlimResponse();

      $response->getBody()->write(json_encode(['message' => 'unauthorized']) ?: 'unauthorized');

      return $response->withStatus(401);
    }

    return $handler->handle($request);
  }
}
