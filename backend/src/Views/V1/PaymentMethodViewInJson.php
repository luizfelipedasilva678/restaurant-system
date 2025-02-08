<?php

namespace App\Views\V1;

use App\Contracts\PaymentMethod\PaymentMethodView;
use App\Enums\HttpCodes;
use App\Exceptions\PaymentMethod\PaymentMethodPresenterException;
use App\Exceptions\PaymentMethod\PaymentMethodViewException;
use App\Presenters\PaymentMethodPresenter;
use App\Views\View;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class PaymentMethodViewInJson extends View implements PaymentMethodView
{
  private PaymentMethodPresenter $presenter;

  public function __construct()
  {
    try {
      $this->presenter = new PaymentMethodPresenter($this);
    } catch (PaymentMethodPresenterException $exception) {
      throw new PaymentMethodViewException($exception->getMessage(), $exception->getCode());
    }
  }

  /**
   * @param array<mixed>|object|string $data
   */
  public function respondWith($data): void
  {
    $this->response->getBody()->write(json_encode($data) ?: '');
  }

  public function handleListPaymentsMethods(Request $request, Response $response): Response
  {
    $this->request = $request;
    $this->response = $response;

    try {
      $this->presenter->getPaymentsMethod();

      return $this->response;
    } catch (PaymentMethodPresenterException $exception) {
      $this->respondWith(['message' => $exception->getMessage()]);

      return $this->response->withStatus($exception->getCode());
    } catch (\Exception $exception) {
      $this->respondWith(['message' => $exception->getMessage()]);

      return $this->response->withStatus(HttpCodes::HTTP_SERVER_ERROR->value);
    }
  }
}
