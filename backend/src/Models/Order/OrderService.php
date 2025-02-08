<?php

namespace App\Models\Order;

use App\Contracts\Order\OrderRepository;
use App\Enums\HttpCodes;
use App\Exceptions\Bill\BillServiceException;
use App\Exceptions\Client\ClientServiceException;
use App\Exceptions\Order\OrderRepositoryException;
use App\Exceptions\Order\OrderServiceException;
use App\Models\Bill\BillDTO;
use App\Models\Bill\BillService;
use App\Models\Client\ClientService;

class OrderService
{
  private OrderRepository $orderRepository;
  private ClientService $clientService;
  private BillService $billService;

  public function __construct(OrderRepository $orderRepository, ClientService $clientService, BillService $billService)
  {
    $this->orderRepository = $orderRepository;
    $this->clientService = $clientService;
    $this->billService = $billService;
  }

  public function getOrder(int $orderId): ?Order
  {
    try {
      $order = $this->orderRepository->getOrder($orderId);

      if (!$order) {
        return null;
      }

      return $order;
    } catch (OrderRepositoryException $exception) {
      throw new OrderServiceException($exception->getMessage(), $exception->getCode());
    }
  }

  public function fulfill(BillDTO $dto): bool
  {
    try {
      return $this->billService->create($dto);
    } catch (BillServiceException $exception) {
      throw new OrderServiceException($exception->getMessage(), $exception->getCode());
    }
  }

  public function update(Order $order): bool
  {
    try {
      return $this->orderRepository->update($order);
    } catch (OrderRepositoryException $exception) {
      throw new OrderServiceException($exception->getMessage(), $exception->getCode());
    }
  }

  public function create(Order $order): bool
  {
    try {
      $orders = $this->getOrders();

      foreach ($orders as $existingOrder) {
        if ($existingOrder->getTable()->getId() === $order->getTable()->getId()) {
          throw new OrderServiceException('Mesa ocupada', HttpCodes::HTTP_BAD_REQUEST->value);
        }
      }

      $this->clientService->create($order->getClient());

      return $this->orderRepository->create($order);
    } catch (ClientServiceException $exception) {
      throw new OrderServiceException($exception->getMessage(), $exception->getCode());
    } catch (OrderRepositoryException $exception) {
      throw new OrderServiceException($exception->getMessage(), $exception->getCode());
    }
  }

  /**
   * @return Order[]
   */
  public function getOrders(): array
  {
    try {
      return $this->orderRepository->getOrders();
    } catch (OrderRepositoryException $exception) {
      throw new OrderServiceException($exception->getMessage(), $exception->getCode());
    }
  }

  public function addItems(Order $order): bool
  {
    try {
      $existingOrder = $this->getOrder($order->getId());

      if (!$existingOrder) {
        throw new OrderServiceException('Pedido não encontrado', HttpCodes::HTTP_NOT_FOUND->value);
      }

      if ($existingOrder->getStatus()->name === 'completed') {
        throw new OrderServiceException('Pedido fechado', HttpCodes::HTTP_BAD_REQUEST->value);
      }

      return $this->orderRepository->addItems($order);
    } catch (OrderRepositoryException $exception) {
      throw new OrderServiceException($exception->getMessage(), $exception->getCode());
    }
  }
}
