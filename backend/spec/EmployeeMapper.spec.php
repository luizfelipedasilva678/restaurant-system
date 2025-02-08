<?php

namespace Tests;

use App\Models\Employee\Employee;
use App\Models\Employee\EmployeeMapper;

use function Kahlan\describe;
use function Kahlan\expect;

describe('EmployeeMapper', function () {
  it('should return EmployeeDTO', function () {
    $entity = new Employee();

    $dto = EmployeeMapper::toDTO($entity);

    expect($dto)->toBeAnInstanceOf('App\Models\Employee\EmployeeDTO');
  });

  it('should return EmployeeDTO array', function () {
    $entities = [
      new Employee(),
      new Employee(),
    ];

    $dtos = EmployeeMapper::toDTOArray($entities);

    expect($dtos[0])->toBeAnInstanceOf('App\Models\Employee\EmployeeDTO');
  });
});
