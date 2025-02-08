<?php

declare(strict_types=1);

namespace Tests;

use App\Models\Table\Table;
use App\Models\Table\TableMapper;
use App\Utils\Env;
use App\Utils\PDOBuilder;
use App\Views\V1\TableViewInJson;
use Slim\Psr7\Response;

use function App\Utils\Tests\createRequest;
use function App\Utils\Tests\cleanTestDB;
use function Kahlan\describe;
use function Kahlan\expect;
use function Kahlan\it;

describe('TableView', function () {
  $this->view = null;
  $this->pdo = null;
  $this->path = '/api/v1/tables';
  $this->method = 'GET';

  beforeAll(function () {
    Env::initEnv(__DIR__ . '/../config/.env');
    date_default_timezone_set('America/Sao_Paulo');
    $instance = new PDOBuilder();
    $this->pdo = PDOBuilder::build(true);
    cleanTestDB($this->pdo);
    allow($instance)->toReceive('::build')->andReturn($this->pdo);
    $this->view = new TableViewInJson();
  });

  it('should create the view correctly', function () {
    expect($this->view)->toBeAnInstanceOf('App\\Views\\V1\\TableViewInJson');
  });

  it('should return table 1 given that is reserved', function () {
    $date = new \DateTime();
    $date->modify('next wednesday');
    $date->setTime(11, 0, 0);
    $startDate = $date->format('Y-m-d H:i:s');

    $this->view->handleListTables(
      createRequest($this->method, $this->path, "startDate=$startDate"),
      $response = new Response(),
      []
    );

    $response->getBody()->rewind();

    expect($response->getBody()->getContents())->toBe(json_encode(
      TableMapper::toDTOArray([
        Table::build(1, 1),
      ])
    ));
  });

  it('should table 2 given that is reserved', function () {
    $date = new \DateTime();
    $date->modify('next thursday');
    $date->setTime(11, 05, 0);
    $startDate = $date->format('Y-m-d H:i:s');

    $this->view->handleListTables(
      createRequest($this->method, $this->path, "startDate=$startDate"),
      $response = new Response(),
      []
    );

    $response->getBody()->rewind();

    expect($response->getBody()->getContents())->toBe(json_encode(
      TableMapper::toDTOArray([
        Table::build(2, 2),
      ])
    ));
  });

  it('should return an error message with status 500 when something unexpected happens', function () {
    $request = createRequest($this->method, $this->path);

    allow($request)->toReceive('getQueryParams')->andRun(function () {
      throw new \Exception('Error', 500);
    });

    $newResponse = $this->view->handleListTables($request, $response = new Response(), []);

    $response->getBody()->rewind();

    expect(str_contains($response->getBody()->getContents(), 'message'))->toBe(true);

    expect($newResponse->getStatusCode())->toBe(500);
  });

  it('should respond with an error message when startDate param is invalid', function () {
    $newResponse = $this->view->handleListTables(
      createRequest($this->method, $this->path, 'startDate=invalidValue'),
      $response = new Response(),
      []
    );

    $response->getBody()->rewind();

    expect(str_contains($response->getBody()->getContents(), 'startDate'))->toBe(true);
    expect($newResponse->getStatusCode())->toBe(400);
  });
});
