<?php

namespace App\Models\Client;

use App\Contracts\Client\ClientRepository;
use App\Enums\HttpCodes;
use App\Exceptions\Client\ClientRepositoryException;

class ClientRepositoryInRDB implements ClientRepository
{
  private \PDO $pdo;

  public function __construct(\PDO $pdo)
  {
    $this->pdo = $pdo;
  }

  public function create(Client $client): Client
  {
    try {
      $sql = <<<'SQL'
				  INSERT INTO Client (name) VALUES (:name);
				SQL;

      $ps = $this->pdo->prepare($sql);
      $ps->bindValue(':name', $client->getName(), \PDO::PARAM_STR);
      $ps->execute();

      $lastClientId = $this->pdo->lastInsertId() ? intval($this->pdo->lastInsertId()) : 0;

      $client->setId($lastClientId);

      if (empty($client->getPhone()->getNumber())) {
        return $client;
      }

      $sql = <<<'SQL'
        INSERT INTO ClientPhone (phone, client_id) VALUES (?, ?);
      SQL;

      $ps = $this->pdo->prepare($sql);
      $ps->execute([$client->getPhone()->getNumber(), $client->getId()]);

      $lastClientPhoneId = $this->pdo->lastInsertId() ? intval($this->pdo->lastInsertId()) : 0;

      $client->getPhone()->setId($lastClientPhoneId);

      return $client;
    } catch (\PDOException $exception) {
      throw new ClientRepositoryException($exception->getMessage(), HttpCodes::HTTP_SERVER_ERROR->value);
    }
  }
}
