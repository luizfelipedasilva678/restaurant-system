<?php

declare(strict_types=1);

namespace App\Models\PaymentMethod;

class PaymentMethodDTO implements \JsonSerializable
{
  public int $id;
  public string $name;

  public function __construct(
    int $id = 0,
    string $name = '',
  ) {
    $this->id = $id;
    $this->name = $name;
  }

  public function jsonSerialize(): mixed
  {
    return [
      'id' => $this->id,
      'name' => $this->name,
    ];
  }
}
