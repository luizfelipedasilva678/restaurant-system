<?php

declare(strict_types=1);

namespace App\Models\Bill;

use App\Contracts\Bill\BillRepository;
use App\Exceptions\Bill\BillRepositoryException;
use App\Exceptions\Bill\BillServiceException;

class BillService
{
  private BillRepository $repository;

  public function __construct(BillRepository $repository)
  {
    $this->repository = $repository;
  }

  /**
   * @return array<string, int>
   */
  public function getSalesByPaymentMethod(string $startDate, string $endDate): array
  {
    try {
      return $this->repository->getSalesByPaymentMethod($startDate, $endDate);
    } catch (BillRepositoryException $exception) {
      throw new BillServiceException($exception->getMessage(), $exception->getCode());
    }
  }

  /**
   * @return array<string, int>
   */
  public function getSalesByEmployee(string $startDate, string $endDate): array
  {
    try {
      return $this->repository->getSalesByEmployee($startDate, $endDate);
    } catch (BillRepositoryException $exception) {
      throw new BillServiceException($exception->getMessage(), $exception->getCode());
    }
  }

  /**
   * @return array<string, int>
   */
  public function getSalesByCategory(string $startDate, string $endDate): array
  {
    try {
      return $this->repository->getSalesByCategory($startDate, $endDate);
    } catch (BillRepositoryException $exception) {
      throw new BillServiceException($exception->getMessage(), $exception->getCode());
    }
  }

  /**
   * @return array<string, int>
   */
  public function getSalesByDay(string $startDate, string $endDate): array
  {
    try {
      return $this->repository->getSalesByDay($startDate, $endDate);
    } catch (BillRepositoryException $exception) {
      throw new BillServiceException($exception->getMessage(), $exception->getCode());
    }
  }

  public function create(BillDTO $dto): bool
  {
    try {
      return $this->repository->create(BillMapper::toEntity($dto));
    } catch (BillRepositoryException $exception) {
      throw new BillServiceException($exception->getMessage(), $exception->getCode());
    }
  }
}
