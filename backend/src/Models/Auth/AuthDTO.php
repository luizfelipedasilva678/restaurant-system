<?php

namespace App\Models\Auth;

use App\Enums\EmployeeType;

class AuthDTO implements \JsonSerializable
{
  public int $id;
  public string $name;
  public string $login;
  public string $password;
  public EmployeeType $type;

  public function __construct(
    int $id = 0,
    string $name = '',
    string $login = '',
    EmployeeType $type = EmployeeType::attendant,
  ) {
    $this->id = $id;
    $this->name = $name;
    $this->login = $login;
    $this->type = $type;
  }

  public function jsonSerialize(): mixed
  {
    return [
      'session' => [
        'user' => [
          'id' => $this->id,
          'name' => $this->name,
          'login' => $this->login,
          'type' => $this->type,
        ]
      ]
    ];
  }
}
