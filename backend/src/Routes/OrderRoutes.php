<?php

declare(strict_types=1);

namespace App\Routes;

use App\Exceptions\Order\OrderViewException;
use Slim\Routing\RouteCollectorProxy as Router;
use App\Views\V1\OrderViewInJson;

class OrderRoutes
{
  public function init(Router $router, bool $suppressLogs = false): void
  {
    try {
      $orderView = new OrderViewInJson();
      $router->get('/orders/{id}', [$orderView, 'handleGetOrder']);
      $router->post('/orders/fulfill', [$orderView, 'handleFulFillOrder']);
      $router->post('/orders/{id}/items', [$orderView, 'handleAddItems']);
      $router->post('/orders', [$orderView, 'handleCreateOrder']);
      $router->get('/orders', [$orderView, 'handleGetOrders']);
    } catch (OrderViewException $exception) {
      !$suppressLogs && error_log("\e[31m" . $exception->getMessage() . "\e[0m");
    } catch (\Exception $exception) {
      !$suppressLogs && error_log("\e[31m" . $exception->getMessage() . "\e[0m");
    }
  }
}
