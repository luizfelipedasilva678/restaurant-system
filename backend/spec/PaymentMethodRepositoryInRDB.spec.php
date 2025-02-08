<?php

namespace Tests;

use App\Exceptions\PaymentMethod\PaymentMethodRepositoryException;
use App\Models\PaymentMethod\PaymentMethod;
use App\Models\PaymentMethod\PaymentMethodRepositoryInRDB;
use App\Utils\Env;
use App\Utils\PDOBuilder;

use function Kahlan\describe;
use function Kahlan\expect;
use function Kahlan\it;
use function App\Utils\Tests\cleanTestDB;
use function Kahlan\allow;
use function Kahlan\beforeAll;

describe('PaymentMethodRepositoryInRDB', function () {
  $this->repository = null;
  $this->pdo = null;

  beforeAll(function () {
    Env::initEnv(__DIR__ . '/../config/.env');
    $this->pdo = PDOBuilder::build(true);
    cleanTestDB($this->pdo);
    $this->repository = new PaymentMethodRepositoryInRDB($this->pdo);
  });

  it('should create the repository correctly', function () {
    expect($this->repository)->toBeAnInstanceOf('App\\Models\\PaymentMethod\\PaymentMethodRepositoryInRDB');
  });

  it('should get the payments methods correctly', function () {
    $results = $this->repository->getAll();

    expect($results)->toEqual([
      new PaymentMethod(1, 'Pix'),
      new PaymentMethod(2, 'Dinheiro'),
      new PaymentMethod(3, 'Cartão de crédito'),
      new PaymentMethod(4, 'Cartão de débito'),
    ]);
  });

  it('should throw and exception when something goes wrong', function () {
    allow($this->repository)->toReceive('getAll')->andRun(function () {
      throw new PaymentMethodRepositoryException('Erro ao buscar metodos de pagamento', 500);
    });

    expect(function () {
      $this->repository->getAll();
    })->toThrow(new PaymentMethodRepositoryException('Erro ao buscar metodos de pagamento', 500));
  });
});
