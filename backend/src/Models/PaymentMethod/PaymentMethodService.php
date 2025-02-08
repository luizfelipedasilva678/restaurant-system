<?php

declare(strict_types=1);

namespace App\Models\PaymentMethod;

use App\Contracts\PaymentMethod\PaymentMethodRepository;
use App\Exceptions\PaymentMethod\PaymentMethodServiceException;

class PaymentMethodService
{
  private PaymentMethodRepository $repository;

  public function __construct(PaymentMethodRepository $repository)
  {
    $this->repository = $repository;
  }

  /**
   * @return array<PaymentMethod>
   */
  public function getPaymentsMethods()
  {
    try {
      return $this->repository->getAll();
    } catch (PaymentMethodServiceException $exception) {
      throw new PaymentMethodServiceException($exception->getMessage(), $exception->getCode());
    }
  }
}
