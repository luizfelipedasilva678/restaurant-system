<?php

namespace App\Models\Bill;

class BillDTO implements \JsonSerializable
{
  public int $paymentMethodId;
  public int $employeeId;
  public int $orderId;
  public float $total;
  public float $discount;

  public function __construct(
    int $paymentMethodId = 0,
    int $employeeId = 0,
    int $orderId = 0,
    float $total = 0,
    float $discount = 0
  ) {
    $this->paymentMethodId = $paymentMethodId;
    $this->employeeId = $employeeId;
    $this->orderId = $orderId;
    $this->total = $total;
    $this->discount = $discount;
  }

  public function jsonSerialize(): mixed
  {
    return get_object_vars($this);
  }
}
