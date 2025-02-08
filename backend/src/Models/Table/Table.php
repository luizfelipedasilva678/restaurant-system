<?php

declare(strict_types=1);

namespace App\Models\Table;

class Table
{
  private int $id;
  private int $number;

  public function __construct(int $id = 0, int $number = 0)
  {
    $this->id = $id;
    $this->number = $number;
  }

  public function getId(): int
  {
    return $this->id;
  }

  public function setId(int $id): void
  {
    $this->id = $id;
  }

  public function getNumber(): int
  {
    return $this->number;
  }

  public function setNumber(int $number): void
  {
    $this->number = $number;
  }

  public static function build(int $id, int $number): Table
  {
    $table = new Table();

    $table->setId($id);
    $table->setNumber($number);

    return $table;
  }
}
