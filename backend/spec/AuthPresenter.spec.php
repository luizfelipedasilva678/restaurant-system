<?php

namespace Tests;

use App\Exceptions\Auth\AuthPresenterException;
use App\Presenters\AuthPresenter;
use App\Utils\PDOBuilder;
use App\Utils\Env;
use App\Views\V1\AuthViewInJson;
use Slim\Psr7\Response;

use function App\Utils\Tests\cleanTestDB;
use function Kahlan\beforeAll;
use function Kahlan\describe;
use function Kahlan\expect;

describe('AuthPresenter', function () {
  $this->presenter = null;
  $this->view = null;
  $this->pdo = null;

  beforeAll(function () {
    Env::initEnv(__DIR__ . '/../config/.env');
    Env::set('ENV', 'test');

    $instance = new PDOBuilder();
    $this->pdo = PDOBuilder::build(true);
    cleanTestDB($this->pdo);
    allow($instance)->toReceive('::build')->andReturn($this->pdo);
    $this->view = new AuthViewInJson();
    $this->presenter = new AuthPresenter($this->view);
  });

  it('should create the presenter correctly', function () {
    expect($this->presenter)->toBeAnInstanceOf('App\Presenters\AuthPresenter');
  });

  it('should set the login response correctly', function () {
    $response = new Response();

    allow($this->view)->toReceive('respondWith')->andRun(function ($data) use ($response) {
      $response->getBody()->write(
        json_encode(
          $data
        )
      );
    });

    $this->presenter->login('user1', '123');

    $response->getBody()->rewind();

    expect($response->getBody()->getContents())->toBe(json_encode([
      'message' => 'sessão iniciada com sucesso'
    ]));
  });

  it('should throw a exception when the employee credentials are incorrectly', function () {
    $response = new Response();

    allow($this->view)->toReceive('respondWith')->andRun(function ($data) use ($response) {
      $response->getBody()->write(
        json_encode(
          $data
        )
      );
    });

    expect(function () {
      $this->presenter->login('user1', '1243');
    })->toThrow(new AuthPresenterException('Usuário ou senha inválidos', 400));
  });
});
