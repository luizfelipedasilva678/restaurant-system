<?php

declare(strict_types=1);

namespace App\Models\Bill;

use App\Models\Employee\Employee;
use App\Models\Order\Order;
use App\Models\PaymentMethod\PaymentMethod;

class BillMapper
{
  public static function toDTO(Bill $bill): BillDTO
  {
    return new BillDTO(
      $bill->getPayment()->getId(),
      $bill->getEmployee()->getId(),
      $bill->getOrder()->getId(),
      $bill->getTotal(),
      $bill->getDiscount()
    );
  }

  public static function toEntity(BillDTO $dto): Bill
  {
    return new Bill(
      $dto->total,
      new Employee($dto->employeeId),
      new PaymentMethod($dto->paymentMethodId),
      new Order($dto->orderId),
      $dto->discount
    );
  }
}
