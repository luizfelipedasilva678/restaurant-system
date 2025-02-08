<?php

use App\Routes\AuthRoutes;
use App\Routes\EmployeeRoutes;
use App\Routes\IndexRoutes;
use App\Routes\ReservationRoutes;
use App\Routes\TableRoutes;
use App\Middlewares\AuthMiddleware;
use App\Middlewares\ContentTypeMiddleware;
use App\Middlewares\RoleMiddleware;
use App\Routes\ItemRoutes;
use App\Routes\OrderRoutes;
use App\Routes\PaymentMethodRoutes;
use App\Routes\ReportsRoutes;
use App\Utils\Env;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;
use Slim\Routing\RouteCollectorProxy as Router;

date_default_timezone_set('America/Sao_Paulo');

require_once __DIR__ . '/../vendor/autoload.php';

Env::initEnv(__DIR__ . '/../config/.env');

$indexRoutes = new IndexRoutes();
$employeeRoutes = new EmployeeRoutes();
$tableRoutes = new TableRoutes();
$reservationsRoutes = new ReservationRoutes();
$authRoutes = new AuthRoutes();
$itemRoutes = new ItemRoutes();
$orderRoutes = new OrderRoutes();
$paymentMethodRoutes = new PaymentMethodRoutes();
$reportsRoutes = new ReportsRoutes();

$IS_DEVELOPMENT = Env::get('ENV') === 'development';

$app = AppFactory::create();

$app->addRoutingMiddleware();
$app->addBodyParsingMiddleware();

$errorMiddleware = $app->addErrorMiddleware(
  $IS_DEVELOPMENT,
  $IS_DEVELOPMENT,
  $IS_DEVELOPMENT
);

$errorHandler = $errorMiddleware->getDefaultErrorHandler();
$errorHandler->forceContentType('application/json');

$app->add(ContentTypeMiddleware::class);
$app->add(AuthMiddleware::class);
$app->add(RoleMiddleware::class);

$app->add(function ($request, $handler) {
  $response = $handler->handle($request);

  return $response
    ->withHeader('Access-Control-Allow-Origin', implode(',', ['http://localhost:5173']))
    ->withHeader('Access-Control-Allow-Credentials', 'true')
    ->withHeader('Access-Control-Allow-Headers', 'X-Requested-With, Content-Type, Accept, Origin, Authorization')
    ->withHeader('Access-Control-Allow-Methods', 'GET,POST,PATCH,DELETE');
});

$app->group('/api', function (Router $routerApi) use (
  $indexRoutes,
  $employeeRoutes,
  $tableRoutes,
  $reservationsRoutes,
  $authRoutes,
  $itemRoutes,
  $orderRoutes,
  $paymentMethodRoutes,
  $reportsRoutes
) {
  $routerApi->group('/v1', function (Router $routerV1) use (
    $indexRoutes,
    $employeeRoutes,
    $tableRoutes,
    $reservationsRoutes,
    $authRoutes,
    $itemRoutes,
    $orderRoutes,
    $paymentMethodRoutes,
    $reportsRoutes
  ) {
    $indexRoutes->init($routerV1);
    $employeeRoutes->init($routerV1);
    $tableRoutes->init($routerV1);
    $reservationsRoutes->init($routerV1);
    $authRoutes->init($routerV1);
    $itemRoutes->init($routerV1);
    $orderRoutes->init($routerV1);
    $paymentMethodRoutes->init($routerV1);
    $reportsRoutes->init($routerV1);
  });

  $routerApi->get('', function (Request $_, Response $response) {
    $response->getBody()->write(json_encode(['v1' => Env::get('HOST_URL') . '/api/v1']));

    return $response;
  });
});

$app->get('/', function (Request $_, Response $response) {
  $response->getBody()->write(json_encode(['api' => Env::get('HOST_URL') . '/api']));

  return $response;
});

$app->run();
