<?php

declare(strict_types=1);

namespace App\Contracts\Reports;

interface ReportsView
{
  /**
   * @param array<mixed>|object|string $data
   */
  public function respondWith($data): void;
}
