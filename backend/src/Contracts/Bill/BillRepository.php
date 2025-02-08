<?php

declare(strict_types=1);

namespace App\Contracts\Bill;

use App\Models\Bill\Bill;

interface BillRepository
{
  public function create(Bill $bill): bool;

  /**
   * @return array<string, int>
   */
  public function getSalesByPaymentMethod(string $startDate, string $endDate): array;

  /**
   * @return array<string, int>
   */
  public function getSalesByEmployee(string $startDate, string $endDate): array;

  /**
   * @return array<string, int>
   */
  public function getSalesByCategory(string $startDate, string $endDate): array;

  /**
   * @return array<string, int>
   */
  public function getSalesByDay(string $startDate, string $endDate): array;
}
