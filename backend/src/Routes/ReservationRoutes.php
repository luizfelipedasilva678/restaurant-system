<?php

declare(strict_types=1);

namespace App\Routes;

use App\Exceptions\Reservation\ReservationViewException;
use App\Views\V1\ReservationViewInJson;
use Slim\Routing\RouteCollectorProxy as Router;

class ReservationRoutes
{
  public function init(Router $router, bool $suppressLogs = false): void
  {
    try {
      $reservationView = new ReservationViewInJson();
      $router->get('/reservations', [$reservationView, 'handleGetReservations']);
      $router->post('/reservations', [$reservationView, 'handleReservationCreation']);
      $router->get('/reservations/{id}', [$reservationView, 'handleGetReservation']);
      $router->patch('/reservations/{id}', [$reservationView, 'handleReservationUpdate']);
    } catch (ReservationViewException $exception) {
      !$suppressLogs && error_log("\e[31m" . $exception->getMessage() . "\e[0m");
    } catch (\Exception $exception) {
      !$suppressLogs && error_log("\e[31m" . $exception->getMessage() . "\e[0m");
    }
  }
}
