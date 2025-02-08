<?php

namespace Tests;

use App\Enums\EmployeeType;
use App\Exceptions\Auth\AuthServiceException;
use App\Models\Auth\AuthService;
use App\Models\Employee\Employee;
use App\Models\Employee\EmployeeRepositoryInRDB;
use App\Utils\Env;
use App\Utils\PDOBuilder;

use function App\Utils\Tests\cleanTestDB;
use function Kahlan\describe;
use function Kahlan\expect;

describe('AuthService', function () {
  $this->service = null;
  $this->repository = null;
  $this->pdo = null;

  beforeAll(function () {
    Env::initEnv(__DIR__ . '/../config/.env');

    $this->pdo = PDOBuilder::build(true);

    cleanTestDB($this->pdo);

    $this->repository = new EmployeeRepositoryInRDB($this->pdo);
    $this->service = new AuthService($this->repository);
  });

  it('should return the Employee object when the credentials are correctly', function () {
    expect($this->service->verifyEmployeeCredentials('user1', '123'))->toEqual(
      Employee::build(1, 'Rozella Cejka', '', 'user1', EmployeeType::attendant)
    );
  });

  it('should throw an AuthServiceException when the employee credentials are incorrectly', function () {
    expect(function () {
      $this->service->verifyEmployeeCredentials('user1', '1234');
    })->toThrow(new AuthServiceException('Usuário ou senha inválidos', 400));
  });
});
