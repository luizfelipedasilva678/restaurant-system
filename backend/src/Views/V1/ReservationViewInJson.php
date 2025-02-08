<?php

declare(strict_types=1);

namespace App\Views\V1;

use App\Contracts\Reservation\ReservationView;
use App\Enums\HttpCodes;
use App\Presenters\ReservationPresenter;
use App\Views\View;
use App\Models\Reservation\ReservationDTO;
use App\Exceptions\Reservation\ReservationPresenterException;
use App\Exceptions\Reservation\ReservationViewException;
use App\Views\Sanitizer;
use App\Views\Validator;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class ReservationViewInJson extends View implements ReservationView
{
  private ReservationPresenter $presenter;
  private Validator $validator;
  private Sanitizer $sanitizer;

  public function __construct()
  {
    try {
      $this->validator = new Validator();
      $this->sanitizer = new Sanitizer();
      $this->presenter = new ReservationPresenter($this);
    } catch (ReservationPresenterException $exception) {
      throw new ReservationViewException($exception->getMessage(), $exception->getCode());
    }
  }

  /**
   * @param array<string, mixed> $args
   */
  public function handleGetReservation(
    Request $request,
    Response $response,
    array $args
  ): Response {
    $this->request = $request;
    $this->response = $response;

    try {
      $messages = $this->validator->validate(
        [
          'id' => ['type' => 'numeric']
        ],
        $args
      );

      if (count($messages) > 0) {
        $this->respondWith(['messages' => $messages]);
        return $this->response->withStatus(HttpCodes::HTTP_BAD_REQUEST->value);
      }

      /** @var array{id: string} $args */
      $this->presenter->getReservation(intval($args['id']));

      return $this->response;
    } catch (ReservationPresenterException $exception) {
      $this->respondWith(['message' => $exception->getMessage()]);
      return $this->response->withStatus($exception->getCode());
    } catch (\Exception $exception) {
      $this->respondWith(['message' => $exception->getMessage()]);
      return $this->response->withStatus(HttpCodes::HTTP_SERVER_ERROR->value);
    }
  }

  public function handleGetReservations(Request $request, Response $response): Response
  {
    $this->request = $request;
    $this->response = $response;

    try {
      /** @var array<string, mixed> $params */
      $params = $request->getQueryParams();
      $sanitizedParams = $this->sanitizer->sanitize($params);
      $messages = $this->validator->validate([
        'inProgress' => [
          'type' => 'string',
          'required' => false,
          'enum' => ['true', 'false'],
        ],
        'page' => [
          'type' => 'numeric',
          'required' => false,
          'min' => 1,
        ],
        'perPage' => [
          'type' => 'numeric',
          'required' => false,
          'min' => 1,
        ],
        'currentAndLater' => [
          'type' => 'string',
          'required' => false,
          'enum' => ['true', 'false'],
        ],
        'initialDate' => [
          'type' => 'Date',
          'required' => false,
          'checkPastDate' => false,
          'dateFormat' => 'Y-m-d',
        ],
        'finalDate' => [
          'type' => 'Date',
          'required' => false,
          'checkPastDate' => false,
          'dateFormat' => 'Y-m-d',
        ],
      ], $sanitizedParams);

      if (count($messages) > 0) {
        $this->respondWith(['messages' => $messages]);
        return $this->response->withStatus(HttpCodes::HTTP_BAD_REQUEST->value);
      }

      /** @var array{page?: int, perPage?: int, currentAndLater?: "false"|"true", initialDate?: string, finalDate?: string, inProgress?: "false"|"true"} $sanitizedParams */
      $page = isset($sanitizedParams['page']) ? intval($sanitizedParams['page']) : 1;
      $perPage = isset($sanitizedParams['perPage']) ? intval($sanitizedParams['perPage']) : 10;
      $currentAndLater = isset($sanitizedParams['currentAndLater']) ? $sanitizedParams['currentAndLater'] === 'true' ? true : false : true;
      $initialDate = isset($sanitizedParams['initialDate']) ? $sanitizedParams['initialDate'] : null;
      $finalDate = isset($sanitizedParams['finalDate']) ? $sanitizedParams['finalDate'] : null;

      $this->presenter->getReservations(
        $page,
        $perPage,
        $currentAndLater,
        $initialDate,
        $finalDate
      );

      return $this->response;
    } catch (ReservationPresenterException $exception) {
      $this->respondWith(['message' => $exception->getMessage()]);
      return $this->response->withStatus($exception->getCode());
    } catch (\Exception $exception) {
      $this->respondWith(['message' => $exception->getMessage()]);
      return $this->response->withStatus(HttpCodes::HTTP_SERVER_ERROR->value);
    }
  }

  /**
   * @param array<string, string> $args
   */
  public function handleReservationUpdate(
    Request $request,
    Response $response,
    array $args
  ): Response {
    $this->request = $request;
    $this->response = $response;

    try {
      /** @var array<string, mixed> $payload */
      $payload = (array) $request->getParsedBody();
      /** @var array<string, mixed> $sanitizedPayload */
      $sanitizedPayload = $this->sanitizer->sanitize($payload);
      $messages = $this->validator->validate(
        [
          'status' => [
            'type' => 'string',
            'const' => 'inactive'
          ],
          'id' => [
            'type' => 'numeric'
          ]
        ],
        array_merge($sanitizedPayload, $args)
      );

      if (count($messages) > 0) {
        $this->respondWith(['messages' => $messages]);
        return $this->response->withStatus(HttpCodes::HTTP_BAD_REQUEST->value);
      }

      /** @var array<string, string> $sanitizedPayload */
      $this->presenter->updateReservation(
        new ReservationDTO(
          intval($args['id']),
          new \DateTime(),
          0,
          0,
          0,
          '',
          '',
          0,
          $sanitizedPayload['status']
        )
      );

      return $this->response;
    } catch (ReservationPresenterException $exception) {
      $this->respondWith(['message' => $exception->getMessage()]);
      return $this->response->withStatus($exception->getCode());
    } catch (\Exception $exception) {
      $this->respondWith(['message' => $exception->getMessage()]);
      return $this->response->withStatus(HttpCodes::HTTP_SERVER_ERROR->value);
    }
  }

  public function handleReservationCreation(
    Request $request,
    Response $response
  ): Response {
    $this->request = $request;
    $this->response = $response;

    try {
      /** @var array<string, mixed> $payload */
      $payload = (array) $request->getParsedBody();

      /** @var array{startTime: string, clientName: string, clientPhone: string, employeeId: number, tableId: number} $sanitizedPayload */
      $sanitizedPayload = $this->sanitizer->sanitize($payload);
      $messages = $this->validator->validate([
        'clientName' => [
          'type' => 'string',
          'length' => 5,
        ],
        'clientPhone' => [
          'type' => 'string',
        ],
        'startTime' => [
          'type' => 'Date',
        ],
        'employeeId' => [
          'type' => 'numeric',
        ],
        'tableId' => [
          'type' => 'numeric',
        ],
      ], $sanitizedPayload);

      if (count($messages) > 0) {
        $this->respondWith(['messages' => $messages]);
        return $this->response->withStatus(HttpCodes::HTTP_BAD_REQUEST->value);
      }

      $this->presenter->createReservation(
        new ReservationDTO(
          0,
          new \DateTime($sanitizedPayload['startTime']),
          intval($sanitizedPayload['tableId']),
          0,
          intval($sanitizedPayload['employeeId']),
          '',
          $sanitizedPayload['clientName'],
          0,
          'active',
          $sanitizedPayload['clientPhone'],
        )
      );

      return $this->response->withStatus(HttpCodes::HTTP_CREATED->value);
    } catch (ReservationPresenterException $exception) {
      $this->respondWith(['message' => $exception->getMessage()]);
      return $this->response->withStatus($exception->getCode());
    } catch (\Exception $exception) {
      $this->respondWith(['message' => $exception->getMessage()]);
      return $this->response->withStatus(HttpCodes::HTTP_SERVER_ERROR->value);
    }
  }

  /**
   * @param array<mixed>|object|string $data
   */
  public function respondWith($data): void
  {
    $this->response->getBody()->write(json_encode($data) ? json_encode($data) : '');
  }
}
