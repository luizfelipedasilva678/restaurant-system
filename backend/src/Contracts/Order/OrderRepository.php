<?php

namespace App\Contracts\Order;

use App\Models\Order\Order;

interface OrderRepository
{
  public function create(Order $order): bool;

  public function addItems(Order $order): bool;

  public function getOrder(int $orderId): ?Order;

  /**
   * @return Order[]
   */
  public function getOrders(): array;

  public function update(Order $order): bool;
}
