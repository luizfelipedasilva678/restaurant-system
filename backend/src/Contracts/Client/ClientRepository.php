<?php

declare(strict_types=1);

namespace App\Contracts\Client;

use App\Models\Client\Client;

interface ClientRepository
{
  public function create(Client $client): Client;
}
