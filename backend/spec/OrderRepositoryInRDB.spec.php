<?php

declare(strict_types=1);

namespace Tests;

use App\Enums\OrderStatus;
use App\Exceptions\Order\OrderRepositoryException;
use App\Models\Client\Client;
use App\Models\Item\Item;
use App\Models\Order\Order;
use App\Models\Order\OrderRepositoryInRDB;
use App\Models\OrderItem\OrderItem;
use App\Models\Table\Table;
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

    cleanTestDB($this->pdo, <<<'SQL'
      INSERT INTO TableOrder(table_id, client_id, status) VALUES (1, 1, 'open');
    SQL);

    $this->repository = new OrderRepositoryInRDB($this->pdo);
  });

  it('should create the repository correctly', function () {
    expect($this->repository)->toBeAnInstanceOf('App\\Models\\Order\\OrderRepositoryInRDB');
  });

  it('should create a new order correctly', function () {
    $order = new Order(
      0,
      OrderStatus::open,
      new Table(1),
      new Client(1)
    );

    $this->repository->create($order);

    expect($order->getId())->toBeGreaterThan(0);
  });

  it('should add items to the order correctly', function () {
    $order = new Order(1, OrderStatus::open, new Table(1), new Client(1));
    $items = [
      new OrderItem(0, 1, new Item(1)),
      new OrderItem(0, 2, new Item(2))
    ];

    $order->setItems($items);
    $result = $this->repository->addItems($order);

    expect($result)->toBeTruthy();
  });

  it('should get the order correctly', function () {
    $order = $this->repository->getOrder(1);

    expect($order->getId())->toBe(1);
    expect(count($order->getItems()))->toBe(2);
  });

  it('should update an order correctly', function () {
    $result = $this->repository->update(new Order(1, OrderStatus::completed));

    expect($result)->toBeTruthy();
  });

  it('should throw and exception when update goes wrong', function () {
    allow($this->repository)->toReceive('update')->andRun(function () {
      throw new OrderRepositoryException('Erro ao atualizar pedido', 500);
    });

    expect(function () {
      $this->repository->update(
        new Order(99, OrderStatus::completed)
      );
    })->toThrow(new OrderRepositoryException('Erro ao atualizar pedido', 500));
  });

  it('should return all orders correctly', function () {
    $orders = $this->repository->getOrders();

    expect(count($orders))->toBe(1);
  });
});
