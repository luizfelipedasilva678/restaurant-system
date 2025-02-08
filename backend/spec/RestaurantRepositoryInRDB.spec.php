<?php

namespace Tests;

use App\Models\Restaurant\RestaurantRepositoryInRDB;
use App\Utils\Env;
use App\Utils\PDOBuilder;

use function App\Utils\Tests\cleanTestDB;
use function Kahlan\beforeAll;
use function Kahlan\describe;
use function Kahlan\it;
use function Kahlan\expect;

describe('RestaurantRepositoryImpl', function () {
  $this->repository = null;
  $this->pdo = null;

  beforeAll(function () {
    Env::initEnv(__DIR__ . '/../config/.env');
    $this->pdo = PDOBuilder::build(true);
    cleanTestDB($this->pdo);
    $this->repository = new RestaurantRepositoryInRDB($this->pdo);
  });

  it('should create the repository correctly', function () {
    expect($this->repository)->toBeAnInstanceOf('App\\Models\\Restaurant\\RestaurantRepositoryInRDB');
  });

  it('should return the working hours correctly', function () {
    $workingHours = $this->repository->getWorkingHours();

    expect($workingHours)->toBe([
      'Monday' => [
        'startTime' => '11:00:00',
        'endTime' => '15:00:00'
      ],
      'Tuesday' => [
        'startTime' => '11:00:00',
        'endTime' => '15:00:00'
      ],
      'Wednesday' => [
        'startTime' => '11:00:00',
        'endTime' => '15:00:00'
      ],
      'Thursday' => [
        'startTime' => '11:00:00',
        'endTime' => '22:00:00'
      ],
      'Friday' => [
        'startTime' => '11:00:00',
        'endTime' => '22:00:00'
      ],
      'Saturday' => [
        'startTime' => '11:00:00',
        'endTime' => '22:00:00'
      ],
      'Sunday' => [
        'startTime' => '11:00:00',
        'endTime' => '22:00:00'
      ]
    ]);
  });

  it('should return the reservation hours correctly', function () {
    $reservationHours = $this->repository->getReservationHours();

    expect($reservationHours)->toBe([
      'Thursday' => [
        'startTime' => '11:00:00',
        'endTime' => '20:00:00'
      ],
      'Friday' => [
        'startTime' => '11:00:00',
        'endTime' => '20:00:00'
      ],
      'Saturday' => [
        'startTime' => '11:00:00',
        'endTime' => '20:00:00'
      ],
      'Sunday' => [
        'startTime' => '11:00:00',
        'endTime' => '20:00:00'
      ]
    ]);
  });
});
