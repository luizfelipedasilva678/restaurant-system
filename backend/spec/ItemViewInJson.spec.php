<?php

declare(strict_types=1);

namespace Tests;

use App\Utils\Env;
use App\Utils\PDOBuilder;
use App\Views\V1\ItemViewInJson;
use Slim\Psr7\Response;

use function App\Utils\Tests\cleanTestDB;
use function App\Utils\Tests\createRequest;
use function Kahlan\describe;
use function Kahlan\expect;

describe('ItemView', function () {
  $this->view = null;
  $this->pdo = null;
  $this->path = '/api/v1/items';
  $this->method = 'GET';

  beforeAll(function () {
    Env::initEnv(__DIR__ . '/../config/.env');
    $instance = new PDOBuilder();
    $this->pdo = PDOBuilder::build(true);
    cleanTestDB($this->pdo);
    allow($instance)->toReceive('::build')->andReturn($this->pdo);
    $this->view = new ItemViewInJson();
  });

  it('should create the view correctly', function () {
    expect($this->view)->toBeAnInstanceOf('App\\Views\\V1\\ItemViewInJson');
  });

  it('should return the items correctly', function () {
    $this->view->handleGetItems(
      createRequest($this->method, $this->path, 'page=1&perPage=10'),
      $response = new Response(),
      []
    );

    $response->getBody()->rewind();

    expect(str_contains($response->getBody()->getContents(), '8'))->toBe(true);
  });
});
