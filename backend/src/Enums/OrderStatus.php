<?php

declare(strict_types=1);

namespace App\Enums;

enum OrderStatus: string
{
  case open = 'open';
  case completed = 'completed';
}
