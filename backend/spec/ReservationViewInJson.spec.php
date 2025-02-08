<?php

namespace Tests;

use App\Utils\Env;
use App\Utils\PDOBuilder;
use App\Views\V1\ReservationViewInJson;
use Slim\Psr7\Response;

use function App\Utils\Tests\cleanTestDB;
use function App\Utils\Tests\createRequest;
use function App\Utils\Tests\isOutOfReservationHours;
use function App\Utils\Tests\isOutOfWorkingHours;
use function Kahlan\describe;
use function Kahlan\expect;
use function Kahlan\skipIf;

describe('ReservationView', function () {
  $this->view = null;
  $this->pdo = null;
  $this->path = '/api/v1/reservations';

  beforeAll(function () {
    Env::initEnv(__DIR__ . '/../config/.env');
    date_default_timezone_set('America/Sao_Paulo');
    $instance = new PDOBuilder();
    $this->pdo = PDOBuilder::build(true);
    cleanTestDB($this->pdo);
    allow($instance)->toReceive('::build')->andReturn($this->pdo);
    $this->view = new ReservationViewInJson();
  });

  it('should create the view correctly', function () {
    expect($this->view)->toBeAnInstanceOf('App\\Views\\V1\\ReservationViewInJson');
  });

  it('should return all reservations correctly', function () {
    $request = createRequest('GET', $this->path, 'page=1&perPage=2&currentAndLater=false');

    $this->view->handleGetReservations($request, $response = new Response());
    $response->getBody()->rewind();
    $responseContent = json_decode($response->getBody()->getContents());

    expect(count($responseContent->data))->toBe(2);
    expect($responseContent->count)->toBe(2);
  });

  it('should return status 400 given that the query params are invalid', function () {
    $request = createRequest('GET', $this->path, 'page=asfdsdf&perPage=invalidvalue&currentAndLater=ddd');

    $newResponse = $this->view->handleGetReservations($request, $response = new Response());
    $response->getBody()->rewind();

    $responseContent = json_decode($response->getBody()->getContents());

    expect($newResponse->getStatusCode())->toBe(400);
    expect(str_contains($responseContent->messages[0], 'page'))->toBe(true);
    expect(str_contains($responseContent->messages[1], 'perPage'))->toBe(true);
    expect(str_contains($responseContent->messages[2], 'currentAndLater'))->toBe(true);
  });

  it('should return the reservation correctly', function () {
    $request = createRequest('GET', $this->path);

    $newResponse = $this->view->handleGetReservation(
      $request,
      $response = new Response(),
      [
        'id' => '1'
      ]
    );

    $response->getBody()->rewind();

    expect($newResponse->getStatusCode())->toBe(200);
    expect(str_contains($newResponse->getBody()->getContents(), '"id":1'))->toBe(true);
  });

  it('should return status 400 given that id value is invalid', function () {
    $request = createRequest('GET', $this->path);

    $newResponse = $this->view->handleGetReservation(
      $request,
      $response = new Response(),
      [
        'id' => 'invalid'
      ]
    );

    $response->getBody()->rewind();

    expect($newResponse->getStatusCode())->toBe(400);
    expect(str_contains($newResponse->getBody()->getContents(), 'id'))->toBe(true);
  });

  it('should return status 404 given that the reservation does not exist', function () {
    $request = createRequest('GET', $this->path);

    $newResponse = $this->view->handleGetReservation(
      $request,
      $response = new Response(),
      [
        'id' => '100'
      ]
    );

    $response->getBody()->rewind();

    expect($newResponse->getStatusCode())->toBe(404);
  });

  it('should return an error given that the restaurant is closed', function () {
    skipIf(isOutOfWorkingHours() ? false : true);

    $request = createRequest('POST', $this->path);
    $startTime = new \DateTime();
    $startTime->modify('next saturday');
    $startTime->setTIme(13, 0, 0);

    allow($request)->toReceive('getParsedBody')->andReturn([
      'clientPhone' => '(22) 2222-2222',
      'clientName' => 'Pedro',
      'tableId' => 1,
      'employeeId' => 3,
      'startTime' => $startTime->format('Y-m-d H:i:s'),
    ]);

    $this->view->handleReservationCreation(
      $request,
      $response = new Response()
    );

    $response->getBody()->rewind();

    expect(str_contains($response->getBody()->getContents(), 'O restaurante'))->toBe(true);
  });

  it('should return an error given that the restaurant is not accepting new reservations', function () {
    skipIf(!isOutOfWorkingHours() && isOutOfReservationHours() ? false : true);

    $request = createRequest('POST', $this->path);
    $startTime = new \DateTime();
    $startTime->modify('next saturday');
    $startTime->setTIme(13, 0, 0);

    allow($request)->toReceive('getParsedBody')->andReturn([
      'clientPhone' => '(22) 2222-2222',
      'clientName' => 'Pedro',
      'tableId' => 1,
      'employeeId' => 3,
      'startTime' => $startTime->format('Y-m-d H:i:s'),
    ]);

    $this->view->handleReservationCreation(
      $request,
      $response = new Response()
    );

    $response->getBody()->rewind();
    $response->withHeader('Content-type', 'application/json; charset=utf-8');

    expect(str_contains($response->getBody()->getContents(), 'O restaurante'))->toBe(true);
  });

  it('should return an error given that the reservation time is out of working hours', function () {
    skipIf(!isOutOfWorkingHours() && !isOutOfReservationHours() ? false : true);

    $request = createRequest('POST', $this->path);
    $startTime = new \DateTime();
    $startTime->modify('next saturday');
    $startTime->setTIme(10, 0, 0);

    allow($request)->toReceive('getParsedBody')->andReturn([
      'clientPhone' => '(22) 2222-2222',
      'clientName' => 'Pedro',
      'tableId' => 1,
      'employeeId' => 3,
      'startTime' => $startTime->format('Y-m-d H:i:s'),
    ]);

    $this->view->handleReservationCreation(
      $request,
      $response = new Response()
    );

    $response->getBody()->rewind();

    expect(str_contains($response->getBody()->getContents(), 'funcionamento'))->toBe(true);
  });

  it('should return an error given that the body is invalid', function () {
    skipIf(!isOutOfWorkingHours() && !isOutOfReservationHours() ? false : true);

    $request = createRequest('POST', $this->path);
    $startTime = new \DateTime();
    $startTime->modify('next saturday');
    $startTime->setTIme(13, 0, 0);

    allow($request)->toReceive('getParsedBody')->andReturn([
      'clientPhone' => '(22) 2222-2222',
      'clientName' => 1123123,
      'tableId' => 'asdasd',
      'employeeId' => 'asdasd',
      'startTime' => $startTime->format('Y-m-d H:i:s'),
    ]);

    $newResponse = $this->view->handleReservationCreation(
      $request,
      $response = new Response()
    );

    $response->getBody()->rewind();

    expect($newResponse->getStatusCode())->toBe(400);
  });

  it('should return and error given that the only possible to update is "status" field', function () {
    $request = createRequest('PATCH', $this->path);

    allow($request)->toReceive('getParsedBody')->andReturn([
      'id' => 1,
      'clientName' => 'Pedro',
      'tableId' => 1,
      'employeeId' => 3,
      'startTime' => '2021-02-01 13:00:00',
      'clientPhone' => '(22) 2222-2222',
    ]);

    $newResponse = $this->view->handleReservationUpdate(
      $request,
      $response = new Response(),
      [
        'id' => '1'
      ]
    );

    $response->getBody()->rewind();

    expect(str_contains($response->getBody()->getContents(), 'status'))->toBe(true);
    expect($newResponse->getStatusCode())->toBe(400);
  });

  it('should return and error given that the only possible value to status is "inactive"', function () {
    $request = createRequest('PATCH', $this->path);

    allow($request)->toReceive('getParsedBody')->andReturn([
      'status' => 'abc'
    ]);

    $newResponse = $this->view->handleReservationUpdate(
      $request,
      $response = new Response(),
      [
        'id' => '1'
      ]
    );

    $response->getBody()->rewind();

    expect($newResponse->getStatusCode())->toBe(400);
    expect(str_contains($response->getBody()->getContents(), 'status'))->toBe(true);
  });

  it('should return an error given that table does not exist', function () {
    skipIf(!isOutOfWorkingHours() && !isOutOfReservationHours() ? false : true);

    $request = createRequest('POST', $this->path);
    $startTime = new \DateTime();
    $startTime->modify('next saturday');
    $startTime->setTIme(13, 0, 0);

    allow($request)->toReceive('getParsedBody')->andReturn([
      'clientPhone' => '(22) 2222-2222',
      'clientName' => 'Pedro',
      'tableId' => 10000,
      'employeeId' => 3,
      'startTime' => $startTime->format('Y-m-d H:i:s'),
    ]);

    $newResponse = $this->view->handleReservationCreation(
      $request,
      $response = new Response()
    );

    $response->getBody()->rewind();

    expect($newResponse->getStatusCode())->toBe(500);
    expect(str_contains($response->getBody()->getContents(), 'Erro'))->toBe(true);
  });

  it('should create a reservation correctly', function () {
    skipIf(!isOutOfWorkingHours() && !isOutOfReservationHours() ? false : true);

    $request = createRequest('POST', $this->path);
    $startTime = new \DateTime();
    $startTime->modify('next saturday');
    $startTime->setTIme(13, 0, 0);

    allow($request)->toReceive('getParsedBody')->andReturn([
      'clientPhone' => '(22) 2222-2222',
      'clientName' => 'Pedro',
      'tableId' => 1,
      'employeeId' => 3,
      'startTime' => $startTime->format('Y-m-d H:i:s'),
    ]);

    $this->view->handleReservationCreation(
      $request,
      $response = new Response()
    );

    $response->getBody()->rewind();

    expect(str_contains($response->getBody()->getContents(), '"id":3'))->toBe(true);
  });

  it('should update a reservation correctly', function () {
    $request = createRequest('PATCH', $this->path);

    allow($request)->toReceive('getParsedBody')->andReturn([
      'status' => 'inactive'
    ]);

    $newResponse = $this->view->handleReservationUpdate(
      $request,
      $response = new Response(),
      [
        'status' => 'inactive',
        'id' => '1'
      ]
    );

    $response->getBody()->rewind();

    expect($newResponse->getStatusCode())->toBe(200);
  });
});
