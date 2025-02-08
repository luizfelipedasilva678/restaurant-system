<?php

namespace Tests;

use App\Models\Client\Client;
use App\Models\Employee\Employee;
use App\Models\Reservation\Reservation;
use App\Models\Reservation\ReservationDTO;
use App\Models\Reservation\ReservationMapper;
use App\Models\Table\Table;

use function Kahlan\describe;
use function Kahlan\expect;

describe('ReservationMapper', function () {
  it('should return ReservationDTO', function () {
    $entity = new Reservation(
      new Table(),
      new Client(),
      new Employee()
    );

    $dto = ReservationMapper::toDTO($entity);

    expect($dto)->toBeAnInstanceOf('App\Models\Reservation\ReservationDTO');
  });

  it('should return ReservationDTO array', function () {
    $entities = [
      new Reservation(
        new Table(),
        new Client(),
        new Employee()
      ),
      new Reservation(
        new Table(),
        new Client(),
        new Employee()
      )
    ];

    $dtos = ReservationMapper::toDTOArray($entities);

    expect($dtos[0])->toBeAnInstanceOf('App\Models\Reservation\ReservationDTO');
  });

  it('should return ReservationEntity', function () {
    $dto = new ReservationDTO();

    $entity = ReservationMapper::toEntity($dto);

    expect($entity)->toBeAnInstanceOf('App\Models\Reservation\Reservation');
  });
});
