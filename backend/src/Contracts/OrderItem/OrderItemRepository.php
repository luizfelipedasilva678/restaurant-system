<?php

namespace App\Contracts\OrderItem;

use App\Models\OrderItem\OrderItem;

interface OrderItemRepository
{
  public function create(OrderItem $orderItem, int $tableId): bool;
}
