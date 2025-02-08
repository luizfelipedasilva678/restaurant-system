<?php

declare(strict_types=1);

namespace App\Enums;

enum EmployeeType: string
{
  case attendant = 'attendant';
  case manager = 'manager';
}
