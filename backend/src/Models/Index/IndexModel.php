<?php

namespace App\Models\Index;

use App\Utils\Env;

class IndexModel
{
  /**
   * @return array<string, string>
   */
  public function getData()
  {
    return [
      'employees' => Env::get('HOST_URL') . '/api/v1/employees',
      'tables' => Env::get('HOST_URL') . '/api/v1/tables',
      'reservations' => Env::get('HOST_URL') . '/api/v1/reservations',
    ];
  }
}
