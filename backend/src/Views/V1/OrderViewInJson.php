<?php

namespace App\Views\V1;

use App\Contracts\Order\OrderView;
use App\Enums\HttpCodes;
use App\Exceptions\Order\OrderPresenterException;
use App\Exceptions\Order\OrderViewException;
use App\Models\Bill\BillDTO;
use App\Models\Order\OrderDTO;
use App\Presenters\OrderPresenter;
use App\Views\Sanitizer;
use App\Views\Validator;
use App\Views\View;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class OrderViewInJson extends View implements OrderView
{
  private OrderPresenter $presenter;
  private Validator $validator;
  private Sanitizer $sanitizer;

  public function __construct()
  {
    try {
      $this->validator = new Validator();
      $this->sanitizer = new Sanitizer();
      $this->presenter = new OrderPresenter($this);
    } catch (OrderPresenterException $exception) {
      throw new OrderViewException($exception->getMessage(), $exception->getCode());
    }
  }

  /**
   * @param array<mixed>|object|string $data
   */
  public function respondWith($data): void
  {
    $this->response->getBody()->write(json_encode($data) ?: '');
  }

  public function handleGetOrders(Request $request, Response $response): Response
  {
    $this->request = $request;
    $this->response = $response;

    try {
      $this->presenter->getOrders();

      return $this->response->withStatus(HttpCodes::HTTP_OK->value);
    } catch (OrderPresenterException $exception) {
      $this->respondWith(['message' => $exception->getMessage()]);
      return $this->response->withStatus($exception->getCode());
    } catch (\Exception $exception) {
      $this->respondWith(['message' => $exception->getMessage()]);
      return $this->response->withStatus(HttpCodes::HTTP_SERVER_ERROR->value);
    }
  }

  public function handleFulFillOrder(Request $request, Response $response): Response
  {
    $this->request = $request;
    $this->response = $response;

    try {
      /** @var array<string, mixed> $payload */
      $payload = (array) $request->getParsedBody();
      $sanitizedPayload = $this->sanitizer->sanitize($payload);

      $messages = $this->validator->validate(
        [
          'employeeId' => [
            'type' => 'numeric'
          ],
          'paymentMethodId' => [
            'type' => 'numeric'
          ],
          'orderId' => [
            'type' => 'numeric'
          ],
          'total' => [
            'type' => 'numeric'
          ],
          'discount' => [
            'type' => 'numeric'
          ]
        ],
        $sanitizedPayload
      );

      if (count($messages) > 0) {
        $this->respondWith(['messages' => $messages]);

        return $this->response->withStatus(HttpCodes::HTTP_BAD_REQUEST->value);
      }

      /** @var array{clientId: string, paymentMethodId: int, employeeId: int, orderId: int, total: float, discount: float} $sanitizedPayload */
      $this->presenter->fulFill(
        new BillDTO(
          $sanitizedPayload['paymentMethodId'],
          $sanitizedPayload['employeeId'],
          $sanitizedPayload['orderId'],
          $sanitizedPayload['total'],
          $sanitizedPayload['discount']
        )
      );

      return $this->response->withStatus(HttpCodes::HTTP_CREATED->value);
    } catch (OrderPresenterException $exception) {
      $this->respondWith(['message' => $exception->getMessage()]);
      return $this->response->withStatus($exception->getCode());
    } catch (\Exception $exception) {
      $this->respondWith(['message' => $exception->getMessage()]);
      return $this->response->withStatus(HttpCodes::HTTP_SERVER_ERROR->value);
    }
  }

  public function handleCreateOrder(Request $request, Response $response): Response
  {
    $this->request = $request;
    $this->response = $response;

    try {
      /** @var array<string, mixed> $payload */
      $payload = $request->getParsedBody();
      $sanitizedPayload = $this->sanitizer->sanitize($payload);

      $messages = $this->validator->validate(
        [
          'clientName' => [
            'type' => 'string',
            'length' => 5
          ],
          'tableId' => [
            'type' => 'numeric'
          ]
        ],
        $sanitizedPayload
      );

      if (count($messages) > 0) {
        $this->respondWith(['messages' => $messages]);
        return $this->response->withStatus(HttpCodes::HTTP_BAD_REQUEST->value);
      }

      /** @var array{clientName: string, tableId: string} $sanitizedPayload */
      $this->presenter->createOrder(new OrderDTO(0, 0, $sanitizedPayload['clientName'], intval($sanitizedPayload['tableId'])));

      return $this->response->withStatus(HttpCodes::HTTP_OK->value);
    } catch (OrderPresenterException $exception) {
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
  public function handleGetOrder(
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
      $id = $args['id'];

      $this->presenter->getOrder(intval($id));

      return $this->response->withStatus(HttpCodes::HTTP_OK->value);
    } catch (OrderPresenterException $exception) {
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
  public function handleAddItems(
    Request $request,
    Response $response,
    array $args
  ): Response {
    $this->request = $request;
    $this->response = $response;

    try {
      /** @var array<string, mixed> $payload */
      $payload = (array) $request->getParsedBody();
      $sanitizedPayload = $this->sanitizer->sanitize($payload);

      $messages = $this->validator->validate(
        [
          'id' => [
            'type' => 'numeric'
          ],
          'items' => [
            'type' => 'array',
            'itemsArray' => [
              'type' => 'object',
              'properties' => [
                'itemId' => [
                  'type' => 'numeric'
                ],
                'quantity' => [
                  'type' => 'numeric',
                  'min' => 1
                ]
              ]
            ]
          ]
        ],
        array_merge($sanitizedPayload, $args)
      );

      if (count($messages) > 0) {
        $this->respondWith(['messages' => $messages]);
        return $this->response->withStatus(HttpCodes::HTTP_BAD_REQUEST->value);
      }

      /**
       * @var array{id: int, itemId: int, quantity: int, price: float, description: string, category: string} $items
       */
      $items = [];

      /**
       * @var array{itemId: int, quantity: int} $item
       */
      foreach ($sanitizedPayload['items'] as $item) {
        /**
         * @var array{id: int, itemId: int, quantity: int, price: float, description: string, category: string} $items
         */
        $items[] = [
          'id' => 0,
          'itemId' => $item['itemId'],
          'quantity' => $item['quantity'],
          'price' => 0,
          'description' => '',
          'category' => '',
        ];
      }

      /** @var array{id: string} $args */
      $id = intval($args['id']);

      $orderDTO = new OrderDTO(
        $id,
        0,
        '',
        0,
        0,
        'open',
        $items
      );

      $this->presenter->addItems($orderDTO);

      return $this->response->withStatus(HttpCodes::HTTP_OK->value);
    } catch (OrderPresenterException $exception) {
      $this->respondWith(['message' => $exception->getMessage()]);
      return $this->response->withStatus($exception->getCode());
    } catch (\Exception $exception) {
      $this->respondWith(['message' => $exception->getMessage()]);
      return $this->response->withStatus(HttpCodes::HTTP_SERVER_ERROR->value);
    }
  }
}
