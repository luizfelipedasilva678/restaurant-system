<?php

declare(strict_types=1);

namespace Tests;

use App\Enums\EmployeeType;
use App\Exceptions\Employee\EmployeeRepositoryException;
use App\Models\Employee\Employee;
use App\Models\Employee\EmployeeRepositoryInRDB;
use App\Utils\Env;
use App\Utils\PDOBuilder;

use function App\Utils\Tests\cleanTestDB;
use function Kahlan\beforeAll;
use function Kahlan\describe;
use function Kahlan\expect;

describe('EmployeeRepositoryInRDB', function () {
  $this->repository = null;
  $this->pdo = null;
  $this->mockPassword = '7564657a09d204f3aff4dede432e7a6a5ded0ff7144b28a23c746727e27bc35da9f3ca22f06bdfb2a043b602f13ccd996448243072897da15da08f2461618705';

  beforeAll(function () {
    Env::initEnv(__DIR__ . '/../config/.env');
    $this->pdo = PDOBuilder::build(true);
    cleanTestDB($this->pdo);
    $this->repository = new EmployeeRepositoryInRDB($this->pdo);
  });

  it('should get the employees correctly', function () {
    expect($this->repository->getAll(1, 0))->toEqual([
      'data' => [
        Employee::build(1, 'Rozella Cejka', '', 'user1', EmployeeType::attendant),
      ],
      'count' => 4
    ]);

    expect($this->repository->getAll(2, 0))->toEqual([
      'data' => [
        Employee::build(1, 'Rozella Cejka', '', 'user1', EmployeeType::attendant),
        Employee::build(2, 'Tamara Reidshaw', '', 'user2', EmployeeType::attendant),
      ],
      'count' => 4
    ]);

    expect($this->repository->getAll(4, 0))->toEqual([
      'data' => [
        Employee::build(1, 'Rozella Cejka', '', 'user1', EmployeeType::attendant),
        Employee::build(2, 'Tamara Reidshaw', '', 'user2', EmployeeType::attendant),
        Employee::build(3, 'Latrena Laughren', '', 'user3', EmployeeType::attendant),
        Employee::build(4, 'Luiz Henrique', '', 'user4', EmployeeType::manager)
      ],
      'count' => 4
    ]);
  });

  it('should get the employee by login and password correctly', function () {
    expect($this->repository->getByLoginAndPassword('user1', $this->mockPassword))->toEqual(
      Employee::build(1, 'Rozella Cejka', '', 'user1', EmployeeType::attendant)
    );
  });

  it('should throw and exception when no user is found', function () {
    expect(function () {
      $this->repository->getByLoginAndPassword('user1', '123');
    })->toThrow(new EmployeeRepositoryException('Usuário ou senha inválidos', 400));
  });
});
