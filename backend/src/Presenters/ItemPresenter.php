<?php

namespace App\Presenters;

use App\Contracts\Item\ItemView;
use App\Enums\HttpCodes;
use App\Exceptions\Item\ItemPresenterException;
use App\Exceptions\Item\ItemServiceException;
use App\Models\Item\ItemMapper;
use App\Models\Item\ItemRepositoryInRDB;
use App\Models\Item\ItemService;
use App\Utils\PDOBuilder;

class ItemPresenter
{
  private ItemView $view;
  private ItemService $service;

  public function __construct(ItemView $view)
  {
    try {
      $pdo = PDOBuilder::build();
      $this->view = $view;
      $this->service = new ItemService(
        new ItemRepositoryInRDB($pdo)
      );
    } catch (\PDOException $exception) {
      throw new ItemPresenterException('Ocorreu um erro inesperado', HttpCodes::HTTP_SERVER_ERROR->value);
    }
  }

  public function getItems(int $page, int $perPage): void
  {
    try {
      $result = $this->service->getItems($page, $perPage);
      $itemsDTOs = ItemMapper::toDTOArray($result['data']);

      $this->view->respondWith([
        'data' => $itemsDTOs,
        'count' => $result['count']
      ]);
    } catch (ItemServiceException $exception) {
      throw new ItemPresenterException($exception->getMessage(), $exception->getCode());
    }
  }
}
