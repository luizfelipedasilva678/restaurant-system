<?php

declare(strict_types=1);

namespace App\Models\Table;

use App\Contracts\Table\TableRepository;
use App\Enums\HttpCodes;
use App\Exceptions\Table\TableRepositoryException;

class TableRepositoryInRDB implements TableRepository
{
  private \PDO $pdo;

  public function __construct(\PDO $pdo)
  {
    $this->pdo = $pdo;
  }

  /**
   * @return array<Table>
   */
  public function getAll(
    string | null $startDate = null,
    string | null $endDate = null
  ): array {
    try {
      $hasDateFilters = $startDate !== null && $endDate !== null;

      $sql = <<<'SQL'
        SELECT id, number
        FROM RestaurantTable
      SQL;

      if ($hasDateFilters) {
        $sql = <<<'SQL'
          SELECT t.id, t.number
          FROM RestaurantTable t
          INNER JOIN Reservation r
          ON t.id = r.restaurant_table_id
          WHERE r.status = "active" and (
            :start_time >= r.start_time and :start_time < r.end_time
            or
            :end_time > r.start_time and :end_time <= r.end_time
          )
        SQL;
      }

      $tables = [];

      $stmt = $this->pdo->prepare($sql);
      $stmt->setFetchMode(\PDO::FETCH_ASSOC);
      $hasDateFilters && $stmt->bindValue(':start_time', $startDate, \PDO::PARAM_STR);
      $hasDateFilters && $stmt->bindValue(':end_time', $endDate, \PDO::PARAM_STR);
      $stmt->execute();

      /** @var array{id: int, number: int} $reg */
      foreach ($stmt as $reg) {
        array_push($tables, new Table($reg['id'], $reg['number']));
      }

      return $tables;
    } catch (\PDOException $exception) {
      throw new TableRepositoryException('Erro ao buscar mesas', HttpCodes::HTTP_SERVER_ERROR->value);
    }
  }
}
