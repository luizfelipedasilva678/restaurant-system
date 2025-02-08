<?php

namespace App\Models\Item;

use App\Contracts\Item\ItemRepository;
use App\Exceptions\Item\ItemServiceException;
use App\Exceptions\Item\ItemRepositoryException;

class ItemService
{
  private ItemRepository $repository;

  public function __construct(ItemRepository $repository)
  {
    $this->repository = $repository;
  }

  /**
   * @return array{data: Item[], count: int}
   */
  public function getItems(int $page, int $perPage): array
  {
    try {
      $offset = intval(ceil(($page - 1) * $perPage));

      return $this->repository->getItems($perPage, $offset);
    } catch (ItemRepositoryException $exception) {
      throw new ItemServiceException($exception->getMessage(), $exception->getCode());
    }
  }
}
