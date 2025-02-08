<?php

namespace App\Contracts\Item;

use App\Models\Item\Item;

interface ItemRepository
{
  /**
   * @return array{data: Item[], count: int}
   */
  public function getItems(int $limit, int $offset): array;
}
