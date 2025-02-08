<?php

namespace Tests;

use App\Models\PaymentMethod\PaymentMethod;
use App\Models\PaymentMethod\PaymentMethodMapper;

use function Kahlan\describe;
use function Kahlan\expect;

describe('PaymentMethodMapper', function () {
  it('should return PaymentMethodDTO', function () {
    $entity = new PaymentMethod();

    $dto = PaymentMethodMapper::toDTO($entity);

    expect($dto)->toBeAnInstanceOf('App\Models\PaymentMethod\PaymentMethodDTO');
  });

  it('should return PaymentMethodDTO array', function () {
    $entities = [
      new PaymentMethod(),
      new PaymentMethod(),
    ];

    $dtos = PaymentMethodMapper::toDTOArray($entities);

    expect($dtos[0])->toBeAnInstanceOf('App\Models\PaymentMethod\PaymentMethodDTO');
  });
});
