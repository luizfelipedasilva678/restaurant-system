<?php

namespace App\Presenters;

use App\Contracts\Table\TableView;
use App\Enums\HttpCodes;
use App\Exceptions\Table\TablePresenterException;
use App\Exceptions\Table\TableServiceException;
use App\Models\Table\TableMapper;
use App\Models\Table\TableRepositoryInRDB;
use App\Models\Table\TableService;
use App\Utils\PDOBuilder;

class TablePresenter
{
  private TableView $view;
  private TableService $service;

  public function __construct(TableView $view)
  {
    try {
      $this->view = $view;
      $repository = new TableRepositoryInRDB(PDOBuilder::build());
      $this->service = new TableService($repository);
    } catch (\PDOException $exception) {
      throw new TablePresenterException('Error connecting to database', HttpCodes::HTTP_SERVER_ERROR->value);
    }
  }

  public function getTables(string | null $startDate = null): void
  {
    try {
      $tables = $this->service->getTables($startDate);
      $this->view->respondWith(TableMapper::toDTOArray($tables));
    } catch (TableServiceException $exception) {
      throw new TablePresenterException($exception->getMessage(), $exception->getCode());
    }
  }
}
