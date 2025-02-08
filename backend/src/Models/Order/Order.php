<?php

namespace App\Models\Order;

use App\Enums\OrderStatus;
use App\Models\Client\Client;
use App\Models\Table\Table;
use App\Models\OrderItem\OrderItem;

class Order
{
  /**
   * @var OrderItem[]
   */
  public array $items;
  public int $id;
  public OrderStatus $status;
  public Table $table;
  public Client $client;

  /**
   * @param OrderItem[] $items
   */
  public function __construct(
    int $id = 0,
    OrderStatus $status = OrderStatus::open,
    Table $table = new Table(),
    Client $client = new Client(),
    array $items = []
  ) {
    $this->id = $id;
    $this->status = $status;
    $this->table = $table;
    $this->client = $client;
    $this->items = $items;
  }

  public function getId(): int
  {
    return $this->id;
  }

  public function getStatus(): OrderStatus
  {
    return $this->status;
  }

  public function getTable(): Table
  {
    return $this->table;
  }

  public function getClient(): Client
  {
    return $this->client;
  }

  /**
   * @return OrderItem[]
   */
  public function getItems(): array
  {
    return $this->items;
  }

  public function setId(int $id): void
  {
    $this->id = $id;
  }

  public function setStatus(OrderStatus $status): void
  {
    $this->status = $status;
  }

  public function setTable(Table $table): void
  {
    $this->table = $table;
  }

  public function setClient(Client $client): void
  {
    $this->client = $client;
  }

  /**
   * @param OrderItem[] $items
   */
  public function setItems(array $items): void
  {
    $this->items = $items;
  }
}
