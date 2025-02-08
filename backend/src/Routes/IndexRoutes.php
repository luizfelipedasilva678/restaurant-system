<?php

declare(strict_types=1);

namespace App\Routes;

use App\Views\V1\IndexViewInJson;
use Slim\Routing\RouteCollectorProxy as Router;

class IndexRoutes
{
  public function init(Router $router): void
  {
    $indexView = new IndexViewInJson();
    $router->get('', [$indexView, 'handleIndex']);
  }
}
