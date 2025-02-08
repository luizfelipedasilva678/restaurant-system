<?php

declare(strict_types=1);

namespace Tests;

use App\Models\Item\ItemRepositoryInRDB;
use App\Models\Item\ItemService;
use App\Utils\Env;
use App\Utils\PDOBuilder;

use function App\Utils\Tests\cleanTestDB;
use function Kahlan\describe;
use function Kahlan\expect;
use function Kahlan\it;

describe('ItemService', function () {
  $this->service = null;
  $this->pdo = null;

  beforeAll(function () {
    Env::initEnv(__DIR__ . '/../config/.env');
    $instance = new PDOBuilder();
    $this->pdo = PDOBuilder::build(true);
    cleanTestDB($this->pdo);
    allow($instance)->toReceive('::build')->andReturn($this->pdo);
    $this->service = new ItemService(
      new ItemRepositoryInRDB($this->pdo)
    );
  });

  it('should create the service correctly', function () {
    expect($this->service)->toBeAnInstanceOf('App\Models\Item\ItemService');
  });

  it('should return the items correctly', function () {
    $result = $this->service->getItems(1, 10);

    expect(count($result['data']))->toBe(8);
    expect($result['count'])->toBe(8);
  });
});
