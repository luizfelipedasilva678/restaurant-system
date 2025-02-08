<?php

namespace App\Views\V1;

use App\Contracts\Table\TableView;
use App\Enums\HttpCodes;
use App\Exceptions\Table\TablePresenterException;
use App\Exceptions\Table\TableViewException;
use App\Presenters\TablePresenter;
use App\Views\Sanitizer;
use App\Views\Validator;
use App\Views\View;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class TableViewInJson extends View implements TableView
{
  private TablePresenter $presenter;
  private Validator $validator;
  private Sanitizer $sanitizer;

  public function __construct()
  {
    try {
      $this->validator = new Validator();
      $this->sanitizer = new Sanitizer();
      $this->presenter = new TablePresenter($this);
    } catch (TablePresenterException $exception) {
      throw new TableViewException($exception->getMessage(), $exception->getCode());
    }
  }

  /**
   * @param array<mixed>|object|string $data
   */
  public function respondWith($data): void
  {
    $this->response->getBody()->write(json_encode($data) ?: '');
  }

  public function handleListTables(Request $request, Response $response): Response
  {
    $this->request = $request;
    $this->response = $response;

    try {
      /** @var array<string, mixed> $params */
      $params = $request->getQueryParams();
      $sanitizedParams = $this->sanitizer->sanitize($params);
      $messages = $this->validator->validate(
        [
          'startDate' => [
            'type' => 'Date',
            'required' => false
          ],
        ],
        $sanitizedParams
      );

      if (count($messages) > 0) {
        $this->respondWith(['messages' => $messages]);
        return $this->response->withStatus(HttpCodes::HTTP_BAD_REQUEST->value);
      }

      /** @var array{startDate?: string} $sanitizedParams */
      $this->presenter->getTables(isset($sanitizedParams['startDate']) ? $sanitizedParams['startDate'] : null);

      return $this->response;
    } catch (TablePresenterException $exception) {
      $this->respondWith(['message' => $exception->getMessage()]);
      return $this->response->withStatus($exception->getCode());
    } catch (\Exception $exception) {
      $this->respondWith(['message' => $exception->getMessage()]);
      return $this->response->withStatus(HttpCodes::HTTP_SERVER_ERROR->value);
    }
  }
}
