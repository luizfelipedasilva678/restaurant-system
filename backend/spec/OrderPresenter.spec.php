<?php

declare(strict_types=1);

namespace Tests;

use App\Exceptions\Order\OrderPresenterException;
use App\Models\Bill\BillDTO;
use App\Models\Order\OrderDTO;
use App\Presenters\OrderPresenter;
use App\Utils\Env;
use App\Utils\PDOBuilder;
use App\Views\V1\OrderViewInJson;
use Slim\Psr7\Response;

use function App\Utils\Tests\cleanTestDB;
use function Kahlan\describe;
use function Kahlan\expect;

describe('OrderPresenter', function () {
  $this->presenter = null;
  $this->view = null;
  $this->pdo = null;

  beforeAll(function () {
    Env::initEnv(__DIR__ . '/../config/.env');
    $instance = new PDOBuilder();
    $this->pdo = PDOBuilder::build(true);
    cleanTestDB($this->pdo);
    allow($instance)->toReceive('::build')->andReturn($this->pdo);
    $this->view = new OrderViewInJson();
    $this->presenter = new OrderPresenter($this->view);
  });

  it('should create the presenter correctly', function () {
    expect($this->presenter)->toBeAnInstanceOf('App\Presenters\OrderPresenter');
  });

  it('should throw an exception when user doesnt have an order', function () {
    expect(function () {
      $this->presenter->getOrder(1);
    })->toThrow(new OrderPresenterException('Pedido não encontrado', 404));
  });

  it('should throw an exception when order doesnt exist', function () {
    expect(function () {
      $response = new Response();

      allow($this->view)->toReceive('respondWith')->andRun(function ($data) use ($response) {
        $response->getBody()->write(
          json_encode(
            $data
          )
        );
      });

      $this->presenter->addItems(
        new OrderDTO(
          0,
          0,
          '',
          0,
          0,
          'open',
          [
            [
              'id' => 0,
              'itemId' => 1,
              'quantity' => 3,
              'price' => 0,
              'description' => '',
              'category' => '',
            ]
          ]
        )
      );
    })->toThrow(new OrderPresenterException('Pedido não encontrado', 404));
  });

  it('should create the order correctly', function () {
    $response = new Response();

    allow($this->view)->toReceive('respondWith')->andRun(function ($data) use ($response) {
      $response->getBody()->write(
        json_encode(
          $data
        )
      );
    });

    $this->presenter->createOrder(
      new OrderDTO(
        0,
        1,
        'Teste',
        3,
        0,
        'open'
      )
    );

    $response->getBody()->rewind();

    expect($response->getBody()->getContents())->toEqual(json_encode([
      'message' => 'Pedido criado com sucesso'
    ]));
  });

  it('should add items to the order correctly', function () {
    $response = new Response();

    allow($this->view)->toReceive('respondWith')->andRun(function ($data) use ($response) {
      $response->getBody()->write(
        json_encode(
          $data
        )
      );
    });

    $this->presenter->addItems(
      new OrderDTO(
        1,
        0,
        '',
        0,
        0,
        'open',
        [
          [
            'id' => 0,
            'itemId' => 1,
            'quantity' => 3,
            'price' => 0,
            'description' => '',
            'category' => '',
          ]
        ]
      )
    );

    $response->getBody()->rewind();

    expect($response->getBody()->getContents())->toEqual(json_encode([
      'message' => 'Itens adicionados com sucesso'
    ]));
  });

  it('should throw and exception if order does not exists', function () {
    expect(function () {
      $this->presenter->fulFill(new BillDTO(1, 1, 99, 10, 0));
    })->toThrow(new OrderPresenterException('Pedido não encontrado', 404));
  });
});
