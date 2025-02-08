<?php

namespace Tests;

use App\Enums\EmployeeType;
use App\Models\Employee\Employee;
use App\Models\Employee\EmployeeRepositoryInRDB;
use App\Models\Employee\EmployeeService;
use App\Utils\Env;
use App\Utils\PDOBuilder;

use function App\Utils\Tests\cleanTestDB;
use function Kahlan\beforeAll;
use function Kahlan\describe;
use function Kahlan\expect;
use function Kahlan\it;

describe('EmployeeService', function () {
  $this->service = null;
  $this->pdo = null;

  beforeAll(function () {
    Env::initEnv(__DIR__ . '/../config/.env');
    $this->pdo = PDOBuilder::build(true);
    cleanTestDB($this->pdo);
    $this->service = new EmployeeService(new EmployeeRepositoryInRDB($this->pdo));
  });

  it('should create the service correctly', function () {
    expect($this->service)->toBeAnInstanceOf('App\Models\Employee\EmployeeService');
  });

  it('should get the employees correctly', function () {
    expect($this->service->getEmployees(1, 1))->toEqual(
      ['data' => [
        Employee::build(1, 'Rozella Cejka', '', 'user1', EmployeeType::attendant),
      ], 'count' => 4]
    );

    expect($this->service->getEmployees(1, 2))->toEqual(
      ['data' => [
        Employee::build(1, 'Rozella Cejka', '', 'user1', EmployeeType::attendant),
        Employee::build(2, 'Tamara Reidshaw', '', 'user2', EmployeeType::attendant),
      ], 'count' => 4]
    );

    expect($this->service->getEmployees(1, 4))->toEqual(
      ['data' => [
        Employee::build(1, 'Rozella Cejka', '', 'user1', EmployeeType::attendant),
        Employee::build(2, 'Tamara Reidshaw', '', 'user2', EmployeeType::attendant),
        Employee::build(3, 'Latrena Laughren', '', 'user3', EmployeeType::attendant),
        Employee::build(4, 'Luiz Henrique', '', 'user4', EmployeeType::manager),
      ],
        'count' => 4
      ]
    );
  });
});
