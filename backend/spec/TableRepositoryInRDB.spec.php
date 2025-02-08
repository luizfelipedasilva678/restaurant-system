<?php

declare(strict_types=1);

namespace Tests;

use App\Models\Table\Table;
use App\Models\Table\TableRepositoryInRDB;
use App\Utils\Env;
use App\Utils\PDOBuilder;

use function App\Utils\Tests\cleanTestDB;
use function Kahlan\beforeAll;
use function Kahlan\describe;

describe('TableRepositoryInRDB', function () {
  $this->repository = null;
  $this->pdo = null;

  beforeAll(function () {
    Env::initEnv(__DIR__ . '/../config/.env');
    $this->pdo = PDOBuilder::build(true);
    cleanTestDB($this->pdo);
    $this->repository = new TableRepositoryInRDB($this->pdo);
  });

  it('should get the tables correctly', function () {
    expect($this->repository->getAll())->toEqual([
      Table::build(1, 1),
      Table::build(2, 2),
      Table::build(3, 3),
      Table::build(4, 4),
      Table::build(5, 5),
      Table::build(6, 6),
      Table::build(7, 7),
      Table::build(8, 8),
      Table::build(9, 9),
      Table::build(10, 10),
    ]);
  });
});
