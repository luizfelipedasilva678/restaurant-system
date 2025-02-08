<?php

declare(strict_types=1);

namespace App\Models\Reservation;

use App\Models\Client\Client;
use App\Models\Employee\Employee;
use App\Models\Table\Table;

class Reservation
{
  private int $id;
  private \DateTime $startTime;
  private \DateTime $endTime;
  private Table $table;
  private Client $client;
  private Employee $employee;
  private string $status;

  public function __construct(
    Table $table,
    Client $client,
    Employee $employee,
    \DateTime $startTime = new \DateTime(),
    int $id = 0,
    string $status = 'active',
    \DateTime $endTime = new \DateTime()
  ) {
    $this->id = $id;
    $this->startTime = $startTime;
    $this->endTime = $endTime;
    $this->table = $table;
    $this->client = $client;
    $this->employee = $employee;
    $this->status = $status;
  }

  public function getId(): int
  {
    return $this->id;
  }

  public function setId(int $id): void
  {
    $this->id = $id;
  }

  public function getStartTime(): \DateTime
  {
    return $this->startTime;
  }

  public function setStartTime(\DateTime $startTime): void
  {
    $this->startTime = $startTime;
  }

  public function getEndTime(): \DateTime
  {
    return $this->endTime;
  }

  public function setEndTime(\DateTime $endTime): void
  {
    $this->endTime = $endTime;
  }

  public function getTable(): Table
  {
    return $this->table;
  }

  public function setTable(Table $table): void
  {
    $this->table = $table;
  }

  public function getClient(): Client
  {
    return $this->client;
  }

  public function setClient(Client $client): void
  {
    $this->client = $client;
  }

  public function getEmployee(): Employee
  {
    return $this->employee;
  }

  public function setEmployee(Employee $employee): void
  {
    $this->employee = $employee;
  }

  public function getStatus(): string
  {
    return $this->status;
  }

  public function setStatus(string $status): void
  {
    $this->status = $status;
  }
}
