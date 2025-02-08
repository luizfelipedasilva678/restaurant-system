<?php

declare(strict_types=1);

namespace App\Utils\Session;

use App\Utils\Env;

define('IS_TEST', Env::get('ENV') === 'test');

// 7 Days
define('SESSION_LIFE_TIME', 7 * 24 * 60 * 60);

class SessionUtils
{
  public static function startSession(): void
  {
    if (IS_TEST) {
      return;
    }

    session_set_cookie_params([
      'lifetime' => SESSION_LIFE_TIME,
      'path' => '/',
      'domain' => 'localhost',
      'secure' => true,
      'httponly' => true,
      'samesite' => 'Strict'
    ]);

    session_start();
  }

  public static function destroySession(): void
  {
    if (IS_TEST) {
      return;
    }

    self::startSession();

    session_destroy();
  }
}
