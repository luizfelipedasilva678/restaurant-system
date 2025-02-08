<?php

namespace App\Views\V1;

use App\Contracts\Reports\ReportsView;
use App\Enums\HttpCodes;
use App\Exceptions\Reports\ReportsPresenterException;
use App\Exceptions\Reports\ReportsViewException;
use App\Presenters\ReportsPresenter;
use App\Views\Sanitizer;
use App\Views\Validator;
use App\Views\View;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class ReportsViewInJson extends View implements ReportsView
{
  private ReportsPresenter $presenter;
  private Validator $validator;
  private Sanitizer $sanitizer;

  public function __construct()
  {
    try {
      $this->validator = new Validator();
      $this->sanitizer = new Sanitizer();
      $this->presenter = new ReportsPresenter($this);
    } catch (ReportsPresenterException $exception) {
      throw new ReportsViewException($exception->getMessage(), $exception->getCode());
    }
  }

  /**
   * @param array<mixed>|object|string $data
   */
  public function respondWith($data): void
  {
    $this->response->getBody()->write(json_encode($data) ?: '');
  }

  public function handleGetSalesByCategory(Request $request, Response $response): Response
  {
    $this->request = $request;
    $this->response = $response;

    try {
      /** @var array<string, mixed> $params */
      $params = $request->getQueryParams();
      $sanitizedParams = $this->sanitizer->sanitize($params);
      $messages = $this->validator->validate([
        'initialDate' => [
          'type' => 'Date',
          'checkPastDate' => false,
          'dateFormat' => 'Y-m-d',
        ],
        'finalDate' => [
          'type' => 'Date',
          'checkPastDate' => false,
          'dateFormat' => 'Y-m-d',
        ],
      ],  $sanitizedParams);

      if (count($messages) > 0) {
        $this->respondWith(['messages' => $messages]);
        return $this->response->withStatus(HttpCodes::HTTP_BAD_REQUEST->value);
      }

      /**
       * @var array{
       *  initialDate: string,
       *  finalDate: string
       * } $sanitizedParams
       */

      $this->presenter->getSalesByCategory($sanitizedParams['initialDate'], $sanitizedParams['finalDate']);

      return $this->response->withStatus(HttpCodes::HTTP_OK->value);
    } catch (ReportsPresenterException $exception) {
      $this->respondWith(['message' => $exception->getMessage()]);
      return $this->response->withStatus($exception->getCode());
    } catch (\Exception $exception) {
      $this->respondWith(['message' => $exception->getMessage()]);
      return $this->response->withStatus(HttpCodes::HTTP_SERVER_ERROR->value);
    }
  }

  public function handleGetSalesByEmployee(Request $request, Response $response): Response
  {
    $this->request = $request;
    $this->response = $response;

    try {
      /** @var array<string, mixed> $params */
      $params = $request->getQueryParams();
      $sanitizedParams = $this->sanitizer->sanitize($params);
      $messages = $this->validator->validate([
        'initialDate' => [
          'type' => 'Date',
          'checkPastDate' => false,
          'dateFormat' => 'Y-m-d',
        ],
        'finalDate' => [
          'type' => 'Date',
          'checkPastDate' => false,
          'dateFormat' => 'Y-m-d',
        ],
      ],  $sanitizedParams);

      if (count($messages) > 0) {
        $this->respondWith(['messages' => $messages]);
        return $this->response->withStatus(HttpCodes::HTTP_BAD_REQUEST->value);
      }

      /**
       * @var array{
       *  initialDate: string,
       *  finalDate: string
       * } $sanitizedParams
       */

      $this->presenter->getSalesByEmployee($sanitizedParams['initialDate'], $sanitizedParams['finalDate']);

      return $this->response->withStatus(HttpCodes::HTTP_OK->value);
    } catch (ReportsPresenterException $exception) {
      $this->respondWith(['message' => $exception->getMessage()]);
      return $this->response->withStatus($exception->getCode());
    } catch (\Exception $exception) {
      $this->respondWith(['message' => $exception->getMessage()]);
      return $this->response->withStatus(HttpCodes::HTTP_SERVER_ERROR->value);
    }
  }

  public function handleGetSalesByPaymentMethod(Request $request, Response $response): Response
  {
    $this->request = $request;
    $this->response = $response;

    try {
      /** @var array<string, mixed> $params */
      $params = $request->getQueryParams();
      $sanitizedParams = $this->sanitizer->sanitize($params);
      $messages = $this->validator->validate([
        'initialDate' => [
          'type' => 'Date',
          'checkPastDate' => false,
          'dateFormat' => 'Y-m-d',
        ],
        'finalDate' => [
          'type' => 'Date',
          'checkPastDate' => false,
          'dateFormat' => 'Y-m-d',
        ],
      ],  $sanitizedParams);

      if (count($messages) > 0) {
        $this->respondWith(['messages' => $messages]);
        return $this->response->withStatus(HttpCodes::HTTP_BAD_REQUEST->value);
      }

      /**
       * @var array{
       *  initialDate: string,
       *  finalDate: string
       * } $sanitizedParams
       */

      $this->presenter->getSalesByPaymentMethod($sanitizedParams['initialDate'], $sanitizedParams['finalDate']);

      return $this->response->withStatus(HttpCodes::HTTP_OK->value);
    } catch (ReportsPresenterException $exception) {
      $this->respondWith(['message' => $exception->getMessage()]);
      return $this->response->withStatus($exception->getCode());
    } catch (\Exception $exception) {
      $this->respondWith(['message' => $exception->getMessage()]);
      return $this->response->withStatus(HttpCodes::HTTP_SERVER_ERROR->value);
    }
  }

  public function handleGetSalesByDay(Request $request, Response $response): Response
  {
    $this->request = $request;
    $this->response = $response;

    try {
      /** @var array<string, mixed> $params */
      $params = $request->getQueryParams();
      $sanitizedParams = $this->sanitizer->sanitize($params);
      $messages = $this->validator->validate([
        'initialDate' => [
          'type' => 'Date',
          'checkPastDate' => false,
          'dateFormat' => 'Y-m-d',
        ],
        'finalDate' => [
          'type' => 'Date',
          'checkPastDate' => false,
          'dateFormat' => 'Y-m-d',
        ],
      ],  $sanitizedParams);

      if (count($messages) > 0) {
        $this->respondWith(['messages' => $messages]);
        return $this->response->withStatus(HttpCodes::HTTP_BAD_REQUEST->value);
      }

      /**
       * @var array{
       *  initialDate: string,
       *  finalDate: string
       * } $sanitizedParams
       */

      $this->presenter->getSalesByDay($sanitizedParams['initialDate'], $sanitizedParams['finalDate']);

      return $this->response->withStatus(HttpCodes::HTTP_OK->value);
    } catch (ReportsPresenterException $exception) {
      $this->respondWith(['message' => $exception->getMessage()]);
      return $this->response->withStatus($exception->getCode());
    } catch (\Exception $exception) {
      $this->respondWith(['message' => $exception->getMessage()]);
      return $this->response->withStatus(HttpCodes::HTTP_SERVER_ERROR->value);
    }
  }
}
