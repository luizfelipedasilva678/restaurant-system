<?php

declare(strict_types=1);

namespace App\Contracts\PaymentMethod;

use App\Models\PaymentMethod\PaymentMethod;

interface PaymentMethodRepository
{
  /** @return array<PaymentMethod> */
  public function getAll();
}
