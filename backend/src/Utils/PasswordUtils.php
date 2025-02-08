<?php

declare(strict_types=1);

namespace App\Utils;

class PasswordUtils
{
  public static function getHashPassword(string $password, string $salt): string
  {
    return hash('sha512', Env::get('PASSWORD_PEPPER_PREFIX') . $password . $salt . Env::get('PASSWORD_PEPPER_SUFFIX'));
  }
}
