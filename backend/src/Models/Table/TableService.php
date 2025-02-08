<?php

namespace App\Models\Table;

use App\Contracts\Table\TableRepository;
use App\Exceptions\Table\TableRepositoryException;
use App\Exceptions\Table\TableServiceException;

class TableService
{
  private TableRepository $repository;

  public function __construct(TableRepository $repository)
  {
    $this->repository = $repository;
  }

  /**
   * @return array<Table>
   */
  public function getTables(string | null $startDate = null)
  {
    try {
      if ($startDate === null) {
        return $this->repository->getAll();
      }

      $date = new \DateTime($startDate);
      $date->modify('+2 hours');
      $endDate = $date->format('Y-m-d H:i:s');

      return $this->repository->getAll($startDate, $endDate);
    } catch (TableRepositoryException $exception) {
      throw new TableServiceException(
        $exception->getMessage(),
        $exception->getCode()
      );
    }
  }
}
