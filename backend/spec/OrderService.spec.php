<?php

declare(strict_types=1);

namespace Tests;

use App\Enums\OrderStatus;
use App\Exceptions\Order\OrderServiceException;
use App\Models\Bill\BillDTO;
use App\Models\Bill\BillRepositoryInRDB;
use App\Models\Bill\BillService;
use App\Models\Client\Client;
use App\Models\Client\ClientRepositoryInRDB;
use App\Models\Client\ClientService;
use App\Models\Item\Item;
use App\Models\Order\Order;
use App\Models\Order\OrderRepositoryInRDB;
use App\Models\Order\OrderService;
use App\Models\OrderItem\OrderItem;
use App\Models\Table\Table;
use App\Utils\Env;
use App\Utils\PDOBuilder;

use function App\Utils\Tests\cleanTestDB;
use function Kahlan\describe;
use function Kahlan\expect;
use function Kahlan\it;

describe('OrderService', function () {
  $this->service = null;
  $this->pdo = null;

  beforeAll(function () {
    Env::initEnv(__DIR__ . '/../config/.env');
    $instance = new PDOBuilder();
    $this->pdo = PDOBuilder::build(true);

    cleanTestDB($this->pdo, <<<'SQL'
      INSERT INTO TableOrder(table_id, client_id, status) VALUES (1, 1, 'open');
    SQL);

    allow($instance)->toReceive('::build')->andReturn($this->pdo);

    $this->service = new OrderService(
      new OrderRepositoryInRDB($this->pdo),
      new ClientService(new ClientRepositoryInRDB($this->pdo)),
      new BillService(new BillRepositoryInRDB(($this->pdo)))
    );
  });

  it('should create the service correctly', function () {
    expect($this->service)->toBeAnInstanceOf('App\\Models\\Order\\OrderService');
  });

  it('should fulfill the order correctly', function () {
    $result = $this->service->fulfill(
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

  it('should throw and exception when fulfill order goes wrong', function () {
    expect(function () {
      $this->service->fulfill(
        new BillDTO(
          1,
          1,
          99,
          10,
          0
        )
      );
    })->toThrow(new OrderServiceException('Erro ao criar conta', 500));
  });

  it('should throw an exception whe order doesnt exist', function () {
    cleanTestDB($this->pdo);

    $items = [
      new OrderItem(0, 1, new Item(1)),
      new OrderItem(0, 2, new Item(2))
    ];

    expect(function () use ($items) {
      $order = new Order(1, OrderStatus::open, new Table(1), new Client(1), $items);

      $this->service->addItems($order);
    })->toThrow(new OrderServiceException('Pedido não encontrado', 404));
  });

  it('should return null given that the order does not exist', function () {
    cleanTestDB($this->pdo);

    expect($this->service->getOrder(1))->toBe(null);
  });
});
