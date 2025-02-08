<?php

namespace App\Views\V1;

use App\Views\View;
use App\Contracts\Item\ItemView;
use App\Enums\HttpCodes;
use App\Exceptions\Item\ItemPresenterException;
use App\Exceptions\Item\ItemViewException;
use App\Presenters\ItemPresenter;
use App\Views\Sanitizer;
use App\Views\Validator;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class ItemViewInJson extends View implements ItemView
{
  private ItemPresenter $presenter;
  private Validator $validator;
  private Sanitizer $sanitizer;

  public function __construct()
  {
    try {
      $this->validator = new Validator();
      $this->sanitizer = new Sanitizer();
      $this->presenter = new ItemPresenter($this);
    } catch (ItemPresenterException $exception) {
      throw new ItemViewException($exception->getMessage(), $exception->getCode());
    }
  }

  /**
   * @param array<mixed>|object|string $data
   */
  public function respondWith($data): void
  {
    $this->response->getBody()->write(json_encode($data) ?: '');
  }

  public function handleGetItems(Request $request, Response $response): Response
  {
    $this->request = $request;
    $this->response = $response;

    try {
      /** @var array<string, mixed> $params */
      $params = $request->getQueryParams();
      $sanitizedParams = $this->sanitizer->sanitize($params);
      $messages = $this->validator->validate([
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
      ], $sanitizedParams);

      if (count($messages) > 0) {
        $this->respondWith(['messages' => $messages]);
        return $this->response->withStatus(HttpCodes::HTTP_BAD_REQUEST->value);
      }

      /** @var array{page?: int, perPage?: int} $sanitizedParams */
      $page = isset($sanitizedParams['page']) ? intval($sanitizedParams['page']) : 1;
      $perPage = isset($sanitizedParams['perPage']) ? intval($sanitizedParams['perPage']) : 10;

      $this->presenter->getItems($page, $perPage);

      return $this->response;
    } catch (ItemPresenterException $exception) {
      $this->respondWith(['message' => $exception->getMessage()]);
      return $this->response->withStatus($exception->getCode());
    } catch (\Exception $exception) {
      $this->respondWith(['message' => $exception->getMessage()]);
      return $this->response->withStatus(HttpCodes::HTTP_SERVER_ERROR->value);
    }
  }
}
