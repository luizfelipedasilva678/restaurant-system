<?php

namespace Tests;

use App\Routes\ReportsRoutes;
use App\Utils\Env;
use App\Utils\PDOBuilder;
use PDOException;
use Slim\Factory\AppFactory;
use Slim\Routing\RouteCollectorProxy as Router;

use function Kahlan\describe;
use function Kahlan\expect;

describe('ReservationRoutes', function () {
  $this->container = null;
  $this->pdo = null;

  beforeAll(function () {
    Env::initEnv(__DIR__ . '/../config/.env');
    $instance = new PDOBuilder();
    $this->pdo = PDOBuilder::build(true);
    allow($instance)->toReceive('::build')->andReturn($this->pdo);
    $this->container = new ReportsRoutes();
  });

  it('should create the container correctly', function () {
    expect($this->container)->toBeAnInstanceOf('App\Routes\ReportsRoutes');
  });

  it('should create routes correctly', function () {
    $app = AppFactory::create();
    $container = $this->container;

    $app->group('/api', function (Router $routerApi) use ($container) {
      $routerApi->group('/v1', function (Router $routerV1) use ($container) {
        $container->init($routerV1, true);
      });
    });

    $routes = $app->getRouteCollector()->getRoutes();

    expect($routes)->toHaveLength(4);
  });

  it('should not create the routes if an error occurs', function () {
    $instance = new PDOBuilder();
    allow($instance)->toReceive('::build')->with()->andRun(function () { throw new PDOException(''); });

    $app = AppFactory::create();
    $container = $this->container;

    $app->group('/api', function (Router $routerApi) use ($container) {
      $routerApi->group('/v1', function (Router $routerV1) use ($container) {
        $container->init($routerV1, true);
      });
    });

    $routes = $app->getRouteCollector()->getRoutes();

    expect($routes)->toHaveLength(0);
  });
});
