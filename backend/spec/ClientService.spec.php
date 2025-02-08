<?php

namespace Tests;

use App\Models\Client\Client;
use App\Models\Client\ClientRepositoryInRDB;
use App\Models\Client\ClientService;
use App\Utils\Env;
use App\Utils\PDOBuilder;

use function App\Utils\Tests\cleanTestDB;
use function Kahlan\beforeAll;
use function Kahlan\describe;
use function Kahlan\expect;
use function Kahlan\it;

describe('ClientService', function () {
  $this->model = null;
  $this->pdo = null;

  beforeAll(function () {
    Env::initEnv(__DIR__ . '/../config/.env');
    $this->pdo = PDOBuilder::build(true);
    cleanTestDB($this->pdo);
    $this->service = new ClientService(new ClientRepositoryInRDB($this->pdo));
  });

  it('should create the service correctly', function () {
    expect($this->service)->toBeAnInstanceOf('App\Models\Client\ClientService');
  });

  it('should create a client correctly', function () {
    $entity = new Client(0, 'Test');
    $client = $this->service->create($entity);

    expect($client)->toBeAnInstanceOf('App\Models\Client\Client');
    expect($client->getName())->toBe('Test');
    expect($client->getId())->toBeGreaterThan(0);
  });
});
