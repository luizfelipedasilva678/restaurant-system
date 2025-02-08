<?php

declare(strict_types=1);

namespace App\Contracts\Table;

use App\Models\Table\Table;

interface TableRepository
{
  /**
   * @return array<Table>
   */
  public function getAll(string | null $startDate = null, string | null $endDate = null): array;
}
