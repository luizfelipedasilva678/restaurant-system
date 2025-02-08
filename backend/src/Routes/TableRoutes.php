<?php

declare(strict_types=1);

namespace App\Routes;

use App\Exceptions\Table\TableViewException;
use App\Views\V1\TableViewInJson;
use Slim\Routing\RouteCollectorProxy as Router;

class TableRoutes
{
  public function init(Router $router, bool $suppressLogs = false): void
  {
    try {
      $tableView = new TableViewInJson();
      $router->get('/tables', [$tableView, 'handleListTables']);
    } catch (TableViewException $exception) {
      !$suppressLogs && error_log("\e[31m" . $exception->getMessage() . "\e[0m");
    } catch (\Exception $exception) {
      !$suppressLogs && error_log("\e[31m" . $exception->getMessage() . "\e[0m");
    }
  }
}
