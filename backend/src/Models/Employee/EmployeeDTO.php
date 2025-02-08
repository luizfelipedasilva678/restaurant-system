<?php

namespace App\Models\Employee;

use App\Enums\EmployeeType;

class EmployeeDTO implements \JsonSerializable
{
  public int $id;
  public string $name;
  public string $login;
  public string $password;
  public EmployeeType $type;

  public function __construct(
    int $id = 0,
    string $name = '',
    EmployeeType $type = EmployeeType::attendant,
  ) {
    $this->id = $id;
    $this->name = $name;
    $this->type = $type;
  }

  public function jsonSerialize(): mixed
  {
    return [
      'id' => $this->id,
      'name' => $this->name,
      'type' => $this->type,
    ];
  }
}
