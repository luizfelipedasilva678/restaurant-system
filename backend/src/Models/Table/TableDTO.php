<?php

namespace App\Models\Table;

class TableDTO implements \JsonSerializable
{
  public int $id;
  public int $number;

  public function __construct(int $id = 0, int $number = 0)
  {
    $this->id = $id;
    $this->number = $number;
  }

  public function jsonSerialize(): mixed
  {
    return [
      'id' => $this->id,
      'number' => $this->number,
    ];
  }
}
