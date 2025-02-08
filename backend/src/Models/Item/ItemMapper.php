<?php

namespace App\Models\Item;

class ItemMapper
{
  public static function toDTO(Item $entity): ItemDTO
  {
    return new ItemDTO(
      $entity->getId(),
      $entity->getCode(),
      $entity->getDescription(),
      $entity->getPrice(),
      $entity->getCategory()->getName()
    );
  }

  /**
   * @param Item[] $entities
   * @return ItemDTO[]
   */
  public static function toDTOArray(array $entities): array
  {
    return array_map([ItemMapper::class, 'toDTO'], $entities);
  }
}
