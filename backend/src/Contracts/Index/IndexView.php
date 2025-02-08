<?php

declare(strict_types=1);

namespace App\Contracts\Index;

interface IndexView
{
  /**
   * @param array<mixed>|object|string $data
   */
  public function respondWith($data): void;
}
