<?php

declare(strict_types=1);

namespace App\Models\Employee;

use App\Contracts\Employee\EmployeeRepository;
use App\Exceptions\Employee\EmployeeRepositoryException;
use App\Exceptions\Employee\EmployeeServiceException;

class EmployeeService
{
  private EmployeeRepository $repository;

  public function __construct(EmployeeRepository $repository)
  {
    $this->repository = $repository;
  }

  /**
   * @return array{data: Employee[], count: int}
   */
  public function getEmployees(int $page, int $perPage)
  {
    try {
      $offset = intval(ceil(($page - 1) * $perPage));

      return $this->repository->getAll($perPage, $offset);
    } catch (EmployeeRepositoryException $exception) {
      throw new EmployeeServiceException($exception->getMessage(), $exception->getCode());
    }
  }
}
