<?php

declare(strict_types=1);

namespace Tests;

use App\Models\Item\Item;
use App\Models\Item\ItemMapper;

use function Kahlan\describe;
use function Kahlan\expect;
use function Kahlan\it;

describe('OrderMapper', function () {
  it('should return an itemDTO correctly', function () {
    expect(ItemMapper::toDTO(new Item()))->toBeAnInstanceOf('App\\Models\\Item\\ItemDTO');
  });
});
