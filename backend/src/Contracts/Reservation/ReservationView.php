<?php

namespace App\Contracts\Reservation;

interface ReservationView
{
  /**
   * @param null|array<mixed>|object|string $data
   */
  public function respondWith($data): void;
}
