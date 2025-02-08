<?php

namespace App\Models\Reservation;

class ReservationDTO implements \JsonSerializable
{
  public int $id;
  public int $tableId;
  public int $tableNumber;
  public string $clientName;
  public int $clientId;
  public string $employeeName;
  public int $employeeId;
  public string $status;
  public \DateTime $startTime;
  public \DateTime $endTime;
  public string $clientPhone;

  public function __construct(
    int $id = 0,
    \DateTime $startTime = new \DateTime(),
    int $tableId = 0,
    int $tableNumber = 0,
    int $employeeId = 0,
    string $employeeName = '',
    string $clientName = '',
    int $clientId = 0,
    string $status = 'active',
    string $clientPhone = ''
  ) {
    $this->id = $id;
    $this->startTime = $startTime;
    $this->tableId = $tableId;
    $this->tableNumber = $tableNumber;
    $this->employeeId = $employeeId;
    $this->employeeName = $employeeName;
    $this->clientName = $clientName;
    $this->clientId = $clientId;
    $this->status = $status;
    $this->clientPhone = $clientPhone;

    $endTime = new \DateTime($startTime->format('Y-m-d H:i:s'));
    $endTime->add(new \DateInterval('PT2H'));
    $this->endTime = $endTime;
  }

  public function jsonSerialize(): mixed
  {
    return [
      'id' => $this->id,
      'status' => $this->status,
      'startTime' => $this->startTime->format('Y-m-d H:i:s'),
      'endTime' => $this->endTime->format('Y-m-d H:i:s'),
      'table' => [
        'id' => $this->tableId,
        'number' => $this->tableNumber,
      ],
      'employee' => [
        'id' => $this->employeeId,
        'name' => $this->employeeName,
      ],
      'client' => [
        'id' => $this->clientId,
        'name' => $this->clientName,
        'phone' => $this->clientPhone
      ],
    ];
  }
}
