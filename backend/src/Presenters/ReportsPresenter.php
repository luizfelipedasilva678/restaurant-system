<?php

namespace App\Presenters;

use App\Contracts\Reports\ReportsView;
use App\Enums\HttpCodes;
use App\Exceptions\Bill\BillServiceException;
use App\Exceptions\Reports\ReportsPresenterException;
use App\Models\Bill\BillRepositoryInRDB;
use App\Models\Bill\BillService;
use App\Utils\PDOBuilder;
use PDOException;

class ReportsPresenter
{
  private BillService $billService;
  private ReportsView $view;

  public function __construct(ReportsView $view)
  {
    try {
      $this->view = $view;
      $this->billService = new BillService(new BillRepositoryInRDB(PDOBuilder::build()));
    } catch (PDOException $exception) {
      throw new ReportsPresenterException('Error connecting to database', HttpCodes::HTTP_SERVER_ERROR->value);
    }
  }

  public function getSalesByEmployee(string $startDate, string $endDate): void
  {
    try {
      $this->view->respondWith($this->billService->getSalesByEmployee($startDate, $endDate));
    } catch (BillServiceException $exception) {
      throw new ReportsPresenterException($exception->getMessage(), $exception->getCode());
    }
  }

  public function getSalesByPaymentMethod(string $startDate, string $endDate): void
  {
    try {
      $this->view->respondWith($this->billService->getSalesByPaymentMethod($startDate, $endDate));
    } catch (BillServiceException $exception) {
      throw new ReportsPresenterException($exception->getMessage(), $exception->getCode());
    }
  }

  public function getSalesByCategory(string $startDate, string $endDate): void
  {
    try {
      $this->view->respondWith($this->billService->getSalesByCategory($startDate, $endDate));
    } catch (BillServiceException $exception) {
      throw new ReportsPresenterException($exception->getMessage(), $exception->getCode());
    }
  }

  public function getSalesByDay(string $startDate, string $endDate): void
  {
    try {
      $this->view->respondWith($this->billService->getSalesByDay($startDate, $endDate));
    } catch (BillServiceException $exception) {
      throw new ReportsPresenterException($exception->getMessage(), $exception->getCode());
    }
  }
}
