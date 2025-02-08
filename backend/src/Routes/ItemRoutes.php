<?php

declare(strict_types=1);

namespace App\Routes;

use App\Exceptions\Item\ItemViewException;
use Slim\Routing\RouteCollectorProxy as Router;
use App\Views\V1\ItemViewInJson;

class ItemRoutes
{
  public function init(Router $router, bool $suppressLogs = false): void
  {
    try {
      $itemView = new ItemViewInJson();
      $router->get('/items', [$itemView, 'handleGetItems']);
    } catch (ItemViewException $exception) {
      !$suppressLogs && error_log("\e[31m" . $exception->getMessage() . "\e[0m");
    } catch (\Exception $exception) {
      !$suppressLogs && error_log("\e[31m" . $exception->getMessage() . "\e[0m");
    }
  }
}
