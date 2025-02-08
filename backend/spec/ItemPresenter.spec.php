<?php

declare(strict_types=1);

namespace Tests;

use App\Presenters\ItemPresenter;
use App\Utils\Env;
use App\Utils\PDOBuilder;
use App\Views\V1\ItemViewInJson;
use Slim\Psr7\Response;

use function App\Utils\Tests\cleanTestDB;
use function Kahlan\describe;
use function Kahlan\expect;

describe('ItemPresenter', function () {
  $this->presenter = null;
  $this->view = null;
  $this->pdo = null;

  beforeAll(function () {
    Env::initEnv(__DIR__ . '/../config/.env');
    $instance = new PDOBuilder();
    $this->pdo = PDOBuilder::build(true);
    cleanTestDB($this->pdo);
    allow($instance)->toReceive('::build')->andReturn($this->pdo);
    $this->view = new ItemViewInJson();
    $this->presenter = new ItemPresenter($this->view);
  });

  it('should create the presenter correctly', function () {
    expect($this->presenter)->toBeAnInstanceOf('App\Presenters\ItemPresenter');
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

    $this->presenter->getItems(1, 10);

    $response->getBody()->rewind();

    expect(str_contains($response->getBody()->getContents(), '8'))->toBe(
      true
    );
  });
});
