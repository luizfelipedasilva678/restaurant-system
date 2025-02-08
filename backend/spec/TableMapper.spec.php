<?php

namespace Tests;

use App\Models\Table\Table;
use App\Models\Table\TableMapper;

use function Kahlan\describe;
use function Kahlan\expect;

describe('TableMapper', function () {
  it('should return TableDTO', function () {
    $entity = new Table();

    $dto = TableMapper::toDTO($entity);

    expect($dto)->toBeAnInstanceOf('App\Models\Table\TableDTO');
  });

  it('should return TableDTO array', function () {
    $entities = [
      new Table(),
      new Table(),
    ];

    $dtos = TableMapper::toDTOArray($entities);

    expect($dtos[0])->toBeAnInstanceOf('App\Models\Table\TableDTO');
  });
});
