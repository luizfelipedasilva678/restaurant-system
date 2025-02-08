<?php

namespace Tests;

use App\Exceptions\Reservation\ReservationPresenterException;
use App\Models\Reservation\ReservationDTO;
use App\Presenters\ReservationPresenter;
use App\Utils\Env;
use App\Utils\PDOBuilder;
use App\Views\V1\ReservationViewInJson;
use Slim\Psr7\Response;

use function App\Utils\Tests\cleanTestDB;
use function App\Utils\Tests\isOutOfReservationHours;
use function App\Utils\Tests\isOutOfWorkingHours;
use function Kahlan\beforeAll;
use function Kahlan\describe;
use function Kahlan\expect;

describe('ReservationPresenter', function () {
  $this->presenter = null;
  $this->view = null;
  $this->pdo = null;

  beforeAll(function () {
    Env::initEnv(__DIR__ . '/../config/.env');
    $instance = new PDOBuilder();
    $this->pdo = PDOBuilder::build(true);
    cleanTestDB($this->pdo);
    allow($instance)->toReceive('::build')->andReturn($this->pdo);
    $this->view = new ReservationViewInJson();
    $this->presenter = new ReservationPresenter($this->view);
  });

  it('should create the presenter correctly', function () {
    expect($this->presenter)->toBeAnInstanceOf('App\Presenters\ReservationPresenter');
  });

  it('should set response with all reservations', function () {
    $response = new Response();

    allow($this->view)->toReceive('respondWith')->andRun(function ($data) use ($response) {
      $response->getBody()->write(
        json_encode(
          $data
        )
      );
    });

    $this->presenter->getReservations(1, 2, false);
    $response->getBody()->rewind();
    $responseBody = json_decode($response->getBody()->getContents());

    expect(count($responseBody->data))->toBe(2);
    expect($responseBody->count)->toBe(2);
  });

  it('should set response with one reservation', function () {
    $response = new Response();

    allow($this->view)->toReceive('respondWith')->andRun(function ($data) use ($response) {
      $response->getBody()->write(
        json_encode(
          $data
        )
      );
    });

    $this->presenter->getReservation(1);
    $response->getBody()->rewind();
    $responseBody = json_decode($response->getBody()->getContents());

    expect($responseBody->id)->toBe(1);
  });

  it('should return an error given that the restaurant is closed', function () {
    skipIf(isOutOfWorkingHours() ? false : true);

    expect(function () {
      $this->presenter->createReservation(new ReservationDTO());
    })->toThrow(new ReservationPresenterException('O restaurante está fechado', 400));
  });

  it('should return an error given that the restaurant is not accepting new reservations', function () {
    skipIf(!isOutOfWorkingHours() && isOutOfReservationHours() ? false : true);

    expect(function () {
      $this->presenter->createReservation(new ReservationDTO());
    })->toThrow(new ReservationPresenterException('O restaurante não está aceitando reservas no momento', 400));
  });

  it('should return an error given that the reservation time is out of working hours', function () {
    skipIf(!isOutOfWorkingHours() && !isOutOfReservationHours() ? false : true);

    $startTime = new \DateTime();
    $startTime->setTime(6, 0, 0);
    $exceptionMessage = '';

    try {
      $this->presenter->createReservation(new ReservationDTO(0, $startTime));
    } catch (ReservationPresenterException $e) {
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

    expect(function () use ($startDate) {
      $this->presenter->createReservation(
        new ReservationDTO(
          0,
          $startDate,
          1,
          1,
          1,
          '',
          'João',
          0,
          'active'
        )
      );
    })->toThrow(new ReservationPresenterException('Mesa indisponível', 400));
  });
});
