<?php

namespace Test;

use App\Models\Restaurant\RestaurantRepositoryInRDB;
use App\Models\Restaurant\RestaurantService;
use App\Utils\Env;
use App\Utils\PDOBuilder;

use function App\Utils\Tests\cleanTestDB;
use function App\Utils\Tests\isOutOfReservationHours;
use function App\Utils\Tests\isOutOfWorkingHours;
use function Kahlan\beforeAll;
use function Kahlan\describe;
use function Kahlan\it;
use function Kahlan\expect;
use function Kahlan\skipIf;

describe('RestaurantService', function () {
  $this->service = null;
  $this->pdo = null;

  beforeAll(function () {
    Env::initEnv(__DIR__ . '/../config/.env');
    date_default_timezone_set('America/Sao_Paulo');
    $instance = new PDOBuilder();
    $this->pdo = PDOBuilder::build(true);
    cleanTestDB($this->pdo);
    allow($instance)->toReceive('::build')->andReturn($this->pdo);
    $repository = new RestaurantRepositoryInRDB($this->pdo);
    $this->service = new RestaurantService($repository);
  });

  it('should create the service correctly', function () {
    expect($this->service)->toBeAnInstanceOf('App\\Models\\Restaurant\\RestaurantService');
  });

  it('should return that the restaurant is Open', function () {
    skipIf(!isOutOfWorkingHours() ? false : true);

    expect($this->service->isOpen())->toBe(true);
  });

  it('should return that the restaurant is Closed', function () {
    skipIf(isOutOfWorkingHours() ? false : true);

    expect($this->service->isOpen())->toBe(false);
  });

  it('should return that is not accepting reservations', function () {
    skipIf(isOutOfReservationHours() ? false : true);

    expect($this->service->isAcceptingNewReservations())->toBe(false);
  });

  it('should return that is accepting reservations', function () {
    skipIf(!isOutOfReservationHours() ? false : true);

    expect($this->service->isAcceptingNewReservations())->toBe(true);
  });
});
