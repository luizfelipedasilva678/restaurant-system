<?php

use App\Presenters\ReportsPresenter;
use App\Utils\Env;
use App\Utils\PDOBuilder;
use App\Views\V1\ReportsViewInJson;
use Slim\Psr7\Response;

use function App\Utils\Tests\cleanTestDB;
use function Kahlan\expect;

describe('ReportsPresenter', function () {
  $this->presenter = null;
  $this->pdo = null;
  $this->view = null;

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
    $this->presenter = new ReportsPresenter(
      $this->view
    );
  });

  it('should return the data to employee report correctly', function () {
    $response = new Response();
    $startDate = new DateTime();
    $endDate = new DateTime();

    allow($this->view)->toReceive('respondWith')->andRun(function ($data) use ($response) {
      $response->getBody()->write(
        json_encode(
          $data
        )
      );
    });

    $this->presenter->getSalesByEmployee($startDate->format('Y-m-d'), $endDate->format('Y-m-d'));

    $response->getBody()->rewind();

    $result = (array) json_decode($response->getBody()->getContents());

    expect(count($result))->toBe(1);
  });

  it('should return the data to day report correctly', function () {
    $response = new Response();
    $startDate = new DateTime();
    $endDate = new DateTime();

    allow($this->view)->toReceive('respondWith')->andRun(function ($data) use ($response) {
      $response->getBody()->write(
        json_encode(
          $data
        )
      );
    });

    $this->presenter->getSalesByDay($startDate->format('Y-m-d'), $endDate->format('Y-m-d'));

    $response->getBody()->rewind();

    $result = (array) json_decode($response->getBody()->getContents());

    expect(count($result))->toBe(1);
  });

  it('should return the data to payment method report correctly', function () {
    $response = new Response();
    $startDate = new DateTime();
    $endDate = new DateTime();

    allow($this->view)->toReceive('respondWith')->andRun(function ($data) use ($response) {
      $response->getBody()->write(
        json_encode(
          $data
        )
      );
    });

    $this->presenter->getSalesByPaymentMethod($startDate->format('Y-m-d'), $endDate->format('Y-m-d'));

    $response->getBody()->rewind();

    $result = (array) json_decode($response->getBody()->getContents());

    expect(count($result))->toBe(1);
  });

  it('should return the data to category report correctly', function () {
    $response = new Response();
    $startDate = new DateTime();
    $endDate = new DateTime();

    allow($this->view)->toReceive('respondWith')->andRun(function ($data) use ($response) {
      $response->getBody()->write(
        json_encode(
          $data
        )
      );
    });

    $this->presenter->getSalesByCategory($startDate->format('Y-m-d'), $endDate->format('Y-m-d'));

    $response->getBody()->rewind();

    $result = (array) json_decode($response->getBody()->getContents());

    expect(count($result))->toBe(1);
  });
});
