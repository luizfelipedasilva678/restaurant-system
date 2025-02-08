<?php

declare(strict_types=1);

namespace App\Models\Client;

use App\Models\Phone\Phone;

class Client
{
  private int $id;
  private string $name;

  /**
   * @var Phone $phone
   */
  private $phone;

  /**
   * @param Phone $phone
   */
  public function __construct(int $id = 0, string $name = '', $phone = new Phone())
  {
    $this->id = $id;
    $this->name = $name;
    $this->phone = $phone;
  }

  /**
   * @param Phone $phone
   */
  public function setPhone($phone): void
  {
    $this->phone = $phone;
  }

  /**
   * @return Phone
   */
  public function getPhone()
  {
    return $this->phone;
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
