<?php

namespace Tests;

use App\Enums\EmployeeType;
use App\Models\Auth\AuthDTO;
use App\Utils\Env;
use App\Utils\PDOBuilder;
use App\Utils\Session\SessionUtils;
use App\Views\V1\AuthViewInJson;
use Slim\Psr7\Response;

use function App\Utils\Tests\cleanTestDB;
use function App\Utils\Tests\createRequest;
use function Kahlan\describe;
use function Kahlan\expect;
use function Kahlan\it;

describe('AuthViewInJson', function () {
  $this->view = null;
  $this->pdo = null;
  $this->sessionUtils = new SessionUtils();

  beforeAll(function () {
    Env::initEnv(__DIR__ . '/../config/.env');
    Env::set('ENV', 'test');

    $instance = new PDOBuilder();

    $this->pdo = PDOBuilder::build(true);

    cleanTestDB($this->pdo);
    allow($instance)->toReceive('::build')->andReturn($this->pdo);

    $this->view = new AuthViewInJson();
  });

  it('should validate the body correctly', function () {
    $request = createRequest('POST', '/api/v1/auth/login');

    allow($request)->toReceive('getParsedBody')->andReturn([]);

    $newResponse = $this->view->login(
      $request,
      new Response(),
      []
    );

    $newResponse->getBody()->rewind();

    expect($newResponse->getStatusCode())->toBe(400);
    expect($newResponse->getBody()->getContents())->toBe(json_encode([
      'messages' => ['Campo login obrigatório', 'Campo password obrigatório']
    ]));
  });

  it('should show the message error when the credentials are invalid', function () {
    $request = createRequest('POST', '/api/v1/auth/login');

    allow($request)->toReceive('getParsedBody')->andReturn([
      'login' => 'user1',
      'password' => '1234'
    ]);

    $newResponse = $this->view->login(
      $request,
      new Response(),
      []
    );

    $newResponse->getBody()->rewind();

    expect($newResponse->getStatusCode())->toBe(400);
    expect($newResponse->getBody()->getContents())->toBe(json_encode([
      'message' => 'Usuário ou senha inválidos'
    ]));
  });

  it('should login correctly', function () {
    $request = createRequest('POST', '/api/v1/auth/login');

    allow($request)->toReceive('getParsedBody')->andReturn([
      'login' => 'user1',
      'password' => '123'
    ]);

    $newResponse = $this->view->login(
      $request,
      new Response(),
      []
    );

    $newResponse->getBody()->rewind();

    expect($newResponse->getStatusCode())->toBe(200);
    expect($_SESSION)->toEqual([
      'id' => 1,
      'name' => 'Rozella Cejka',
      'login' => 'user1',
      'userType' => 'attendant'
    ]);
    expect($newResponse->getBody()->getContents())->toBe(json_encode([
      'message' => 'sessão iniciada com sucesso'
    ]));
  });

  it('should logout correctly', function () {
    allow($this->sessionUtils)->toReceive('::destroySession')->andRun(function () {
      $_SESSION = [];
    });

    $request = createRequest('GET', '/api/v1/auth/logout');

    $newResponse = $this->view->logout(
      $request,
      new Response(),
      []
    );

    $newResponse->getBody()->rewind();

    expect(empty($_SESSION))->toBeTruthy();
    expect($newResponse->getBody()->getContents())->toBe(json_encode([
      'message' => 'sessão destruída com sucesso'
    ]));
  });

  it('should return null when some in the session in invalid', function () {
    $_SESSION = [];

    $request = createRequest('GET', '/api/v1/auth/logout');

    $newResponse = $this->view->getCurrentSession(
      $request,
      new Response(),
      []
    );

    $newResponse->getBody()->rewind();

    expect($newResponse->getBody()->getContents())->toBe(json_encode([
      'session' => null
    ]));
  });

  it('should get the current session correctly', function () {
    $_SESSION = [
      'id' => 1,
      'name' => 'test',
      'login' => 'user',
      'userType' => 'attendant'
    ];

    $request = createRequest('GET', '/api/v1/auth/logout');

    $newResponse = $this->view->getCurrentSession(
      $request,
      new Response(),
      []
    );

    $newResponse->getBody()->rewind();

    expect($newResponse->getBody()->getContents())->toBe(json_encode(
      new AuthDTO(
        1,
        'test',
        'user',
        EmployeeType::attendant
      )
    ));
  });
});
