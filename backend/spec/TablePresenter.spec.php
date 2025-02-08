<?php

namespace Tests;

use App\Models\Table\Table;
use App\Presenters\TablePresenter;
use App\Utils\PDOBuilder;
use Slim\Psr7\Response;
use App\Models\Table\TableMapper;
use App\Utils\Env;
use App\Views\V1\TableViewInJson;

use function App\Utils\Tests\cleanTestDB;
use function Kahlan\beforeAll;
use function Kahlan\describe;
use function Kahlan\expect;
use function Kahlan\it;

describe('TablePresenter', function () {
  $this->presenter = null;
  $this->view = null;
  $this->pdo = null;

  beforeAll(function () {
    Env::initEnv(__DIR__ . '/../config/.env');
    $instance = new PDOBuilder();
    $this->pdo = PDOBuilder::build(true);
    cleanTestDB($this->pdo);
    allow($instance)->toReceive('::build')->andReturn($this->pdo);
    $this->view = new TableViewInJson();
    $this->presenter = new TablePresenter($this->view);
  });

  it('should create the presenter correctly', function () {
    expect($this->presenter)->toBeAnInstanceOf('App\Presenters\TablePresenter');
  });

  it('should set the response correctly', function () {
    $response = new Response();

    $tables = [
      Table::build(1, 1),
      Table::build(2, 2),
      Table::build(3, 3),
      Table::build(4, 4),
      Table::build(5, 5),
      Table::build(6, 6),
      Table::build(7, 7),
      Table::build(8, 8),
      Table::build(9, 9),
      Table::build(10, 10),
    ];

    allow($this->view)->toReceive('respondWith')->andRun(function ($dtos) use ($response) {
      $response->getBody()->write(json_encode($dtos));
    });

    $this->presenter->getTables();

    $response->getBody()->rewind();

    expect($response->getBody()->getContents())->toBe(json_encode(TableMapper::toDTOArray($tables)));
  });
});
