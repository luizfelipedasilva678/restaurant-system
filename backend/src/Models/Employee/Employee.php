<?php

declare(strict_types=1);

namespace App\Models\Employee;

use App\Enums\EmployeeType;

class Employee
{
  private int $id;
  private string $name;
  private string $login;
  private string $password;
  private EmployeeType $type;

  public function __construct(
    int $id = 0,
    string $name = '',
    string $login = '',
    EmployeeType $type = EmployeeType::attendant,
    string $password = ''
  ) {
    $this->id = $id;
    $this->name = $name;
    $this->login = $login;
    $this->password = $password;
    $this->type = $type;
  }

  public function getId(): int
  {
    return $this->id;
  }

  public function setId(int $id): void
  {
    $this->id = $id;
  }

  public function getName(): string
  {
    return $this->name;
  }

  public function setName(string $name): void
  {
    $this->name = $name;
  }

  public function getLogin(): string
  {
    return $this->login;
  }

  public function setLogin(string $login): void
  {
    $this->login = $login;
  }

  public function getPassword(): string
  {
    return $this->password;
  }

  public function setPassword(string $password): void
  {
    $this->password = $password;
  }

  public function getType(): EmployeeType
  {
    return $this->type;
  }

  public function setType(EmployeeType $type): void
  {
    $this->type = $type;
  }

  public static function build(int $id, string $name, string $password, string $login, EmployeeType $type): Employee
  {
    $employee = new Employee();

    $employee->setId($id);
    $employee->setName($name);
    $employee->setLogin($login);
    $employee->setPassword($password);
    $employee->setType($type);

    return $employee;
  }
}
