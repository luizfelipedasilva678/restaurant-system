<?php

namespace App\Contracts\Order;

interface OrderView
{
  /**
   * @param array<mixed>|object|string $data
   */
  public function respondWith($data): void;
}
