<?php

declare(strict_types=1);

namespace App\Middlewares;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;

class ContentTypeMiddleware
{
  public function __invoke(Request $request, RequestHandler $handler): Response
  {
    return $handler->handle($request)->withHeader('Content-type', 'application/json; charset=utf-8');
  }
}
