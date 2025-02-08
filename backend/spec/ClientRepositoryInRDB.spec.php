<?php

namespace Tests;

use App\Models\Client\Client;
use App\Models\Client\ClientRepositoryInRDB;
use App\Models\Phone\Phone;
use App\Utils\Env;
use App\Utils\PDOBuilder;

use function Kahlan\describe;
use function Kahlan\expect;
use function Kahlan\it;
use function App\Utils\Tests\cleanTestDB;
use function Kahlan\beforeAll;

describe('ClientRepositoryInRDB', function () {
  $this->repository = null;
  $this->pdo = null;

  beforeAll(function () {
    Env::initEnv(__DIR__ . '/../config/.env');
    $this->pdo = PDOBuilder::build(true);
    cleanTestDB($this->pdo);
    $this->repository = new ClientRepositoryInRDB($this->pdo);
  });

  it('should create the repository correctly', function () {
    expect($this->repository)->toBeAnInstanceOf('App\\Models\\Client\\ClientRepositoryInRDB');
  });

  it('should create a client correctly', function () {
    $client = $this->repository->create(new Client(0, 'Client 1'));

    expect($client)->toBeAnInstanceOf('App\\Models\\Client\\Client');
    expect($client->getName())->toBe('Client 1');
    expect($client->getId())->toBeGreaterThan(0);
  });

  it('should create the client phone correctly', function () {
    $client = $this->repository->create(new Client(0, 'Client 1', new Phone(0, '(22) 2222-2222')));

    expect($client)->toBeAnInstanceOf('App\\Models\\Client\\Client');
    expect($client->getName())->toBe('Client 1');
    expect($client->getId())->toBeGreaterThan(0);

    expect($client->getPhone())->toBeAnInstanceOf('App\\Models\\Phone\\Phone');
    expect($client->getPhone()->getNumber())->toBe('(22) 2222-2222');
    expect($client->getPhone()->getId())->toBeGreaterThan(0);
  });

  it('should not create the client phone when the phone is empty', function () {
    $client = $this->repository->create(new Client(0, 'Client 1'));

    expect($client->getPhone()->getNumber())->toBe('');
    expect($client->getPhone()->getId())->toBe(0);
  });
});
