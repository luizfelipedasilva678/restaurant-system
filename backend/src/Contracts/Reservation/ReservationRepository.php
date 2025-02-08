<?php

declare(strict_types=1);

namespace App\Contracts\Reservation;

use App\Models\Reservation\Reservation;

interface ReservationRepository
{
  public function create(Reservation $reservation): Reservation;

  public function update(Reservation $reservation): Reservation;

  public function get(int $id): ?Reservation;

  /** @return array{data: Reservation[], count: int}  */
  public function getAll(
    int $limit,
    int $offset,
    bool $currentAndLater,
    string | null $initialDate = null,
    string | null $finalDate = null
  ): array;
}
