<?php

declare(strict_types=1);

namespace Tests;

use App\Models\PaymentMethod\PaymentMethodDTO;
use App\Utils\Env;
use App\Utils\PDOBuilder;
use App\Views\V1\PaymentMethodViewInJson;
use Slim\Psr7\Response;

use function App\Utils\Tests\createRequest;
use function App\Utils\Tests\cleanTestDB;
use function Kahlan\beforeAll;
use function Kahlan\describe;
use function Kahlan\expect;
use function Kahlan\it;

describe('PaymentMethodViewInJson', function () {
  $this->view = null;
  $this->pdo = null;
  $this->path = '/api/v1/payments-methods';
  $this->method = 'GET';

  beforeAll(function () {
    Env::initEnv(__DIR__ . '/../config/.env');
    $instance = new PDOBuilder();
    $this->pdo = PDOBuilder::build(true);
    cleanTestDB($this->pdo);
    allow($instance)->toReceive('::build')->andReturn($this->pdo);
    $this->view = new PaymentMethodViewInJson();
  });

  it('should create the view correctly', function () {
    expect($this->view)->toBeAnInstanceOf('App\\Views\\V1\\PaymentMethodViewInJson');
  });

  it('should get the payments methods correctly', function () {
    $this->view->handleListPaymentsMethods(
      createRequest($this->method, $this->path),
      $response = new Response(),
      []
    );

    $response->getBody()->rewind();

    expect($response->getBody()->getContents())->toBe(json_encode([
      new PaymentMethodDTO(1, 'Pix'),
      new PaymentMethodDTO(2, 'Dinheiro'),
      new PaymentMethodDTO(3, 'Cartão de crédito'),
      new PaymentMethodDTO(4, 'Cartão de débito'),
    ]));
  });
});
