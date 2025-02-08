<?php

namespace App\Models\Order;

use App\Enums\OrderStatus;
use App\Models\Client\Client;
use App\Models\Item\Item;
use App\Models\OrderItem\OrderItem;
use App\Models\Table\Table;

class OrderMapper
{
  public static function toDTO(Order $order): OrderDTO
  {
    /**
     * @var array{id: int, itemId: int, quantity: int, price: float, description: string, category: string} $items
     */
    $items = [];

    foreach ($order->getItems() as $item) {
      /**
       * @var array{id: int, itemId: int, quantity: int, price: float, description: string, category: string} $items
       */
      $items[] = [
        'id' => $item->getId(),
        'itemId' => $item->getItem()->getId(),
        'quantity' => $item->getQuantity(),
        'price' => $item->getItem()->getPrice(),
        'description' => $item->getItem()->getDescription(),
        'category' => $item->getItem()->getCategory()->getName()
      ];
    }

    return new OrderDTO(
      $order->getId(),
      $order->getClient()->getId(),
      $order->getClient()->getName(),
      $order->getTable()->getId(),
      $order->getTable()->getNumber(),
      $order->getStatus()->name,
      $items
    );
  }

  public static function toEntity(OrderDTO $orderDTO): Order
  {
    /** @var OrderItem[] $items */
    $items = [];

    /**
     * @var array{id: int, itemId: int, quantity: int} $item
     */
    foreach ($orderDTO->items as $item) {
      $items[] = new OrderItem(
        $item['id'],
        $item['quantity'],
        new Item($item['itemId'])
      );
    }

    return new Order(
      $orderDTO->id,
      OrderStatus::from($orderDTO->status),
      new Table($orderDTO->tableId),
      new Client($orderDTO->clientId, $orderDTO->clientName),
      $items
    );
  }

  /**
   * @param Order[] $entities
   *
   * @return OrderDTO[]
   */
  public static function toDTOArray(array $entities): array
  {
    return array_map([self::class, 'toDTO'], $entities);
  }
}
