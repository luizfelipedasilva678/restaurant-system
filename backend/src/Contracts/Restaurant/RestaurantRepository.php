<?php

declare(strict_types=1);

namespace App\Contracts\Restaurant;

interface RestaurantRepository
{
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
   * Sunday:array{
   *  startTime: string,
   *  endTime: string
   * },
   *} */
  public function getWorkingHours(): array;

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
   *} */
  public function getReservationHours(): array;
}
