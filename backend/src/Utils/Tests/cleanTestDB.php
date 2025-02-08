<?php

declare(strict_types=1);

namespace App\Utils\Tests;

function cleanTestDB(\PDO $pdo, string $additionalDmlScript = ''): void
{
  $dmlScript = file_get_contents(__DIR__ . '/../../../db/dml-test.sql');

  if (!$dmlScript) {
    return;
  }

  $pdo->exec(<<<SQL
    DELETE FROM Reservation;
    DELETE FROM Schedule;
    DELETE FROM Day;
    DELETE FROM Bill;
    DELETE FROM Employee;
    DELETE FROM ClientPhone;
    DELETE FROM PaymentMethod;
    DELETE FROM OrderItem;
    DELETE FROM TableOrder;
    DELETE FROM Client;
    DELETE FROM RestaurantTable;
    DELETE FROM Item;
    DELETE FROM Category;
    {$dmlScript}
    {$additionalDmlScript}
  SQL);
}
