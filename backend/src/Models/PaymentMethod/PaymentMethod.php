<?php

declare(strict_types=1);

namespace App\Models\PaymentMethod;

class PaymentMethod
{
  private int $id;
  private string $name;

  public function __construct(int $id = 0, string $name = '')
  {
    $this->id = $id;
    $this->name = $name;
  }

  public function getId(): int
  {
    return $this->id;
  }

  public function getName(): string
  {
    return $this->name;
  }

  public function setName(string $name): void
  {
    $this->name = $name;
  }

  public function setId(int $id): void
  {
    $this->id = $id;
  }
}
