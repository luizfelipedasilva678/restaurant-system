<?php

namespace App\Models\OrderItem;

use App\Models\Item\Item;

class OrderItem
{
  public int $id;
  public int $quantity;
  public Item $item;

  public function __construct(
    int $id = 0,
    int $quantity = 0,
    Item $item = new Item()
  ) {
    $this->id = $id;
    $this->quantity = $quantity;
    $this->item = $item;
  }

  public function getId(): int
  {
    return $this->id;
  }

  public function getQuantity(): int
  {
    return $this->quantity;
  }

  public function getItem(): Item
  {
    return $this->item;
  }

  public function setId(int $id): void
  {
    $this->id = $id;
  }

  public function setQuantity(int $quantity): void
  {
    $this->quantity = $quantity;
  }

  public function setItem(Item $item): void
  {
    $this->item = $item;
  }
}
