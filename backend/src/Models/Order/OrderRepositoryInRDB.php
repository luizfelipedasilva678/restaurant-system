<?php

namespace App\Models\Order;

use App\Contracts\Order\OrderRepository;
use App\Enums\HttpCodes;
use App\Enums\OrderStatus;
use App\Exceptions\Order\OrderRepositoryException;
use App\Exceptions\OrderItem\OrderItemRepositoryException;
use App\Models\Client\Client;
use App\Models\OrderItem\OrderItemRepositoryInRDB;
use App\Models\Table\Table;
use PDO;
use PDOException;

class OrderRepositoryInRDB implements OrderRepository
{
  private PDO $pdo;
  private OrderItemRepositoryInRDB $orderItemRepository;

  public function __construct(PDO $pdo)
  {
    $this->pdo = $pdo;
    $this->orderItemRepository = new OrderItemRepositoryInRDB($pdo);
  }

  public function getOrder(int $orderId): ?Order
  {
    try {
      $sql  = <<<SQL
        SELECT
          torder.id,
          torder.status,
          rt.number as table_number,
          rt.id as table_id,
          c.name as client_name,
          c.id as client_id
        FROM TableOrder torder
        INNER JOIN RestaurantTable rt ON torder.table_id = rt.id
        INNER JOIN Client c ON torder.client_id = c.id
        WHERE torder.id = :orderId;
      SQL;

      $stmt = $this->pdo->prepare($sql);
      $stmt->setFetchMode(\PDO::FETCH_ASSOC);
      $stmt->bindValue(':orderId', $orderId, \PDO::PARAM_INT);
      $stmt->execute();

      /**
       * @var array{
       *   id: int,
       *   status: string,
       *   table_number: int,
       *   table_id: int,
       *   client_name: string,
       *   client_id: int
       * } | false
       */
      $order = $stmt->fetch();

      if (!$order) {
        return null;
      }

      return new Order(
        $order['id'],
        OrderStatus::from($order['status']),
        new Table($order['table_id'], $order['table_number']),
        new Client($order['client_id'], $order['client_name']),
        $this->orderItemRepository->getItems($order['id'])
      );
    } catch (PDOException $exception) {
      throw new OrderRepositoryException('Erro ao obter pedido', HttpCodes::HTTP_SERVER_ERROR->value);
    }
  }

  public function addItems(Order $order): bool
  {
    try {
      foreach ($order->getItems() as $item) {
        $this->orderItemRepository->create($item, $order->getId());
      }

      return true;
    } catch (OrderItemRepositoryException $exception) {
      throw new OrderRepositoryException('Erro ao adicionar items ao pedido', HttpCodes::HTTP_SERVER_ERROR->value);
    }
  }

  /**
   * @return Order[]
   */
  public function getOrders(): array
  {
    try {
      $sql = <<<SQL
        SELECT id from TableOrder
        WHERE status = "open";
      SQL;

      $stmt = $this->pdo->prepare($sql);
      $stmt->setFetchMode(\PDO::FETCH_ASSOC);
      $stmt->execute();

      $result = $stmt->fetchAll();

      /**
       * @var Order[]
       */
      $orders = [];

      /**
       * @var array{
       *   id: int
       * } $order
       */
      foreach ($result as $order) {
        $fetchedOrder = $this->getOrder($order['id']) ;

        if (!$fetchedOrder) {
          continue;
        }

        $orders[] = $fetchedOrder;
      }

      return $orders;
    } catch (PDOException $exception) {
      return [];
    }
  }

  public function update(Order $order): bool
  {
    try {
      $sql = <<<SQL
        UPDATE TableOrder SET status = :status WHERE id = :id;
      SQL;

      $stmt = $this->pdo->prepare($sql);

      $stmt->bindValue(':status', $order->getStatus()->name);
      $stmt->bindValue(':id', $order->getId());

      $stmt->execute();

      if ($stmt->rowCount() <= 0) {
        return false;
      }

      return true;
    } catch (PDOException $exception) {
      throw new OrderRepositoryException('Erro ao atualizar pedido', HttpCodes::HTTP_SERVER_ERROR->value);
    }
  }

  public function create(Order $order): bool
  {
    try {
      $sql = <<<SQL
        INSERT INTO TableOrder (table_id, client_id, status)
        VALUES (:table_id, :client_id, :status);
      SQL;

      $stmt = $this->pdo->prepare($sql);
      $stmt->bindValue(':table_id', $order->getTable()->getId());
      $stmt->bindValue(':client_id', $order->getClient()->getId());
      $stmt->bindValue(':status', $order->getStatus()->name);
      $stmt->execute();

      if ($this->pdo->lastInsertId()) {
        $order->setId(intval($this->pdo->lastInsertId()));
      }

      return true;
    } catch (PDOException $exception) {
      throw new OrderRepositoryException('Erro ao criar pedido', HttpCodes::HTTP_SERVER_ERROR->value);
    }
  }
}
