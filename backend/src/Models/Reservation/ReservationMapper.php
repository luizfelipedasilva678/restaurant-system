<?php

namespace App\Models\Reservation;

use App\Models\Client\Client;
use App\Models\Employee\Employee;
use App\Models\Phone\Phone;
use App\Models\Table\Table;

class ReservationMapper
{
  public static function toDTO(Reservation $reservation): ReservationDTO
  {
    return new ReservationDTO(
      $reservation->getId(),
      $reservation->getStartTime(),
      $reservation->getTable()->getId(),
      $reservation->getTable()->getNumber(),
      $reservation->getEmployee()->getId(),
      $reservation->getEmployee()->getName(),
      $reservation->getClient()->getName(),
      $reservation->getClient()->getId(),
      $reservation->getStatus(),
      $reservation->getClient()->getPhone()->getNumber()
    );
  }

  public static function toEntity(ReservationDTO $reservation): Reservation
  {
    return new Reservation(
      new Table($reservation->tableId, $reservation->tableNumber),
      new Client(
        $reservation->clientId,
        $reservation->clientName,
        new Phone(0, $reservation->clientPhone)
      ),
      new Employee($reservation->employeeId, $reservation->employeeName),
      $reservation->startTime,
      $reservation->id,
      $reservation->status,
      $reservation->endTime
    );
  }

  /**
   * @param Reservation[] $entities
   *
   * @return ReservationDTO[]
   */
  public static function toDTOArray(array $entities): array
  {
    return array_map([ReservationMapper::class, 'toDTO'], $entities);
  }
}
