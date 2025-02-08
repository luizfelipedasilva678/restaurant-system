<?php

namespace App\Presenters;

use App\Contracts\Reservation\ReservationView;
use App\Enums\HttpCodes;
use App\Exceptions\Reservation\ReservationPresenterException;
use App\Exceptions\Reservation\ReservationServiceException;
use App\Models\Client\ClientRepositoryInRDB;
use App\Models\Client\ClientService;
use App\Models\Reservation\ReservationDTO;
use App\Models\Reservation\ReservationMapper;
use App\Models\Reservation\ReservationRepositoryInRDB;
use App\Models\Reservation\ReservationService;
use App\Models\Restaurant\RestaurantRepositoryInRDB;
use App\Models\Restaurant\RestaurantService;
use App\Models\Table\TableRepositoryInRDB;
use App\Models\Table\TableService;
use App\Utils\PDOBuilder;

class ReservationPresenter
{
  private ReservationView $view;
  private ReservationService $service;

  public function __construct(ReservationView $view)
  {
    try {
      $pdo = PDOBuilder::build();
      $reservationRepository = new ReservationRepositoryInRDB($pdo);
      $clientRepository = new ClientRepositoryInRDB($pdo);
      $restaurantRepository = new RestaurantRepositoryInRDB($pdo);
      $clientService = new ClientService($clientRepository);
      $restaurantService = new RestaurantService($restaurantRepository);
      $tableRepository = new TableRepositoryInRDB($pdo);
      $tableService = new TableService($tableRepository);

      $this->view = $view;
      $this->service = new ReservationService(
        $reservationRepository,
        $clientService,
        $restaurantService,
        $tableService
      );
    } catch (\PDOException $exception) {
      throw new ReservationPresenterException('Error connecting to database', HttpCodes::HTTP_SERVER_ERROR->value);
    } catch (ReservationServiceException $exception) {
      throw new ReservationPresenterException($exception->getMessage(), $exception->getCode());
    }
  }

  public function createReservation(ReservationDTO $reservationDTO): void
  {
    try {
      $reservation = ReservationMapper::toEntity($reservationDTO);

      $this->view->respondWith(
        ReservationMapper::toDTO(
          $this->service->create($reservation)
        )
      );
    } catch (ReservationServiceException $exception) {
      throw new ReservationPresenterException($exception->getMessage(), $exception->getCode());
    }
  }

  public function getReservation(int $id): void
  {
    try {
      $this->view->respondWith(
        ReservationMapper::toDTO($this->service->getReservation($id))
      );
    } catch (ReservationServiceException $exception) {
      throw new ReservationPresenterException($exception->getMessage(), $exception->getCode());
    }
  }

  public function getReservations(
    int $page,
    int $perPage,
    bool $currentAndLater,
    string | null $initialDate = null,
    string | null $finalDate = null
  ): void {
    try {
      $result = $this->service->getReservations(
        $page,
        $perPage,
        $currentAndLater,
        $initialDate,
        $finalDate
      );

      $this->view->respondWith(
        [
          'data' => ReservationMapper::toDTOArray(
            $result['data']
          ),
          'count' => $result['count']
        ]
      );
    } catch (ReservationServiceException $exception) {
      throw new ReservationPresenterException($exception->getMessage(), $exception->getCode());
    }
  }

  public function updateReservation(ReservationDTO $reservationDTO): void
  {
    try {
      $reservation = ReservationMapper::toEntity($reservationDTO);

      $this->view->respondWith(
        ReservationMapper::toDTO(
          $this->service->update($reservation)
        )
      );
    } catch (ReservationServiceException $exception) {
      throw new ReservationPresenterException($exception->getMessage(), $exception->getCode());
    }
  }
}
