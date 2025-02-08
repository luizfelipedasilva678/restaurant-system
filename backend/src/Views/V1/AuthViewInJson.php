<?php

namespace App\Views\V1;

use App\Contracts\Auth\AuthView;
use App\Enums\EmployeeType;
use App\Enums\HttpCodes;
use App\Exceptions\Auth\AuthPresenterException;
use App\Exceptions\Auth\AuthViewException;
use App\Models\Auth\AuthDTO;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Presenters\AuthPresenter;
use App\Utils\Session\SessionUtils;
use App\Views\Sanitizer;
use App\Views\Validator;
use App\Views\View;

class AuthViewInJson extends View implements AuthView
{
  private AuthPresenter $presenter;
  private Validator $validator;
  private Sanitizer $sanitizer;

  public function __construct()
  {
    try {
      $this->validator = new Validator();
      $this->sanitizer = new Sanitizer();
      $this->presenter = new AuthPresenter($this);
    } catch (AuthPresenterException $exception) {
      throw new AuthViewException($exception->getMessage(), $exception->getCode());
    }
  }

  /**
   * @param array<mixed>|object|string $data
   */
  public function respondWith($data): void
  {
    $this->response->getBody()->write(json_encode($data) ?: '');
  }

  /**
   * @param array{id: int|string, name: string, login: string, userType: string} $userData
   */
  public function updateSession($userData): void
  {
    SessionUtils::startSession();

    $_SESSION['id'] = $userData['id'];
    $_SESSION['name'] = $userData['name'];
    $_SESSION['login'] = $userData['login'];
    $_SESSION['userType'] = $userData['userType'];
  }

  public function destroySession(): void
  {
    SessionUtils::destroySession();
  }

  public function getCurrentSession(
    Request $request,
    Response $response
  ): Response {
    SessionUtils::startSession();

    $this->request = $request;
    $this->response = $response;

    if (!empty($_SESSION)) {
      /**
       * @var array{id: int, name: string, login: string, userType: string} $session;
       */
      $session = $_SESSION;

      $this->respondWith(
        new AuthDTO(
          $session['id'],
          $session['name'],
          $session['login'],
          EmployeeType::from($session['userType'])
        )
      );

      return $this->response->withStatus(HttpCodes::HTTP_OK->value);
    }

    $this->respondWith(
      [
        'session' => null
      ]
    );

    return $this->response->withStatus(HttpCodes::HTTP_NOT_FOUND->value);
  }

  public function logout(
    Request $request,
    Response $response
  ): Response {
    $this->request = $request;
    $this->response = $response;

    try {
      $this->presenter->logout();

      return $this->response;
    } catch (AuthPresenterException $exception) {
      $this->respondWith(['message' => $exception->getMessage()]);

      return $this->response->withStatus($exception->getCode());
    } catch (\Exception $exception) {
      $this->respondWith(['message' => $exception->getMessage()]);

      return $this->response->withStatus(HttpCodes::HTTP_SERVER_ERROR->value);
    }
  }

  public function login(
    Request $request,
    Response $response
  ): Response {
    $this->request = $request;
    $this->response = $response;

    try {
      /** @var array<string, mixed> $payload */
      $payload = (array) $request->getParsedBody();

      /** @var array{login: string, password: string} $sanitizedPayload */
      $sanitizedPayload = $this->sanitizer->sanitize($payload);
      $messages = $this->validator->validate([
        'login' => [
          'type' => 'string'
        ],
        'password' => [
          'type' => 'string',
        ]
      ], $sanitizedPayload);

      if (count($messages) > 0) {
        $this->respondWith(['messages' => $messages]);

        return $this->response->withStatus(HttpCodes::HTTP_BAD_REQUEST->value);
      }

      $this->presenter->login(
        $sanitizedPayload['login'],
        $sanitizedPayload['password']
      );

      return $this->response;
    } catch (AuthPresenterException $exception) {
      $this->respondWith(['message' => $exception->getMessage()]);

      return $this->response->withStatus($exception->getCode());
    } catch (\Exception $exception) {
      $this->respondWith(['message' => $exception->getMessage()]);

      return $this->response->withStatus(HttpCodes::HTTP_SERVER_ERROR->value);
    }
  }
}
