<?php

declare(strict_types=1);

namespace Tests;

use App\Models\Order\Order;
use App\Models\Order\OrderDTO;
use App\Models\Order\OrderMapper;

use function Kahlan\describe;
use function Kahlan\expect;
use function Kahlan\it;

describe('OrderMapper', function () {
  it('should return an orderDto correctly', function () {
    expect(OrderMapper::toDTO(new Order()))->toBeAnInstanceOf('App\\Models\\Order\\OrderDTO');
  });

  it('should return an order correctly', function () {
    expect(OrderMapper::toEntity(new OrderDTO()))->toBeAnInstanceOf('App\\Models\\Order\\Order');
  });
});
