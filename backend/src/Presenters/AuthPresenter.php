<?php

namespace App\Presenters;

use App\Contracts\Auth\AuthView;
use App\Enums\HttpCodes;
use App\Exceptions\Auth\AuthPresenterException;
use App\Exceptions\Auth\AuthServiceException;
use App\Exceptions\Employee\EmployeePresenterException;
use App\Models\Auth\AuthService;
use App\Models\Employee\EmployeeRepositoryInRDB;
use App\Utils\PDOBuilder;

class AuthPresenter
{
  private AuthView $view;
  private AuthService $service;

  public function __construct(AuthView $view)
  {
    $this->view = $view;

    try {
      $repository = new EmployeeRepositoryInRDB(PDOBuilder::build());

      $this->service = new AuthService($repository);
    } catch (\PDOException $exception) {
      throw new EmployeePresenterException('Error connecting to database', HttpCodes::HTTP_SERVER_ERROR->value);
    }
  }

  public function login(string $login, string $password): void
  {
    try {
      $employee = $this->service->verifyEmployeeCredentials($login, $password);

      $this->view->updateSession([
        'id' => $employee->getId(),
        'name' => $employee->getName(),
        'login' => $employee->getLogin(),
        'userType' => $employee->getType()->value
      ]);

      $this->view->respondWith(['message' => 'sessão iniciada com sucesso']);
    } catch (AuthServiceException $exception) {
      throw new AuthPresenterException($exception->getMessage(), $exception->getCode());
    }
  }

  public function logout(): void
  {
    try {
      $this->view->destroySession();

      $this->view->respondWith(['message' => 'sessão destruída com sucesso']);
    } catch (\Exception $exception) {
      throw new AuthPresenterException('Erro ao deslogar funcionário', HttpCodes::HTTP_SERVER_ERROR->value);
    }
  }
}
