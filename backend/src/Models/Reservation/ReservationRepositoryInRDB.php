<?php

namespace App\Models\Reservation;

use App\Contracts\Reservation\ReservationRepository;
use App\Enums\HttpCodes;
use App\Exceptions\Reservation\ReservationRepositoryException;
use App\Models\Client\Client;
use App\Models\Employee\Employee;
use App\Models\Phone\Phone;
use App\Models\Table\Table;

class ReservationRepositoryInRDB implements ReservationRepository
{
  private \PDO $pdo;

  public function __construct(\PDO $pdo)
  {
    $this->pdo = $pdo;
  }

  /** @return array{data: Reservation[], count: int}   */
  public function getAll(
    int $limit,
    int $offset,
    bool $currentAndLater,
    string | null $initialDate = null,
    string | null $finalDate = null
  ): array {
    try {
      $countSql = 'SELECT COUNT(*) as total_reservations FROM Reservation';
      $hasDateFilter = $initialDate !== null && $finalDate !== null;
      $sql = <<<'SQL'
        SELECT r.id,
        r.start_time,
        r.end_time,
        r.status,
        e.id as employee_id,
        e.name as employee_name,
        c.id as client_id,
        c.name as client_name,
        cp.id as client_phone_id,
        cp.phone as client_phone,
        rt.id as table_id,
        rt.number as table_number
        FROM Reservation r
        INNER JOIN Employee e ON r.employee_id = e.id
        INNER JOIN Client c ON r.client_id = c.id
        INNER JOIN RestaurantTable rt ON r.restaurant_table_id = rt.id
        INNER JOIN ClientPhone cp ON cp.client_id = c.id
      SQL;

      if ($hasDateFilter) {
        $sql = <<<SQL
					  {$sql}
					  WHERE DATE_FORMAT(r.end_time, '%Y-%m-%d') BETWEEN :initial_date AND :final_date
					SQL;

        $countSql = <<<SQL
            {$countSql}
            WHERE DATE_FORMAT(end_time, '%Y-%m-%d') BETWEEN :initial_date AND :final_date
          SQL;
      } elseif ($currentAndLater) {
        $sql = <<<SQL
					  {$sql}
					  WHERE DATE_FORMAT(r.end_time, '%Y-%m-%d') <= DATE_FORMAT(now(), '%Y-%m-%d')
					SQL;

        $countSql = <<<SQL
            {$countSql}
            WHERE DATE_FORMAT(end_time, '%Y-%m-%d') <= DATE_FORMAT(now(), '%Y-%m-%d')
          SQL;
      }

      $sql = <<<SQL
				  {$sql}
				  ORDER BY r.start_time ASC, rt.number ASC
				  LIMIT :limit OFFSET :offset
				SQL;

      /** @var Reservation[] */
      $reservations = [];

      $stmt = $this->pdo->prepare($sql);
      $stmt->setFetchMode(\PDO::FETCH_ASSOC);
      $hasDateFilter && $stmt->bindValue(':initial_date', $initialDate);
      $hasDateFilter && $stmt->bindValue(':final_date', $finalDate);
      $stmt->bindValue(':limit', $limit, \PDO::PARAM_INT);
      $stmt->bindValue(':offset', $offset, \PDO::PARAM_INT);
      $stmt->execute();

      /** @var array{
       * id: int,
       * table_id: int,
       * table_number: int,
       * client_id: int,
       * client_name: string,
       * client_phone_id: int,
       * client_phone: string,
       * employee_id: int,
       * employee_name: string,
       * start_time: string,
       * end_time: string,
       * status: string
       * } $reservation
       *  */
      foreach ($stmt as $reservation) {
        array_push($reservations, new Reservation(
          new Table($reservation['table_id'], $reservation['table_number']),
          new Client(
            $reservation['client_id'],
            $reservation['client_name'],
            new Phone(
              $reservation['client_phone_id'],
              $reservation['client_phone']
            )
          ),
          new Employee($reservation['employee_id'], $reservation['employee_name']),
          new \DateTime($reservation['start_time']),
          $reservation['id'],
          $reservation['status'],
          new \DateTime($reservation['end_time']),
        ));
      }

      $stmt = $this->pdo->prepare($countSql);
      $hasDateFilter && $stmt->bindValue(':initial_date', $initialDate);
      $hasDateFilter && $stmt->bindValue(':final_date', $finalDate);
      $stmt->execute();
      $count = (int) ($stmt ? $stmt->fetchColumn() : 0);

      return [
        'data' => $reservations,
        'count' => $count
      ];
    } catch (\PDOException $e) {
      throw new ReservationRepositoryException('Erro ao obter reservas', HttpCodes::HTTP_SERVER_ERROR->value);
    }
  }

