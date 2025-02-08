<?php

namespace App\Models\Restaurant;

use App\Contracts\Restaurant\RestaurantRepository;
use App\Enums\HttpCodes;
use App\Exceptions\Restaurant\RestaurantRepositoryException;

class RestaurantRepositoryInRDB implements RestaurantRepository
{
  private \Pdo $pdo;

  public function __construct(\Pdo $pdo)
  {
    $this->pdo = $pdo;
  }

  /** @return array{
   * Monday: array{
   *  startTime: string,
   *  endTime: string
   * },
   * Tuesday: array{
   *  startTime: string,
   *  endTime: string
   * },
   * Wednesday: array{
   *  startTime: string,
   *  endTime: string
   * },
   * Thursday: array{
   *  startTime: string,
   *  endTime: string
   * },
   * Friday: array{
   *  startTime: string,
   *  endTime: string
   * },
   * Saturday: array{
   *  startTime: string,
   *  endTime: string
   * },
   * Sunday: array{
   *  startTime: string,
   *  endTime: string
   * },
   *} */
  public function getWorkingHours(): array
  {
    try {
      $sql = <<<'SQL'
				  SELECT Day.name, Schedule.start_time, Schedule.end_time
				  FROM Schedule
				  INNER JOIN Day ON Schedule.day_id = Day.id
				  WHERE Schedule.schedule_type = "working_hours"
				SQL;

      $stmt = $this->pdo->prepare($sql);
      $stmt->setFetchMode(\Pdo::FETCH_ASSOC);
      $stmt->execute();

      /**
       * @var array{
       * Monday: array{
       *  startTime: string,
       *  endTime: string
       * },
       * Tuesday: array{
       *  startTime: string,
       *  endTime: string
       * },
       * Wednesday: array{
       *  startTime: string,
       *  endTime: string
       * },
       * Thursday: array{
       *  startTime: string,
       *  endTime: string
       * },
       * Friday: array{
       *  startTime: string,
       *  endTime: string
       * },
       * Saturday: array{
       *  startTime: string,
       *  endTime: string
       * },
       * Sunday:array{
       *  startTime: string,
       *  endTime: string
       * },
       *} $workingHours */
      $workingHours = [];

      /** @var array{name: "Friday"|"Monday"|"Saturday"|"Sunday"|"Thursday"|"Tuesday"|"Wednesday", start_time: string, end_time: string} $reg */
      foreach ($stmt as $reg) {
        $workingHours[$reg['name']] = [
          'startTime' => $reg['start_time'],
          'endTime' => $reg['end_time'],
        ];
      }

      return $workingHours;
    } catch (\PDOException $e) {
      throw new RestaurantRepositoryException('Erro ao buscar horário de funcionamento', HttpCodes::HTTP_SERVER_ERROR->value);
    }
  }

  /** @return array{
   * Thursday: array{
   *  startTime: string,
   *  endTime: string
   * },
   * Friday: array{
   *  startTime: string,
   *  endTime: string
   * },
   * Saturday: array{
   *  startTime: string,
   *  endTime: string
   * },
   * Sunday:array{
   *  startTime: string,
   *  endTime: string
   * },
   *}*/
  public function getReservationHours(): array
  {
    try {
      $sql = <<<'SQL'
				  SELECT Day.name, Schedule.start_time, Schedule.end_time
				  FROM Schedule
				  INNER JOIN Day ON Schedule.day_id = Day.id
				  WHERE Schedule.schedule_type = "reservation"
				SQL;

      $stmt = $this->pdo->prepare($sql);
      $stmt->setFetchMode(\Pdo::FETCH_ASSOC);
      $stmt->execute();

      /** @var array{
       * Thursday: array{
       *  startTime: string,
       *  endTime: string
       * },
       * Friday: array{
       *  startTime: string,
       *  endTime: string
       * },
       * Saturday: array{
       *  startTime: string,
       *  endTime: string
       * },
       * Sunday:array{
       *  startTime: string,
       *  endTime: string
       * },
       *} $reservationHours */
      $reservationHours = [];

      /** @var array{name: "Friday"|"Saturday"|"Sunday"|"Thursday", start_time: string, end_time: string} $reg */
      foreach ($stmt as $reg) {
        $reservationHours[$reg['name']] = [
          'startTime' => $reg['start_time'],
          'endTime' => $reg['end_time'],
        ];
      }

      return $reservationHours;
    } catch (\PDOException $e) {
      throw new RestaurantRepositoryException('Erro ao obter horário de reserva', HttpCodes::HTTP_SERVER_ERROR->value);
    }
  }
}
