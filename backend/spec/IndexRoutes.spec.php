<?php

declare(strict_types=1);

namespace Tests;

use App\Routes\IndexRoutes;
use Slim\Factory\AppFactory;
use Slim\Routing\RouteCollectorProxy as Router;

use function Kahlan\describe;
use function Kahlan\expect;

describe('IndexRoutes', function () {
  $this->container = null;
  $this->pdo = null;

  beforeAll(function () {
    $this->container = new IndexRoutes();
  });

  it('should create the container correctly', function () {
    expect($this->container)->toBeAnInstanceOf('App\Routes\IndexRoutes');
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

    expect($routes)->toHaveLength(1);
  });
});
