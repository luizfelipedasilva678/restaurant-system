<?php

declare(strict_types=1);

namespace Tests;

use App\Models\Item\ItemRepositoryInRDB;
use App\Utils\Env;
use App\Utils\PDOBuilder;

use function App\Utils\Tests\cleanTestDB;
use function Kahlan\beforeAll;
use function Kahlan\describe;
use function Kahlan\expect;
use function Kahlan\it;

describe('OrderRepositoryInRDB', function () {
  $this->repository = null;
  $this->pdo = null;

  beforeAll(function () {
    Env::initEnv(__DIR__ . '/../config/.env');
    $this->pdo = PDOBuilder::build(true);
    cleanTestDB($this->pdo);
    $this->repository = new ItemRepositoryInRDB($this->pdo);
  });

  it('should create the repository correctly', function () {
    expect($this->repository)->toBeAnInstanceOf('App\\Models\\Item\\ItemRepositoryInRDB');
  });

  it('should return all items correctly', function () {
    $result = $this->repository->getItems(10, 0);

    expect(count($result['data']))->toBe(8);
    expect($result['count'])->toBe(8);
  });
});
