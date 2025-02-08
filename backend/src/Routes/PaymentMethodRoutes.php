<?php

declare(strict_types=1);

namespace App\Routes;

use App\Exceptions\PaymentMethod\PaymentMethodViewException;
use Slim\Routing\RouteCollectorProxy as Router;
use App\Views\V1\PaymentMethodViewInJson;

class PaymentMethodRoutes
{
  public function init(Router $router, bool $suppressLogs = false): void
  {
    try {
      $paymentMethodView = new PaymentMethodViewInJson();

      $router->get('/payments-methods', [$paymentMethodView, 'handleListPaymentsMethods']);
    } catch (PaymentMethodViewException $exception) {
      !$suppressLogs && error_log("\e[31m" . $exception->getMessage() . "\e[0m");
    } catch (\Exception $exception) {
      !$suppressLogs && error_log("\e[31m" . $exception->getMessage() . "\e[0m");
    }
  }
}
