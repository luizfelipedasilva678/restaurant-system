<?php

declare(strict_types=1);

namespace Tests;

use App\Enums\EmployeeType;
use App\Models\Employee\Employee;
use App\Models\Employee\EmployeeMapper;
use App\Utils\Env;
use App\Utils\PDOBuilder;
use App\Views\V1\EmployeeViewInJson;
use Slim\Psr7\Response;

use function App\Utils\Tests\createRequest;
use function App\Utils\Tests\cleanTestDB;
use function Kahlan\beforeAll;
use function Kahlan\describe;
use function Kahlan\expect;
use function Kahlan\it;

describe('EmployeeView', function () {
  $this->view = null;
  $this->pdo = null;
  $this->path = '/api/v1/employees';
  $this->method = 'GET';

  beforeAll(function () {
    Env::initEnv(__DIR__ . '/../config/.env');
    $instance = new PDOBuilder();
    $this->pdo = PDOBuilder::build(true);
    cleanTestDB($this->pdo);
    allow($instance)->toReceive('::build')->andReturn($this->pdo);
    $this->view = new EmployeeViewInJson();
  });

  it('should create the view correctly', function () {
    expect($this->view)->toBeAnInstanceOf('App\\Views\\V1\\EmployeeViewInJson');
  });

  it('should get employees correctly based on the query params', function () {
    $this->view->handleListEmployees(
      createRequest($this->method, $this->path, 'page=1&perPage=1'),
      $response = new Response(),
      []
    );

    $response->getBody()->rewind();

    expect($response->getBody()->getContents())->toBe(json_encode([
      'data' => EmployeeMapper::toDTOArray(
        [
          Employee::build(1, 'Rozella Cejka', '', 'user1', EmployeeType::attendant)
        ]
      ),
      'count' => 4
    ]));

    $this->view->handleListEmployees(
      createRequest($this->method, $this->path, 'page=1&perPage=2'),
      $response = new Response(),
      []
    );

    $response->getBody()->rewind();

    expect($response->getBody()->getContents())->toEqual(json_encode([
      'data' => EmployeeMapper::toDTOArray([
        Employee::build(1, 'Rozella Cejka', '', 'user1', EmployeeType::attendant),
        Employee::build(2, 'Tamara Reidshaw', '', 'user2', EmployeeType::attendant)
      ]),
      'count' => 4
    ]));

    $this->view->handleListEmployees(
      createRequest($this->method, $this->path, 'page=1&perPage=2'),
      $response = new Response(),
      []
    );

    $response->getBody()->rewind();

    expect($response->getBody()->getContents())->toEqual(json_encode([
      'data' => EmployeeMapper::toDTOArray([
        Employee::build(1, 'Rozella Cejka', '', 'user1', EmployeeType::attendant),
        Employee::build(2, 'Tamara Reidshaw', '', 'user2', EmployeeType::attendant),
      ]),
      'count' => 4
    ]));
  });

  it('should return an error message with status 500 when something unexpected happens', function () {
    $request = createRequest($this->method, $this->path);

    allow($request)->toReceive('getQueryParams')->andRun(function () {
      throw new \Exception('Error', 500);
    });

    $newResponse = $this->view->handleListEmployees($request, $response = new Response(), []);

    $response->getBody()->rewind();

    expect(str_contains($response->getBody()->getContents(), 'message'))->toBe(true);

    expect($newResponse->getStatusCode())->toBe(500);
  });
});
