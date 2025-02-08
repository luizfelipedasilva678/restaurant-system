<?php

declare(strict_types=1);

namespace App\Routes;

use App\Exceptions\Auth\AuthViewException;
use App\Views\V1\AuthViewInJson;
use Slim\Routing\RouteCollectorProxy as Router;

class AuthRoutes
{
  public function init(Router $router, bool $suppressLogs = false): void
  {
    try {
      $authView = new AuthViewInJson();
      $router->post('/auth/login', [$authView, 'login']);
      $router->get('/auth/logout', [$authView, 'logout']);
      $router->get('/auth/session', [$authView, 'getCurrentSession']);
    } catch (AuthViewException $exception) {
      !$suppressLogs && error_log("\e[31m" . $exception->getMessage() . "\e[0m");
    } catch (\Exception $exception) {
      !$suppressLogs && error_log("\e[31m" . $exception->getMessage() . "\e[0m");
    }
  }
}
