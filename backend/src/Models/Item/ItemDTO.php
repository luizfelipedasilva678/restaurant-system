<?php

namespace App\Models\Item;

class ItemDTO implements \JsonSerializable
{
  public int $id;
  public string $code;
  public string $description;
  public float $price;
  public string $category;

  public function __construct(
    int $id = 0,
    string $code = '',
    string $description = '',
    float $price = 0.0,
    string $category = ''
  ) {
    $this->id = $id;
    $this->code = $code;
    $this->description = $description;
    $this->price = $price;
    $this->category = $category;
  }

  public function jsonSerialize(): mixed
  {
    return [
      'id' => $this->id,
      'code' => $this->code,
      'description' => $this->description,
      'price' => $this->price,
      'category' => $this->category,
    ];
  }
}
