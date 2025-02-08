<?php

declare(strict_types=1);

namespace App\Models\Phone;

class Phone
{
  private int $id;
  private string $number;

  public function __construct(int $id = 0, string $number = '')
  {
    $this->id = $id;
    $this->number = $number;
  }

  public function getId(): int
  {
    return $this->id;
  }

  public function getNumber(): string
  {
    return $this->number;
  }

  public function setNumber(string $number): void
  {
    $this->number = $number;
  }

  public function setId(int $id): void
  {
    $this->id = $id;
  }
}
