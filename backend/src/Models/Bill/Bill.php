<?php

declare(strict_types=1);

namespace App\Models\Bill;

use App\Models\Employee\Employee;
use App\Models\Order\Order;
use App\Models\PaymentMethod\PaymentMethod;
use DateTime;

class Bill
{
  private int $id;
  private float $total;
  private float $discount;
  private PaymentMethod $payment;
  private Employee $employee;
  private \DateTime $creationDate;
  private Order $order;

  public function __construct(
    float $total,
    Employee $employee,
    PaymentMethod $payment,
    Order $order,
    float $discount = 0,
    \DateTime $creationDate = new DateTime(),
    int $id = 0,
  ) {
    $this->id = $id;
    $this->total = $total;
    $this->creationDate = $creationDate;
    $this->payment = $payment;
    $this->employee = $employee;
    $this->discount = $discount;
    $this->order = $order;
  }

  public function setOrder(Order $order): void
  {
    $this->order = $order;
  }

  public function getOrder(): Order
  {
    return $this->order;
  }

  public function getId(): int
  {
    return $this->id;
  }

  public function setId(int $id): Bill
  {
    $this->id = $id;

    return $this;
  }

  public function setEmployee(Employee $employee): void
  {
    $this->employee = $employee;
  }

  public function getEmployee(): Employee
  {
    return $this->employee;
  }

  public function setPayment(PaymentMethod $payment): Bill
  {
    $this->payment = $payment;

    return $this;
  }

  public function getPayment(): PaymentMethod
  {
    return $this->payment;
  }

  public function setCreationDate(\DateTime $creationDate): Bill
  {
    $this->creationDate = $creationDate;

    return $this;
  }

  public function getCreationDate(): \DateTime
  {
    return $this->creationDate;
  }

  public function getDiscount(): float
  {
    return $this->discount;
  }

  public function setDiscount(float $discount): Bill
  {
    $this->discount = $discount;

    return $this;
  }

  public function getTotal(): float
  {
    return $this->total;
  }

  public function setTotal(float $total): Bill
  {
    $this->total = $total;

    return $this;
  }
}
