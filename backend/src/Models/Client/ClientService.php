<?php

namespace App\Models\Client;

use App\Contracts\Client\ClientRepository;
use App\Exceptions\Client\ClientRepositoryException;
use App\Exceptions\Client\ClientServiceException;

class ClientService
{
  private ClientRepository $repository;

  public function __construct(ClientRepository $repository)
  {
    $this->repository = $repository;
  }

  public function create(Client $client): Client
  {
    try {
      return $this->repository->create($client);
    } catch (ClientRepositoryException $exception) {
      throw new ClientServiceException($exception->getMessage(), $exception->getCode());
    }
  }
}
