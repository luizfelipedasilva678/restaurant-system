<?php

namespace App\Models\Item;

use App\Models\Category\Category;

class Item
{
  private int $id;
  private string $code;
  private Category $category;
  private string $description;
  private float $price;

  public function __construct(
    int $id = 0,
    string $code = '',
    Category $category = new Category(0, ''),
    string $description = '',
    float $price = 0.0
  ) {
    $this->id = $id;
    $this->code = $code;
    $this->category = $category;
    $this->description = $description;
    $this->price = $price;
  }

  public function getId(): int
  {
    return $this->id;
  }

  public function getCode(): string
  {
    return $this->code;
  }

  public function getCategory(): Category
  {
    return $this->category;
  }

  public function getDescription(): string
  {
    return $this->description;
  }

  public function getPrice(): float
  {
    return $this->price;
  }

  public function setId(int $id): void
  {
    $this->id = $id;
  }

  public function setCode(string $code): void
  {
    $this->code = $code;
  }

  public function setCategory(Category $category): void
  {
    $this->category = $category;
  }

  public function setDescription(string $description): void
  {
    $this->description = $description;
  }

  public function setPrice(float $price): void
  {
    $this->price = $price;
  }
}
