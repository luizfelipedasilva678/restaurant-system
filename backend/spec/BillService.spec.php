<?php

namespace Tests;

use App\Exceptions\Bill\BillServiceException;
use App\Models\Bill\BillDTO;
use App\Models\Bill\BillRepositoryInRDB;
use App\Models\Bill\BillService;
use App\Utils\Env;
use App\Utils\PDOBuilder;
use DateTime;

use function App\Utils\Tests\cleanTestDB;
use function Kahlan\beforeAll;
use function Kahlan\describe;
use function Kahlan\expect;
use function Kahlan\it;

describe('BillService', function () {
  $this->model = null;
  $this->pdo = null;

  beforeAll(function () {
    Env::initEnv(__DIR__ . '/../config/.env');
    $this->pdo = PDOBuilder::build(true);
    cleanTestDB($this->pdo, <<<'SQL'
      INSERT INTO TableOrder(table_id, client_id, status) VALUES (1, 1, 'open');
    SQL);
    $this->service = new BillService(new BillRepositoryInRDB($this->pdo));
  });

  it('should create the service correctly', function () {
    expect($this->service)->toBeAnInstanceOf('App\Models\Bill\BillService');
  });

  it('should create a bill correctly', function () {
    $result = $this->service->create(
      new BillDTO(
        1,
        1,
        1,
        10,
        0
      )
    );

    expect($result)->toBeTruthy();
  });

  it('should throw and exception when something goes wrong', function () {
    expect(function () {
      $this->service->create(
        new BillDTO(
          1,
          1,
          2,
          10,
          0
        )
      );
    })->toThrow(new BillServiceException('Erro ao criar conta', 500));
  });

  it('should return the data for sales for category correctly', function () {
    $startDate = new DateTime();
    $endDate = new DateTime();
    $result = $this->service->getSalesByCategory($startDate->format('Y-m-d'), $endDate->format('Y-m-d'));

    expect(count($result))->toBe(0);
  });

  it('should return the data for sales for employee correctly', function () {
    $startDate = new DateTime();
    $endDate = new DateTime();
    $result = $this->service->getSalesByEmployee($startDate->format('Y-m-d'), $endDate->format('Y-m-d'));

    expect(count($result))->toBe(1);
  });

  it('should return the data for sales for day correctly', function () {
    $startDate = new DateTime();
    $endDate = new DateTime();
    $result = $this->service->getSalesByDay($startDate->format('Y-m-d'), $endDate->format('Y-m-d'));

    expect(count($result))->toBe(1);
  });

  it('should return the data for sales for payment method correctly', function () {
    $startDate = new DateTime();
    $endDate = new DateTime();
    $result = $this->service->getSalesByPaymentMethod($startDate->format('Y-m-d'), $endDate->format('Y-m-d'));

    expect(count($result))->toBe(1);
  });
});
