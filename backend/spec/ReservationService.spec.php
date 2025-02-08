<?php

namespace Tests;

use App\Exceptions\Reservation\ReservationServiceException;
use App\Models\Client\Client;
use App\Models\Client\ClientRepositoryInRDB;
use App\Models\Client\ClientService;
use App\Models\Employee\Employee;
use App\Models\Phone\Phone;
use App\Models\Reservation\Reservation;
use App\Models\Reservation\ReservationRepositoryInRDB;
use App\Models\Reservation\ReservationService;
use App\Models\Restaurant\RestaurantRepositoryInRDB;
use App\Models\Restaurant\RestaurantService;
use App\Models\Table\Table;
use App\Models\Table\TableRepositoryInRDB;
use App\Models\Table\TableService;
use App\Utils\Env;
use App\Utils\PDOBuilder;

use function App\Utils\Tests\cleanTestDB;
use function App\Utils\Tests\isOutOfReservationHours;
use function App\Utils\Tests\isOutOfWorkingHours;
use function Kahlan\beforeAll;
use function Kahlan\it;
use function Kahlan\expect;
use function Kahlan\skipIf;

describe('ReservationService', function () {
  $this->pdo = null;
  $this->service = null;

  beforeAll(function () {
    Env::initEnv(__DIR__ . '/../config/.env');
    date_default_timezone_set('America/Sao_Paulo');
    $instance = new PDOBuilder();
    $this->pdo = PDOBuilder::build(true);
    cleanTestDB($this->pdo);
    allow($instance)->toReceive('::build')->andReturn($this->pdo);
    $this->service = new ReservationService(
      new ReservationRepositoryInRDB($this->pdo),
      new ClientService(new ClientRepositoryInRDB($this->pdo)),
      new RestaurantService(new RestaurantRepositoryInRDB($this->pdo)),
      new TableService(new TableRepositoryInRDB($this->pdo)),
    );
  });

  it('should create the service correctly', function () {
    expect($this->service)->toBeAnInstanceOf('App\\Models\\Reservation\\ReservationService');
  });

  it('should return the reservations correctly', function () {
    $reservations = $this->service->getReservations(1, 2, false);

    expect(count($reservations['data']))->toBe(2);
  });

  it('should return an error informing that the restaurant is closed', function () {
    skipIf(isOutOfWorkingHours() ? false : true);

    $endTime = new \DateTime();
    $endTime->modify('+2 hours');

    expect(function () use ($endTime) {
      $this->service->create(
        new Reservation(
          new Table(1, 3),
          new Client(1),
          new Employee(1),
          new \DateTime(),
          0,
          'active',
          $endTime
        )
      );
    })->toThrow(new ReservationServiceException('O restaurante está fechado', 400));
  });

  it('should return and error informing that the restaurant is not accepting new reservations', function () {
    skipIf(!isOutOfWorkingHours() && isOutOfReservationHours() ? false : true);

    $endTime = new \DateTime();
    $endTime->modify('+2 hours');

    expect(function () use ($endTime) {
      $this->service->create(
        new Reservation(
          new Table(1, 3),
          new Client(1),
          new Employee(1),
          new \DateTime(),
          0,
          'active',
          $endTime
        )
      );
    })->toThrow(new ReservationServiceException('O restaurante não está aceitando reservas no momento', 400));
  });

  it('should return and error informing that the reservation time is out of working hours', function () {
    skipIf(!isOutOfWorkingHours() && !isOutOfReservationHours() ? false : true);

    $startTime = new \DateTime();
    $startTime->setTime(6, 0, 0);

    $endTime = new \DateTime();
    $endTime->setTime(8, 0, 0);

    $exceptionMessage = '';

    try {
      $this->service->create(
        new Reservation(
          new Table(1, 3),
          new Client(1),
          new Employee(1),
          $startTime,
          0,
          'active',
          $endTime
        )
      );
    } catch (ReservationServiceException $e) {
      $exceptionMessage = $e->getMessage();
    }

    expect(str_contains($exceptionMessage, 'funcionamento'))->toBe(true);
  });

  it('should return an error given that the table is not available', function () {
    skipIf(!isOutOfWorkingHours() && !isOutOfReservationHours() ? false : true);

    $date = new \DateTime();
    $date->modify('next wednesday');
    $date->setTime(11, 0, 0);
    $startDate = new \DateTime($date->format('Y-m-d H:i:s'));
    $date->setTime(13, 0, 0);
    $endTime = new \DateTime($date->format('Y-m-d H:i:s'));

    expect(function () use ($endTime, $startDate) {
      $this->service->create(
        new Reservation(
          new Table(1),
          new Client(2),
          new Employee(2),
          $startDate,
          0,
          'active',
          $endTime
        )
      );
    })->toThrow(new ReservationServiceException('Mesa indisponível', 400));
  });

  it('should create the reservation correctly', function () {
    skipIf(!isOutOfWorkingHours() && !isOutOfReservationHours() ? false : true);

    $date = new \DateTime();
    $date->modify('next friday');
    $date->setTime(11, 0, 0);
    $startDate = new \DateTime($date->format('Y-m-d H:i:s'));
    $date->setTime(13, 0, 0);
    $endTime = new \DateTime($date->format('Y-m-d H:i:s'));

    $reservation = $this->service->create(
      new Reservation(
        new Table(1),
        new Client(2, 'test', new Phone(0, '(22) 2222-2222')),
        new Employee(2),
        $startDate,
        0,
        'active',
        $endTime
      )
    );

    expect($reservation->getId())->toBeGreaterThan(0);
    expect($reservation)->toBeAnInstanceOf('\App\Models\Reservation\Reservation');
  });

  it('should get reservation correctly', function () {
    $reservation = $this->service->getReservation(1);

    expect($reservation)->toBeAnInstanceOf('\App\Models\Reservation\Reservation');
    expect($reservation->getId())->toBe(1);
  });

  it('should update reservation correctly', function () {
    $reservation = $this->service->update(
      new Reservation(
        new Table(),
        new Client(),
        new Employee(),
        new \DateTime(),
        1,
        'inactive',
        new \DateTime()
      )
    );

    expect($reservation)->toBeAnInstanceOf('\App\Models\Reservation\Reservation');
    expect($reservation->getId())->toBe(1);
    expect($reservation->getStatus())->toBe('inactive');
  });
});