  public function update(Reservation $reservation): Reservation
  {
    try {
      $sql = <<<'SQL'
				  UPDATE Reservation
				  SET status = :status
				  WHERE id = :id
				SQL;

      $stmt = $this->pdo->prepare($sql);
      $stmt->bindValue(':status', $reservation->getStatus());
      $stmt->bindValue(':id', $reservation->getId(), \PDO::PARAM_INT);
      $stmt->execute();

      return $this->get($reservation->getId());
    } catch (\PDOException $e) {
      throw new ReservationRepositoryException('Erro ao atualizar reserva', 500);
    }
  }

  public function get(int $id): Reservation
  {
    try {
      $sql = <<<'SQL'
          SELECT r.id,
          r.start_time,
          r.end_time,
          r.status,
          e.id as employee_id,
          e.name as employee_name,
          c.id as client_id,
          c.name as client_name,
          cp.id as client_phone_id,
          cp.phone as client_phone,
          rt.id as table_id,
          rt.number as table_number
          FROM Reservation r
          INNER JOIN Employee e ON r.employee_id = e.id
          INNER JOIN Client c ON r.client_id = c.id
          INNER JOIN RestaurantTable rt ON r.restaurant_table_id = rt.id
          INNER JOIN ClientPhone cp ON cp.client_id = c.id
          WHERE r.id = :id
      SQL;

      $stmt = $this->pdo->prepare($sql);
      $stmt->setFetchMode(\PDO::FETCH_ASSOC);
      $stmt->bindValue(':id', $id, \PDO::PARAM_INT);
      $stmt->execute();

      /** @var array{
       * table_id: int,
       * table_number: int,
       * client_id: int,
       * client_phone_id: int,
       * client_phone: string,
       * client_name: string,
       * employee_id: int,
       * employee_name: string,
       * start_time: string,
       * end_time: string,
       * status: string
       * } |false
       * $reg */
      $reg = $stmt->fetch(\PDO::FETCH_ASSOC);

      if (!$reg) {
        throw new ReservationRepositoryException('Reserva não encontrada', 404);
      }

      return new Reservation(
        new Table($reg['table_id'], $reg['table_number']),
        new Client($reg['client_id'], $reg['client_name'], new Phone($reg['client_phone_id'], $reg['client_phone'])),
        new Employee($reg['employee_id'], $reg['employee_name']),
        new \DateTime($reg['start_time']),
        $id,
        $reg['status'],
        new \DateTime($reg['end_time']),
      );
    } catch (\PDOException $e) {
      throw new ReservationRepositoryException('Erro ao obter reserva', 500);
    }
  }

  public function create(Reservation $reservation): Reservation
  {
    try {
      $sql = <<<'SQL'
				  INSERT INTO
				  Reservation (start_time, end_time, client_id, employee_id, restaurant_table_id, status)
				  VALUES (:start_time, :end_time, :client_id, :employee_id, :restaurant_table_id, :status);
				SQL;

      $stmt = $this->pdo->prepare($sql);

      $stmt->bindValue(':start_time', $reservation->getStartTime()->format('Y-m-d H:i:s'));
      $stmt->bindValue(':end_time', $reservation->getEndTime()->format('Y-m-d H:i:s'));
      $stmt->bindValue(':client_id', $reservation->getClient()->getId());
      $stmt->bindValue(':employee_id', $reservation->getEmployee()->getId());
      $stmt->bindValue(':restaurant_table_id', $reservation->getTable()->getId());
      $stmt->bindValue(':status', $reservation->getStatus());
      $stmt->execute();

      if (!$this->pdo->lastInsertId()) {
        throw new ReservationRepositoryException('Erro ao criar reserva', 500);
      }

      return $this->get(intval($this->pdo->lastInsertId()));
    } catch (\PDOException $e) {
      throw new ReservationRepositoryException('Erro ao criar reserva', 500);
    }
  }
}
