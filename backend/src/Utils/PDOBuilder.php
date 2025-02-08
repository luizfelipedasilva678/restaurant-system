<?php

declare(strict_types=1);

namespace App\Utils;

class PDOBuilder
{
  public static function build(bool $test = false): \PDO
  {
    $database = !$test ? Env::get('DATABASE_NAME') : 'test';
    $host = Env::get('DATABASE_HOST');
    $user = Env::get('DATABASE_USER');
    $password = Env::get('DATABASE_PASSWORD');

    return new \PDO(
      "mysql:dbname={$database};host={$host}",
      $user,
      $password,
      [
        \PDO::ATTR_PERSISTENT => true,
        \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
      ]
    );
  }
}
