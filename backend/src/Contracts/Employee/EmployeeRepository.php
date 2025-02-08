<?php

declare(strict_types=1);

namespace App\Contracts\Employee;

use App\Models\Employee\Employee;

interface EmployeeRepository
{
  /**
   * @return array{data: Employee[], count: int}
   */
  public function getAll(int $limit, int $offset);

  public function getByLoginAndPassword(string $login, string $password): Employee;

  public function getSaltByLogin(string $login): string;
}
