<?php

declare(strict_types=1);

namespace App\Utils\Tests;

use Slim\Psr7\Factory\StreamFactory;
use Slim\Psr7\Headers;
use Slim\Psr7\Request;
use Slim\Psr7\Request as SlimRequest;
use Slim\Psr7\Uri;

/**
 * @param array<string> $headers
 * @param array<string> $cookies
 * @param array<string> $serverParams
 */
function createRequest(
  string $method,
  string $path,
  string $query = '',
  array $headers = ['HTTP_ACCEPT' => 'application/json'],
  array $cookies = [],
  array $serverParams = []
): Request {
  $uri = new Uri('', '', 80, $path, $query);

  /**
   * @var resource
   */
  $handle = fopen('php://temp', 'w+');

  $stream = (new StreamFactory())->createStreamFromResource($handle);

  $h = new Headers();

  /**
   * @var string $name
   * @var string $value
   */
  foreach ($headers as $name => $value) {
    $h->addHeader($name, $value);
  }

  return new SlimRequest($method, $uri, $h, $cookies, $serverParams, $stream);
}
