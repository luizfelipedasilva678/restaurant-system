<?php

namespace App\Models\PaymentMethod;

class PaymentMethodMapper
{
  public static function toDTO(
    PaymentMethod $entity
  ): PaymentMethodDTO {
    return new PaymentMethodDTO(
      $entity->getId(),
      $entity->getName()
    );
  }

  /**
   * @param PaymentMethod[] $entities
   * @return PaymentMethodDTO[]
   */
  public static function toDTOArray(array $entities): array
  {
    return array_map([PaymentMethodMapper::class, 'toDTO'], $entities);
  }
}
