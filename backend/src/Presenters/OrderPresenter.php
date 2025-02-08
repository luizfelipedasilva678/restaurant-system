<?php

namespace App\Presenters;

use App\Contracts\Order\OrderView;
use App\Enums\HttpCodes;
use App\Enums\OrderStatus;
use App\Exceptions\Order\OrderPresenterException;
use App\Exceptions\Order\OrderServiceException;
use App\Models\Bill\BillDTO;
use App\Models\Bill\BillRepositoryInRDB;
use App\Models\Bill\BillService;
use App\Models\Client\ClientRepositoryInRDB;
use App\Models\Client\ClientService;
use App\Models\Order\OrderDTO;
use App\Models\Order\OrderMapper;
use App\Models\Order\OrderRepositoryInRDB;
use App\Models\Order\OrderService;
use App\Utils\PDOBuilder;
use App\Utils\Transaction;
use PDO;
use PDOException;

class OrderPresenter
{
  private OrderView $view;
  private OrderService $orderService;
  private PDO $pdo;

  public function __construct(OrderView $orderView)
  {
    try {
      $this->pdo = PDOBuilder::build();

      $this->view = $orderView;
      $this->orderService = new OrderService(
        new OrderRepositoryInRDB($this->pdo),
        new ClientService(new ClientRepositoryInRDB($this->pdo)),
        new BillService(new BillRepositoryInRDB($this->pdo))
      );
    } catch (PDOException $exception) {
      throw new OrderPresenterException('Error connecting to database', HttpCodes::HTTP_SERVER_ERROR->value);
    }
  }

  public function fulFill(BillDTO $dto): void
  {
    try {
      Transaction::start($this->pdo, function () use ($dto) {
        $order = $this->orderService->getOrder($dto->orderId);

        if (!$order) {
          throw new OrderPresenterException('Pedido não encontrado', HttpCodes::HTTP_NOT_FOUND->value);
        }

        if ($order->getStatus() === OrderStatus::completed) {
          throw new OrderPresenterException('Pedido já finalizado', HttpCodes::HTTP_BAD_REQUEST->value);
        }

        $order->setStatus(OrderStatus::completed);

        $valueWithDiscount = $dto->total - ($dto->total * ($dto->discount / 100));
        $value = $dto->discount > 0 ? $valueWithDiscount : $dto->total;
        $dto->total = $value;

        $order->setStatus(OrderStatus::completed);

        $settle = $this->orderService->fulFill($dto);
        $updated = $this->orderService->update($order);

        if (!$settle || !$updated) {
          throw new OrderPresenterException('Erro ao completar pedido', HttpCodes::HTTP_SERVER_ERROR->value);
        }

        $this->view->respondWith([
          'message' => 'pedido finalizado com sucesso'
        ]);
      });
    } catch (OrderServiceException $exception) {
      throw new OrderPresenterException($exception->getMessage(), $exception->getCode());
    }
  }

  public function createOrder(OrderDTO $orderDTO): void
  {
    try {
      $this->orderService->create(OrderMapper::toEntity($orderDTO));

      $this->view->respondWith([
        'message' => 'Pedido criado com sucesso',
      ]);
    } catch (OrderServiceException $exception) {
      throw new OrderPresenterException($exception->getMessage(), $exception->getCode());
    }
  }

  public function getOrders(): void
  {
    try {
      $orders = $this->orderService->getOrders();

      $this->view->respondWith(OrderMapper::toDTOArray($orders));
    } catch (OrderServiceException $exception) {
      throw new OrderPresenterException($exception->getMessage(), $exception->getCode());
    }
  }

  public function getOrder(int $orderId): void
  {
    try {
      $order = $this->orderService->getOrder($orderId);

      if (!$order) {
        throw new OrderPresenterException('Pedido não encontrado', HttpCodes::HTTP_NOT_FOUND->value);
      }

      $this->view->respondWith(
        OrderMapper::toDTO($order)
      );
    } catch (OrderServiceException $exception) {
      throw new OrderPresenterException($exception->getMessage(), $exception->getCode());
    }
  }

  public function addItems(OrderDTO $orderDTO): void
  {
    try {
      $result = $this->orderService->addItems(
        OrderMapper::toEntity($orderDTO)
      );

      if (!$result) {
        throw new OrderPresenterException('Erro ao adicionar items ao pedido', HttpCodes::HTTP_SERVER_ERROR->value);
      }

      $this->view->respondWith([
        'message' => 'Itens adicionados com sucesso',
      ]);
    } catch (OrderServiceException $exception) {
      throw new OrderPresenterException($exception->getMessage(), $exception->getCode());
    }
  }
}
