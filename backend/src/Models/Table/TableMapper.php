<?php

namespace App\Models\Table;

class TableMapper
{
  public static function toDTO(Table $entity): TableDTO
  {
    return new TableDTO(
      $entity->getId(),
      $entity->getNumber()
    );
  }

  /**
   * @param Table[] $entities
   *
   * @return TableDTO[]
   */
  public static function toDTOArray(array $entities): array
  {
    return array_map([TableMapper::class, 'toDTO'], $entities);
  }
}
