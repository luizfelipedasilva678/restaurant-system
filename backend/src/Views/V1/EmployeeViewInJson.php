<?php

namespace App\Views\V1;

use App\Contracts\Employee\EmployeeView;
use App\Enums\HttpCodes;
use App\Exceptions\Employee\EmployeePresenterException;
use App\Exceptions\Employee\EmployeeViewException;
use App\Presenters\EmployeePresenter;
use App\Views\Sanitizer;
use App\Views\Validator;
use App\Views\View;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class EmployeeViewInJson extends View implements EmployeeView
{
  private EmployeePresenter $presenter;
  private Validator $validator;
  private Sanitizer $sanitizer;

  public function __construct()
  {
    try {
      $this->validator = new Validator();
      $this->sanitizer = new Sanitizer();
      $this->presenter = new EmployeePresenter($this);
    } catch (EmployeePresenterException $exception) {
      throw new EmployeeViewException($exception->getMessage(), $exception->getCode());
    }
  }

  /**
   * @param array<mixed>|object|string $data
   */
  public function respondWith($data): void
  {
    $this->response->getBody()->write(json_encode($data) ?: '');
  }

  public function handleListEmployees(Request $request, Response $response): Response
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

      $this->presenter->getEmployees($page, $perPage);

      return $this->response;
    } catch (EmployeePresenterException $exception) {
      $this->respondWith(['message' => $exception->getMessage()]);
      return $this->response->withStatus($exception->getCode());
    } catch (\Exception $exception) {
      $this->respondWith(['message' => $exception->getMessage()]);
      return $this->response->withStatus(HttpCodes::HTTP_SERVER_ERROR->value);
    }
  }
}
