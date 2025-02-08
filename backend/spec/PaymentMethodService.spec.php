<?php

namespace Tests;

use App\Exceptions\PaymentMethod\PaymentMethodServiceException;
use App\Models\PaymentMethod\PaymentMethod;
use App\Models\PaymentMethod\PaymentMethodRepositoryInRDB;
use App\Models\PaymentMethod\PaymentMethodService;
use App\Utils\Env;
use App\Utils\PDOBuilder;

use function App\Utils\Tests\cleanTestDB;
use function Kahlan\beforeAll;
use function Kahlan\describe;
use function Kahlan\expect;
use function Kahlan\it;

describe('PaymentMethodService', function () {
  $this->model = null;
  $this->pdo = null;

  beforeAll(function () {
    Env::initEnv(__DIR__ . '/../config/.env');
    $this->pdo = PDOBuilder::build(true);
    cleanTestDB($this->pdo);
    $this->service = new PaymentMethodService(new PaymentMethodRepositoryInRDB($this->pdo));
  });

  it('should create the service correctly', function () {
    expect($this->service)->toBeAnInstanceOf('App\Models\PaymentMethod\PaymentMethodService');
  });

  it('should get the payments methods correctly', function () {
    $results = $this->service->getPaymentsMethods();

    expect($results)->toEqual([
      new PaymentMethod(1, 'Pix'),
      new PaymentMethod(2, 'Dinheiro'),
      new PaymentMethod(3, 'Cartão de crédito'),
      new PaymentMethod(4, 'Cartão de débito'),
    ]);
  });

  it('should throw and exception when something goes wrong', function () {
    allow($this->service)->toReceive('getPaymentsMethods')->andRun(function () {
      throw new PaymentMethodServiceException('Erro ao buscar metodos de pagamento', 500);
    });

    expect(function () {
      $this->service->getPaymentsMethods();
    })->toThrow(new PaymentMethodServiceException('Erro ao buscar metodos de pagamento', 500));
  });
});
