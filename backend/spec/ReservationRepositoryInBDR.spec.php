<?php

namespace Tests;

use App\Exceptions\Reservation\ReservationRepositoryException;
use App\Models\Client\Client;
use App\Models\Employee\Employee;
use App\Models\Reservation\Reservation;
use App\Models\Reservation\ReservationRepositoryInRDB;
use App\Models\Table\Table;
use App\Utils\Env;
use App\Utils\PDOBuilder;

use function App\Utils\Tests\cleanTestDB;
use function Kahlan\beforeAll;
use function Kahlan\describe;
use function Kahlan\expect;
use function Kahlan\it;

describe('ReservationRepositoryInRDB', function () {
  $this->pdo = null;

  $this->repository = null;
  $this->pdo = null;

  beforeAll(function () {
    Env::initEnv(__DIR__ . '/../config/.env');
    date_default_timezone_set('America/Sao_Paulo');
    $this->pdo = PDOBuilder::build(true);
    cleanTestDB($this->pdo);
    $this->repository = new ReservationRepositoryInRDB($this->pdo);
  });

  it('should create the repository correctly', function () {
    expect($this->repository)->toBeAnInstanceOf('App\\Models\\Reservation\\ReservationRepositoryInRDB');
  });

  it('should create an reservation', function () {
    $endTime = new \DateTime();
    $endTime->modify('+2 hours');

    $reservation = $this->repository->create(
      new Reservation(
        new Table(1),
        new Client(1),
        new Employee(1),
        new \DateTime(),
        0,
        'active',
        $endTime
      )
    );

    expect($reservation)->toBeAnInstanceOf('App\\Models\\Reservation\\Reservation');
    expect($reservation->getStatus())->toBe('active');
    expect($reservation->getId())->toBeGreaterThan(0);
    expect($this->repository->get($reservation->getId()))->toBeAnInstanceOf('App\\Models\\Reservation\\Reservation');
  });

  it('should return all reservations', function () {
    $reservations = $this->repository->getAll(2, 0, false);
    expect(count($reservations['data']))->toBe(2);
  });

  it('should return a reservation', function () {
    $reservation = $this->repository->get(1);

    expect($reservation)->toBeAnInstanceOf('App\\Models\\Reservation\\Reservation');
    expect($reservation->getId())->toBe(1);
  });

  it('should update a reservation', function () {
    $reservation = new Reservation(
      new Table(),
      new Client(),
      new Employee()
    );
    $reservation->setId(1);
    $reservation->setStatus('inactive');

    $updatedReservation = $this->repository->update($reservation);

    expect($updatedReservation)->toBeAnInstanceOf('App\\Models\\Reservation\\Reservation');
    expect($updatedReservation->getStatus())->toBe('inactive');
  });

  it('should throw an exception when reservation doesn\'t exist', function () {
    expect(function () {
      $this->repository->get(0);
    })->toThrow(new ReservationRepositoryException('Reserva não encontrada', 404));
  });

  it('should throw an exception when updating a reservation that doesn\'t exist', function () {
    expect(function () {
      $reservation = new Reservation(
        new Table(),
        new Client(),
        new Employee()
      );
      $reservation->setId(-1);
      $reservation->setStatus('inactive');

      $this->repository->update($reservation);
    })->toThrow(new ReservationRepositoryException('Reserva não encontrada', 404));
  });

  it('should return the count of reservations correctly', function () {
    $reservations = $this->repository->getAll(10, 0, false);

    expect(count($reservations['data']))->toBe(3);
    expect($reservations['count'])->toBe(3);
  });

  it('should return one reservation', function () {
    $reservations = $this->repository->getAll(1, 0, false);

    expect(count($reservations['data']))->toBe(1);
    expect($reservations['count'])->toBe(3);
  });
});
