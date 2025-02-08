<?php

namespace Tests;

use App\Models\Bill\Bill;
use App\Models\Bill\BillDTO;
use App\Models\Bill\BillMapper;
use App\Models\Employee\Employee;
use App\Models\Order\Order;
use App\Models\PaymentMethod\PaymentMethod;

use function Kahlan\describe;
use function Kahlan\expect;

describe('BillMapper', function () {
  it('should return BillDTO', function () {
    $entity = new Bill(
      10,
      new Employee(1),
      new PaymentMethod(1),
      new Order(1),
      0
    );

    $dto = BillMapper::toDTO($entity);

    expect($dto)->toBeAnInstanceOf('App\Models\Bill\BillDTO');
  });

  it('should return a Bill', function () {
    $dto = new BillDTO(1, 1, 1, 10, 0);

    $entity = BillMapper::toEntity($dto);

    expect($entity)->toBeAnInstanceOf('App\Models\Bill\Bill');
  });
});
