<?php

declare(strict_types=1);

namespace App\Contracts\Table;

interface TableView
{
  /**
   * @param array<mixed>|object|string $data
   */
  public function respondWith($data): void;
}
