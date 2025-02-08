<?php

namespace App\Models\Reservation;

use App\Contracts\Reservation\ReservationRepository;
use App\Enums\HttpCodes;
use App\Exceptions\Client\ClientServiceException;
use App\Exceptions\Order\OrderServiceException;
use App\Exceptions\Reservation\ReservationRepositoryException;
use App\Exceptions\Reservation\ReservationServiceException;
use App\Models\Client\ClientService;
use App\Models\Restaurant\RestaurantService;
use App\Models\Table\TableService;
use App\Utils\DateUtils;

class ReservationService
{
  private ReservationRepository $repository;
  private ClientService $clientService;
  private RestaurantService $restaurantService;
  private TableService $tableService;

  public function __construct(
    ReservationRepository $repository,
    ClientService $clientService,
    RestaurantService $restaurantService,
    TableService $tableService,
  ) {
    $this->repository = $repository;
    $this->clientService = $clientService;
    $this->restaurantService = $restaurantService;
    $this->tableService = $tableService;
  }

  public function create(Reservation $reservation): Reservation
  {
    try {
      if (!$this->restaurantService->isOpen()) {
        throw new ReservationServiceException('O restaurante está fechado', HttpCodes::HTTP_BAD_REQUEST->value);
      }

      if (!$this->restaurantService->isAcceptingNewReservations()) {
        throw new ReservationServiceException('O restaurante não está aceitando reservas no momento',  HttpCodes::HTTP_BAD_REQUEST->value);
      }

      if (!$this->restaurantService->isReservationWithinWorkingHours($reservation->getStartTime(), $reservation->getEndTime())) {
        $day = DateUtils::getDayOfWeek($reservation->getStartTime());
        $translatedDay = DateUtils::getTranslatedDayOfWeek($day);
        $workingHours = $this->restaurantService->getWorkingHours()[$day];
        $message = 'O horário da reserva está fora do horário de funcionamento. ';
        $message .= $translatedDay === 'Sábado' || $translatedDay === 'Domingo' ? 'No ' : 'Na ';
        $message .=  $translatedDay . ', ';
        $message .= 'o restaurante só funciona entre ' . $workingHours['startTime'] . ' e ' . $workingHours['endTime'] . '.';
        throw new ReservationServiceException($message, HttpCodes::HTTP_BAD_REQUEST->value);
      }

      $tables = $this->tableService->getTables($reservation->getStartTime()->format('Y-m-d H:i:s'));

      foreach ($tables as $table) {
        if ($table->getId() === $reservation->getTable()->getId()) {
          throw new ReservationServiceException('Mesa indisponível', HttpCodes::HTTP_BAD_REQUEST->value);
        }
      }

      $this->clientService->create($reservation->getClient());
      $reservation = $this->repository->create($reservation);

      return $reservation;
    } catch (ReservationRepositoryException $exception) {
      throw new ReservationServiceException($exception->getMessage(), $exception->getCode());
    } catch (OrderServiceException $exception) {
      throw new ReservationServiceException($exception->getMessage(), $exception->getCode());
    } catch (ClientServiceException $exception) {
      throw new ReservationServiceException($exception->getMessage(), $exception->getCode());
    }
  }

  /** @return array{data: Reservation[], count: int}   */
  public function getReservations(
    int $page,
    int $perPage,
    bool $currentAndLater,
    string | null $initialDate = null,
    string | null $finalDate = null
  ): array {
    try {
      $offset = intval(ceil(($page - 1) * $perPage));

      return $this->repository->getAll(
        $perPage,
        $offset,
        $currentAndLater,
        $initialDate,
        $finalDate
      );
    } catch (ReservationRepositoryException $exception) {
      throw new ReservationServiceException($exception->getMessage(), $exception->getCode());
    }
  }

  public function getReservation(int $id): Reservation
  {
    try {
      /** @var Reservation $reservation */
      $reservation = $this->repository->get($id);

      return $reservation;
    } catch (ReservationRepositoryException $exception) {
      throw new ReservationServiceException($exception->getMessage(), $exception->getCode());
    }
  }

  public function update(Reservation $reservation): Reservation
  {
    try {
      return $this->repository->update($reservation);
    } catch (ReservationRepositoryException $exception) {
      throw new ReservationServiceException($exception->getMessage(), $exception->getCode());
    }
  }
}
