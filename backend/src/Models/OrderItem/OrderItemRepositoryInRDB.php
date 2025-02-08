<?php

namespace App\Models\OrderItem;

use App\Contracts\OrderItem\OrderItemRepository;
use App\Enums\HttpCodes;
use App\Exceptions\OrderItem\OrderItemRepositoryException;
use App\Models\Category\Category;
use App\Models\Item\Item;
use PDO;
use PDOException;

class OrderItemRepositoryInRDB implements OrderItemRepository
{
  private PDO $pdo;

  public function __construct(PDO $pdo)
  {
    $this->pdo = $pdo;
  }

  /**
   * @return OrderItem[]
   */
  public function getItems(int $tableOrderId): array
  {
    try {
      $sql = <<<SQL
          SELECT
            oi.id,
            oi.quantity,
            i.id as item_id,
            i.code as item_code,
            i.description as item_description,
            i.price as item_price,
            c.name as category_name,
            c.id as category_id
          FROM OrderItem oi
          INNER JOIN Item i ON oi.item_id = i.id
          INNER JOIN Category c ON i.category_id = c.id
          WHERE oi.table_order_id = :table_order_id;
        SQL;

      $stmt = $this->pdo->prepare($sql);
      $stmt->setFetchMode(\PDO::FETCH_ASSOC);
      $stmt->bindValue(':table_order_id', $tableOrderId);
      $stmt->execute();

      $result = $stmt->fetchAll();

      if (!$result) {
        return [];
      }

      $orderItems = [];

      /**
       * @var array{
       *   id: int,
       *   quantity: int,
       *   item_id: int,
       *   item_code: string,
       *   item_description: string,
       *   item_price: float,
       *   category_name: string,
       *   category_id: int
       * } $item
       */
      foreach ($result as $item) {
        $orderItems[] = new OrderItem(
          $item['id'],
          $item['quantity'],
          new Item(
            $item['item_id'],
            $item['item_code'],
            new Category($item['category_id'], $item['category_name']),
            $item['item_description'],
            $item['item_price']
          )
        );
      }

      return $orderItems;
    } catch (PDOException $exception) {
      return [];
    }
  }

  public function create(OrderItem $orderItem, int $tableOrderId): bool
  {
    try {
      $sql = <<<SQL
        INSERT INTO OrderItem (quantity, item_id, table_order_id)
        VALUES (:quantity, :item_id, :table_order_id);
      SQL;

      $stmt = $this->pdo->prepare($sql);
      $stmt->bindValue(':quantity', $orderItem->getQuantity());
      $stmt->bindValue(':item_id', $orderItem->getItem()->getId());
      $stmt->bindValue(':table_order_id', $tableOrderId);
      $stmt->execute();

      return true;
    } catch (PDOException $exception) {
      throw new OrderItemRepositoryException('Erro adicionar item ao pedido', HttpCodes::HTTP_SERVER_ERROR->value);
    }
  }
}
