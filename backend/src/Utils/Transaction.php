<?php

declare(strict_types=1);

namespace App\Utils;

use PDOException;

class Transaction
{
  public static function start(\PDO $pdo, callable $fn): void
  {
    try {
      $pdo->beginTransaction();

      $fn();

      $pdo->commit();
    } catch (PDOException $error) {
      $pdo->rollBack();

      throw $error;
    }
  }
}
