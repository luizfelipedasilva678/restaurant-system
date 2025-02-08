<?php

namespace Tests;

use App\Presenters\IndexPresenter;
use App\Views\V1\IndexViewInJson;
use Slim\Psr7\Response;

use function Kahlan\beforeAll;
use function Kahlan\describe;
use function Kahlan\expect;

describe('IndexPresenter', function () {
  $this->presenter = null;
  $this->view = null;

  beforeAll(function () {
    $this->view = new IndexViewInJson();
    $this->presenter = new IndexPresenter($this->view);
  });

  it('should create the presenter correctly', function () {
    expect($this->presenter)->toBeAnInstanceOf('App\Presenters\IndexPresenter');
  });

  it('should get data correctly', function () {
    $response = new Response();

    allow($this->view)->toReceive('respondWith')->andRun(function ($data) use ($response) {
      $response->getBody()->write(
        json_encode(
          $data
        )
      );
    });

    $this->presenter->getData();
    $response->getBody()->rewind();
    $responseBody = json_decode($response->getBody()->getContents());

    expect(str_contains($responseBody->employees, '/api/v1/employees'))->toBe(true);
    expect(str_contains($responseBody->tables, '/api/v1/tables'))->toBe(true);
    expect(str_contains($responseBody->reservations, '/api/v1/reservations'))->toBe(true);
  });
});
