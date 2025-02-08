<?php

declare(strict_types=1);

namespace App\Routes;

use App\Exceptions\Employee\EmployeeViewException;
use App\Views\V1\EmployeeViewInJson;
use Slim\Routing\RouteCollectorProxy as Router;

class EmployeeRoutes
{
  public function init(Router $router, bool $suppressLogs = false): void
  {
    try {
      $employeeView = new EmployeeViewInJson();
      $router->get('/employees', [$employeeView, 'handleListEmployees']);
    } catch (EmployeeViewException $exception) {
      !$suppressLogs && error_log("\e[31m" . $exception->getMessage() . "\e[0m");
    } catch (\Exception $exception) {
      !$suppressLogs && error_log("\e[31m" . $exception->getMessage() . "\e[0m");
    }
  }
}
