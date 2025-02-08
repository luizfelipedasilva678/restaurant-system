<?php

namespace App\Contracts\Item;

interface ItemView
{
  /**
   * @param array<mixed>|object|string $data
   */
  public function respondWith($data): void;
}
