<?php

namespace Tests;

use App\Models\PaymentMethod\PaymentMethod;
use App\Models\PaymentMethod\PaymentMethodMapper;
use App\Utils\PDOBuilder;
use Slim\Psr7\Response;
use App\Presenters\PaymentMethodPresenter;
use App\Utils\Env;
use App\Views\V1\PaymentMethodViewInJson;

use function App\Utils\Tests\cleanTestDB;
use function Kahlan\beforeAll;
use function Kahlan\describe;
use function Kahlan\expect;
use function Kahlan\it;

describe('PaymentMethodPresenter', function () {
  $this->presenter = null;
  $this->view = null;
  $this->pdo = null;

  beforeAll(function () {
    Env::initEnv(__DIR__ . '/../config/.env');
    $instance = new PDOBuilder();
    $this->pdo = PDOBuilder::build(true);
    cleanTestDB($this->pdo);
    allow($instance)->toReceive('::build')->andReturn($this->pdo);
    $this->view = new PaymentMethodViewInJson();
    $this->presenter = new PaymentMethodPresenter($this->view);
  });

  it('should create the presenter correctly', function () {
    expect($this->presenter)->toBeAnInstanceOf('App\Presenters\PaymentMethodPresenter');
  });

  it('should set the response correctly', function () {
    $response = new Response();

    $paymentsMethods = [
      new PaymentMethod(1, 'Pix'),
      new PaymentMethod(2, 'Dinheiro'),
      new PaymentMethod(3, 'Cartão de crédito'),
      new PaymentMethod(4, 'Cartão de débito'),
    ];

    allow($this->view)->toReceive('respondWith')->andRun(function ($dtos) use ($response) {
      $response->getBody()->write(json_encode($dtos));
    });

    $this->presenter->getPaymentsMethod();

    $response->getBody()->rewind();

    expect($response->getBody()->getContents())->toBe(json_encode(PaymentMethodMapper::toDTOArray($paymentsMethods)));
  });
});
