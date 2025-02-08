<?php

namespace Tests;

use App\Views\V1\IndexViewInJson;
use Slim\Psr7\Response;

use function App\Utils\Tests\createRequest;
use function Kahlan\describe;
use function Kahlan\expect;

describe('IndexView', function () {
  $this->view = null;
  $this->pdo = null;
  $this->path = '/';
  $this->method = 'GET';

  beforeAll(function () {
    $this->view = new IndexViewInJson();
  });

  it('should return the routes correctly', function () {
    $this->view->handleIndex(
      createRequest($this->method, $this->path),
      $response = new Response(),
      []
    );

    $response->getBody()->rewind();
    $responseBody = json_decode($response->getBody()->getContents());

    expect(str_contains($responseBody->employees, '/api/v1/employees'))->toBe(true);
    expect(str_contains($responseBody->tables, '/api/v1/tables'))->toBe(true);
    expect(str_contains($responseBody->reservations, '/api/v1/reservations'))->toBe(true);
  });
});
