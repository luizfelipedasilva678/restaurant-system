<?php

namespace App\Models\Restaurant;

use App\Contracts\Restaurant\RestaurantRepository;
use App\Exceptions\Restaurant\RestaurantRepositoryException;
use App\Exceptions\Restaurant\RestaurantServiceException;
use App\Utils\DateUtils;

class RestaurantService
{
  private RestaurantRepository $repository;

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
  private array $workingHours;

  /**
   * @var array{
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
  private array $reservationHours;

  public function __construct(RestaurantRepository $repository)
  {
    $this->repository = $repository;
    $this->workingHours = $this->getWorkingHoursFromRepository();
    $this->reservationHours = $this->getReservationHoursFromRepository();
  }

  public function isOpen(): bool
  {
    return $this->isWithinValidInterval($this->workingHours);
  }

  public function isAcceptingNewReservations(): bool
  {
    return $this->isWithinValidInterval($this->reservationHours);
  }

  public function isReservationWithinWorkingHours(
    \DateTime $customerInitialReservationTime,
    \DateTime $customerFinalReservationTime
  ): bool {
    $dayOfWeek = DateUtils::getDayOfWeek($customerInitialReservationTime);
    $restaurantStartTimeForDay = DateUtils::getDateTimeFromString($customerInitialReservationTime->format('Y-m-d'), $this->workingHours[$dayOfWeek]['startTime']);
    $restaurantEndTimeForDay = DateUtils::getDateTimeFromString($customerInitialReservationTime->format('Y-m-d'), $this->workingHours[$dayOfWeek]['endTime']);

    return $customerInitialReservationTime >= $restaurantStartTimeForDay && $customerFinalReservationTime <= $restaurantEndTimeForDay;
  }

  /**
   * @return array{
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
  public function getWorkingHours(): array
  {
    return $this->workingHours;
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
   * Sunday:array{
   *  startTime: string,
   *  endTime: string
   * },
   *} */
  private function getWorkingHoursFromRepository(): array
  {
    try {
      return $this->repository->getWorkingHours();
    } catch (RestaurantRepositoryException $exception) {
      throw new RestaurantServiceException(
        $exception->getMessage(),
        $exception->getCode()
      );
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
  private function getReservationHoursFromRepository(): array
  {
    try {
      return $this->repository->getReservationHours();
    } catch (RestaurantRepositoryException $exception) {
      throw new RestaurantServiceException(
        $exception->getMessage(),
        $exception->getCode()
      );
    }
  }

  /** @param array{
   * Monday?: array{
   *  startTime: string,
   *  endTime: string
   * },
   * Tuesday?: array{
   *  startTime: string,
   *  endTime: string
   * },
   * Wednesday?: array{
   *  startTime: string,
   *  endTime: string
   * },
   * Thursday?: array{
   *  startTime: string,
   *  endTime: string
   * },
   * Friday?: array{
   *  startTime: string,
   *  endTime: string
   * },
   * Saturday?: array{
   *  startTime: string,
   *  endTime: string
   * },
   * Sunday?:array{
   *  startTime: string,
   *  endTime: string
   * },
   *} $hoursForDay */
  private function isWithinValidInterval(array $hoursForDay): bool
  {
    $day = DateUtils::getDayOfWeek();

    if (!isset($hoursForDay[$day])) {
      return false;
    }

    $now = new \DateTime();
    $startTimeForDay = DateUtils::getDateTimeFromString($now->format('Y-m-d'), $hoursForDay[$day]['startTime']);
    $endTimeForDay = DateUtils::getDateTimeFromString($now->format('Y-m-d'), $hoursForDay[$day]['endTime']);

    return $now >= $startTimeForDay && $now <= $endTimeForDay;
  }
}
