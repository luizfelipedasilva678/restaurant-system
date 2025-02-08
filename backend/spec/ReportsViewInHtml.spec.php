<?php

use App\Utils\Env;
use App\Utils\PDOBuilder;
use App\Views\V1\ReportsViewInJson;
use Slim\Psr7\Response;

use function App\Utils\Tests\cleanTestDB;
use function App\Utils\Tests\createRequest;
use function Kahlan\expect;

describe('OrderViewInJson', function () {
  $this->view = null;
  $this->pdo = null;

  beforeAll(function () {
    Env::initEnv(__DIR__ . '/../config/.env');
    $instance = new PDOBuilder();
    $this->pdo = PDOBuilder::build(true);
    cleanTestDB($this->pdo, <<<'SQL'
      INSERT INTO TableOrder(table_id, client_id, status) VALUES (1, 1, 'open');
      INSERT INTO OrderItem(quantity, table_order_id, item_id) VALUES (2, 1, 1);
      INSERT INTO Bill(total, discount, creation_date, employee_id, payment_method_id, table_order_id) VALUES (10, 0, now(), 1, 1, 1);
    SQL);
    allow($instance)->toReceive('::build')->andReturn($this->pdo);
    $this->view = new ReportsViewInJson();
  });

  it('should return an error when initial date or final date is invalid', function () {
    $returnedResponse1 = $this->view->handleGetSalesByDay(
      createRequest('GET', '/reports/sales-by-day', ''),
      new Response(),
    );

    $returnedResponse2 = $this->view->handleGetSalesByPaymentMethod(
      createRequest('GET', '/reports/sales-by-payment-method', ''),
      new Response(),
    );

    $returnedResponse3 = $this->view->handleGetSalesByCategory(
      createRequest('GET', '/reports/sales-by-category', ''),
      new Response(),
    );

    $returnedResponse4 = $this->view->handleGetSalesByEmployee(
      createRequest('GET', '/reports/sales-by-table', ''),
      new Response(),
    );

    expect($returnedResponse1->getStatusCode())->toBe(400);
    expect($returnedResponse2->getStatusCode())->toBe(400);
    expect($returnedResponse3->getStatusCode())->toBe(400);
    expect($returnedResponse4->getStatusCode())->toBe(400);
  });

  it('should return data to day report correctly', function () {
    $startDate = new DateTime();
    $endDate = new DateTime();

    $this->view->handleGetSalesByDay(
      createRequest('GET', '/reports/sales-by-day', "initialDate={$startDate->format('Y-m-d')}&finalDate={$endDate->format('Y-m-d')}"),
      $response = new Response(),
    );

    $response->getBody()->rewind();

    expect(count((array)json_encode($response->getBody()->getContents())))->toBe(1);
  });

  it('should return data to payment method report correctly', function () {
    $startDate = new DateTime();
    $endDate = new DateTime();

    $this->view->handleGetSalesByPaymentMethod(
      createRequest('GET', '/reports/sales-by-payment-method', "initialDate={$startDate->format('Y-m-d')}&finalDate={$endDate->format('Y-m-d')}"),
      $response = new Response(),
    );

    $response->getBody()->rewind();

    expect(count((array)json_encode($response->getBody()->getContents())))->toBe(1);
  });

  it('should return data to category report correctly', function () {
    $startDate = new DateTime();
    $endDate = new DateTime();

    $this->view->handleGetSalesByCategory(
      createRequest('GET', '/reports/sales-by-category', "initialDate={$startDate->format('Y-m-d')}&finalDate={$endDate->format('Y-m-d')}"),
      $response = new Response(),
    );

    $response->getBody()->rewind();

    expect(count((array)json_encode($response->getBody()->getContents())))->toBe(1);
  });

  it('should return data to employee report correctly', function () {
    $startDate = new DateTime();
    $endDate = new DateTime();

    $this->view->handleGetSalesByEmployee(
      createRequest('GET', '/reports/sales-by-employee', "initialDate={$startDate->format('Y-m-d')}&finalDate={$endDate->format('Y-m-d')}"),
      $response = new Response(),
    );

    $response->getBody()->rewind();

    expect(count((array)json_encode($response->getBody()->getContents())))->toBe(1);
  });
});
