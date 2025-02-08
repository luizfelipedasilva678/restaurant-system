<?php

namespace Tests;

use App\Enums\EmployeeType;
use App\Models\Employee\Employee;
use App\Presenters\EmployeePresenter;
use App\Utils\PDOBuilder;
use App\Models\Employee\EmployeeMapper;
use App\Utils\Env;
use App\Views\V1\EmployeeViewInJson;
use Slim\Psr7\Response;

use function App\Utils\Tests\cleanTestDB;
use function Kahlan\beforeAll;
use function Kahlan\describe;
use function Kahlan\expect;
use function Kahlan\it;

describe('EmployeePresenter', function () {
  $this->presenter = null;
  $this->view = null;
  $this->pdo = null;

  beforeAll(function () {
    Env::initEnv(__DIR__ . '/../config/.env');
    $instance = new PDOBuilder();
    $this->pdo = PDOBuilder::build(true);
    cleanTestDB($this->pdo);
    allow($instance)->toReceive('::build')->andReturn($this->pdo);
    $this->view = new EmployeeViewInJson();
    $this->presenter = new EmployeePresenter($this->view);
  });

  it('should create the presenter correctly', function () {
    expect($this->presenter)->toBeAnInstanceOf('App\Presenters\EmployeePresenter');
  });

  it('should set the response correctly', function () {
    $response = new Response();

    allow($this->view)->toReceive('respondWith')->andRun(function ($data) use ($response) {
      $response->getBody()->write(
        json_encode(
          $data
        )
      );
    });

    $this->presenter->getEmployees(1, 4);

    $response->getBody()->rewind();

    $dtos = EmployeeMapper::toDTOArray([
      Employee::build(1, 'Rozella Cejka', '', '', EmployeeType::attendant),
      Employee::build(2, 'Tamara Reidshaw', '', '', EmployeeType::attendant),
      Employee::build(3, 'Latrena Laughren', '', '', EmployeeType::attendant),
      Employee::build(4, 'Luiz Henrique', '', '', EmployeeType::manager)
    ]);

    expect($response->getBody()->getContents())->toBe(
      json_encode(
        [
          'data' => $dtos,
          'count' => 4
        ]
      )
    );
  });
});
