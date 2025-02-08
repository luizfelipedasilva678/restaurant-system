<?php

namespace App\Models\Order;

use JsonSerializable;

class OrderDTO implements JsonSerializable
{
  public int $id;
  public int $clientId;
  public string $clientName;
  public int $tableId;
  public int $tableNumber;
  public string $status;
  /**
   * @var array{id: int, itemId: int, quantity: int, price: float, description: string, category: string} $items;
   */
  public array $items;

  /**
   * @param array{id: int, itemId: int, quantity: int, price: float, description: string, category: string} $items
   */
  public function __construct(
    int $id = 0,
    int $clientId = 0,
    string $clientName = '',
    int $tableId = 0,
    int $tableNumber = 0,
    string $status = 'open',
    array $items = []
  ) {
    $this->id = $id;
    $this->clientId = $clientId;
    $this->tableId = $tableId;
    $this->status = $status;
    $this->items = $items;
    $this->tableNumber = $tableNumber;
    $this->clientName = $clientName;
  }

  public function jsonSerialize(): mixed
  {
    return [
      'id' => $this->id,
      'status' => $this->status,
      'items' => $this->items,
      'client' => [
        'id' => $this->clientId,
        'name' => $this->clientName,
      ],
      'table' => [
        'id' => $this->tableId,
        'number' => $this->tableNumber,
      ],
    ];
  }
}
