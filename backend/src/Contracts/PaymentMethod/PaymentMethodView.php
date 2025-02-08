<?php

declare(strict_types=1);

namespace App\Contracts\PaymentMethod;

interface PaymentMethodView
{
  /**
   * @param array<mixed>|object|string $data
   */
  public function respondWith($data): void;
}
