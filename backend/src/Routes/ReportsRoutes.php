<?php

declare(strict_types=1);

namespace App\Routes;

use App\Exceptions\PaymentMethod\PaymentMethodViewException;
use App\Views\V1\ReportsViewInJson;
use Slim\Routing\RouteCollectorProxy as Router;

class ReportsRoutes
{
  public function init(Router $router, bool $suppressLogs = false): void
  {
    try {
      $reportsView = new ReportsViewInJson();

      $router->get('/reports/sales-by-payment-method', [$reportsView, 'handleGetSalesByPaymentMethod']);
      $router->get('/reports/sales-by-employee', [$reportsView, 'handleGetSalesByEmployee']);
      $router->get('/reports/sales-by-day', [$reportsView, 'handleGetSalesByDay']);
      $router->get('/reports/sales-by-category', [$reportsView, 'handleGetSalesByCategory']);
    } catch (PaymentMethodViewException $exception) {
      !$suppressLogs && error_log("\e[31m" . $exception->getMessage() . "\e[0m");
    } catch (\Exception $exception) {
      !$suppressLogs && error_log("\e[31m" . $exception->getMessage() . "\e[0m");
    }
  }
}
