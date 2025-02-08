<?php

namespace Tests;

use App\Exceptions\Bill\BillRepositoryException;
use App\Models\Bill\Bill;
use App\Models\Bill\BillRepositoryInRDB;
use App\Models\Employee\Employee;
use App\Models\Order\Order;
use App\Models\PaymentMethod\PaymentMethod;
use App\Utils\Env;
use App\Utils\PDOBuilder;
use DateTime;

use function Kahlan\describe;
use function Kahlan\expect;
use function Kahlan\it;
use function App\Utils\Tests\cleanTestDB;
use function Kahlan\beforeAll;

describe('BillRepositoryInRDB', function () {
  $this->repository = null;
  $this->pdo = null;

  beforeAll(function () {
    Env::initEnv(__DIR__ . '/../config/.env');
    $this->pdo = PDOBuilder::build(true);
    cleanTestDB($this->pdo, <<<'SQL'
      INSERT INTO TableOrder(table_id, client_id, status) VALUES (1, 1, 'open');
    SQL);
    $this->repository = new BillRepositoryInRDB($this->pdo);
  });

  it('should create the repository correctly', function () {
    expect($this->repository)->toBeAnInstanceOf('App\\Models\\Bill\\BillRepositoryInRDB');
  });

  it('should create a bill correctly', function () {
    $result = $this->repository->create(
      new Bill(
        10,
        new Employee(1),
        new PaymentMethod(1),
        new Order(1),
        0
      )
    );

    expect($result)->toBeTruthy();
  });

  it('should throw and exception when something goes wrong', function () {
    expect(function () {
      $this->repository->create(
        new Bill(
          10,
          new Employee(1),
          new PaymentMethod(1),
          new Order(2),
          0
        )
      );
    })->toThrow(new BillRepositoryException('Erro ao criar conta', 500));
  });

  it('should return the data for sales for category correctly', function () {
    $startDate = new DateTime();
    $endDate = new DateTime();
    $result = $this->repository->getSalesByCategory($startDate->format('Y-m-d'), $endDate->format('Y-m-d'));

    expect(count($result))->toBe(0);
  });

  it('should return the data for sales for employee correctly', function () {
    $startDate = new DateTime();
    $endDate = new DateTime();
    $result = $this->repository->getSalesByEmployee($startDate->format('Y-m-d'), $endDate->format('Y-m-d'));

    expect(count($result))->toBe(1);
  });

  it('should return the data for sales for day correctly', function () {
    $startDate = new DateTime();
    $endDate = new DateTime();
    $result = $this->repository->getSalesByDay($startDate->format('Y-m-d'), $endDate->format('Y-m-d'));

    expect(count($result))->toBe(1);
  });

  it('should return the data for sales for payment method correctly', function () {
    $startDate = new DateTime();
    $endDate = new DateTime();
    $result = $this->repository->getSalesByPaymentMethod($startDate->format('Y-m-d'), $endDate->format('Y-m-d'));

    expect(count($result))->toBe(1);
  });
});
