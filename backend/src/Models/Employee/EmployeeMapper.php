<?php

namespace App\Models\Employee;

class EmployeeMapper
{
  public static function toDTO(
    Employee $entity
  ): EmployeeDTO {
    return new EmployeeDTO(
      $entity->getId(),
      $entity->getName(),
      $entity->getType()
    );
  }

  /**
   * @param Employee[] $entities
   * @return EmployeeDTO[]
   */
  public static function toDTOArray(array $entities): array
  {
    return array_map([EmployeeMapper::class, 'toDTO'], $entities);
  }
}
