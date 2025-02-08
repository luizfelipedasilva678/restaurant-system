<?php

namespace App\Presenters;

use App\Contracts\Employee\EmployeeView;
use App\Enums\HttpCodes;
use App\Exceptions\Employee\EmployeePresenterException;
use App\Exceptions\Employee\EmployeeServiceException;
use App\Models\Employee\EmployeeMapper;
use App\Models\Employee\EmployeeRepositoryInRDB;
use App\Models\Employee\EmployeeService;
use App\Utils\PDOBuilder;

class EmployeePresenter
{
  private EmployeeView $view;
  private EmployeeService $service;

  public function __construct(EmployeeView $view)
  {
    $this->view = $view;

    try {
      $repository = new EmployeeRepositoryInRDB(PDOBuilder::build());
      $this->service = new EmployeeService($repository);
    } catch (\PDOException $exception) {
      throw new EmployeePresenterException('Error connecting to database', HttpCodes::HTTP_SERVER_ERROR->value);
    }
  }

  public function getEmployees(int $page, int $perPage): void
  {
    try {
      $result = $this->service->getEmployees($page, $perPage);
      $employeesDTOs = EmployeeMapper::toDTOArray($result['data']);

      $this->view->respondWith([
        'data' => $employeesDTOs,
        'count' => $result['count']
      ]);
    } catch (EmployeeServiceException $exception) {
      throw new EmployeePresenterException($exception->getMessage(), $exception->getCode());
    }
  }
}
