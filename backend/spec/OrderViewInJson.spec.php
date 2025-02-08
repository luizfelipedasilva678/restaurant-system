<?php

declare(strict_types=1);

use App\Utils\Env;
use App\Utils\PDOBuilder;
use App\Views\V1\OrderViewInJson;
use Slim\Psr7\Response;

use function App\Utils\Tests\cleanTestDB;
use function App\Utils\Tests\createRequest;
use function Kahlan\describe;
use function Kahlan\expect;

describe('OrderViewInJson', function () {
  $this->view = null;
  $this->pdo = null;

  beforeAll(function () {
    Env::initEnv(__DIR__ . '/../config/.env');
    $instance = new PDOBuilder();
    $this->pdo = PDOBuilder::build(true);
    cleanTestDB($this->pdo, <<<SQL
      INSERT INTO Reservation (start_time, end_time, client_id, employee_id, restaurant_table_id, status)
      SELECT now(),
      DATE_ADD(now(), INTERVAL 2 HOUR),
      1, 1, 1, 'active';
    SQL);
    allow($instance)->toReceive('::build')->andReturn($this->pdo);
    $this->view = new OrderViewInJson();
  });

  it('should create the view correctly', function () {
    expect($this->view)->toBeAnInstanceOf('App\\Views\\V1\\OrderViewInJson');
  });

  it('should return that that order was not found', function () {
    $this->view->handleGetOrder(
      createRequest('GET', '/orders'),
      $response = new Response(),
      ['id' => 1]
    );

    $response->getBody()->rewind();

    expect($response->getBody()->getContents())->toBe(json_encode([
      'message' => 'Pedido não encontrado'
    ]));
  });

  it('should return an error given that required fields are missing', function () {
    $request = createRequest('POST', '/orders/items');

    allow($request)->toReceive('getParsedBody')->andReturn([
      'items' => [
        [
          'quantity' => 1
        ],
      ]
    ]);

    $returnedResponse = $this->view->handleAddItems(
      $request,
      $response = new Response(),
      ['id' => 1]
    );

    $response->getBody()->rewind();

    expect($returnedResponse->getStatusCode())->toBe(400);
    expect(str_contains($response->getBody()->getContents(), 'itemId'))->toBe(true);
  });

  it('should create order correctly', function () {
    $request = createRequest('POST', '/orders');

    allow($request)->toReceive('getParsedBody')->andReturn([
      'clientName' => 'Teste',
      'tableId' => 6
    ]);

    $returnedResponse = $this->view->handleCreateOrder(
      $request,
      $response = new Response()
    );

    $response->getBody()->rewind();

    expect($returnedResponse->getStatusCode())->toBe(200);
    expect($response->getBody()->getContents())->toBe(json_encode([
      'message' => 'Pedido criado com sucesso'
    ]));
  });

  it('should add items to the order correctly', function () {
    $request = createRequest('POST', '/orders/items');

    allow($request)->toReceive('getParsedBody')->andReturn([
      'items' => [
        [
          'itemId' => 1,
          'quantity' => 1
        ],
      ]
    ]);

    $returnedResponse = $this->view->handleAddItems(
      $request,
      $response = new Response(),
      ['id' => 1]
    );

    $response->getBody()->rewind();

    expect($returnedResponse->getStatusCode())->toBe(200);
    expect(str_contains($returnedResponse->getBody()->getContents(), 'sucesso'))->toBe(true);
  });

  it('should return the order correctly', function () {
    $request = createRequest('GET', '/orders');

    $returnedResponse = $this->view->handleGetOrder(
      $request,
      $response = new Response(),
      ['id' => 1]
    );

    $response->getBody()->rewind();

    expect($returnedResponse->getStatusCode())->toBe(200);
    expect(str_contains($response->getBody()->getContents(), 'items'))->toBe(true);
  });

  it('should fulfill the order correctly', function () {
    $request = createRequest('POST', '/orders/fulfill');

    allow($request)->toReceive('getParsedBody')->andReturn([
      'employeeId' => 1,
      'paymentMethodId' => 1,
      'orderId' => 1,
      'total' => 10,
      'discount' => 0
    ]);

    $returnedResponse = $this->view->handleFulfillOrder(
      $request,
      $response = new Response()
    );

    $response->getBody()->rewind();

    expect($returnedResponse->getStatusCode())->toBe(201);
    expect(str_contains($returnedResponse->getBody()->getContents(), 'sucesso'))->toBe(true);
  });

  it('should return an error given the required fulfill fields are missing', function () {
    $request = createRequest('POST', '/orders/fulfill');

    allow($request)->toReceive('getParsedBody')->andReturn([
      'paymentMethodId' => 1,
      'orderId' => 1,
      'total' => 10,
      'discount' => 0
    ]);

    $returnedResponse = $this->view->handleFulfillOrder(
      $request,
      $response = new Response()
    );

    $response->getBody()->rewind();

    expect($returnedResponse->getStatusCode())->toBe(400);
    expect(str_contains($returnedResponse->getBody()->getContents(), 'Campo employeeId'))->toBe(true);
  });
});
