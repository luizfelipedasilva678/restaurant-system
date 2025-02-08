<?php

declare(strict_types=1);

namespace App\Models\Auth;

use App\Contracts\Employee\EmployeeRepository;
use App\Exceptions\Auth\AuthServiceException;
use App\Exceptions\Employee\EmployeeRepositoryException;
use App\Models\Employee\Employee;
use App\Utils\PasswordUtils;

class AuthService
{
  private EmployeeRepository $employeeRepository;

  public function __construct(EmployeeRepository $employeeRepository)
  {
    $this->employeeRepository = $employeeRepository;
  }

  public function verifyEmployeeCredentials(string $login, string $password): Employee
  {
    try {
      $userSalt = $this->employeeRepository->getSaltByLogin($login);

      return $this->employeeRepository->getByLoginAndPassword(
        $login,
        PasswordUtils::getHashPassword($password, $userSalt)
      );
    } catch (EmployeeRepositoryException $exception) {
      throw new AuthServiceException($exception->getMessage(), $exception->getCode());
    } catch (\Exception $exception) {
      throw new AuthServiceException('Erro ao autenticar usuário', 500);
    }
  }
}
