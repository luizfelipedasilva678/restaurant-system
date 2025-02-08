<?php

declare(strict_types=1);

namespace App\Models\Employee;

use App\Contracts\Employee\EmployeeRepository;
use App\Enums\EmployeeType;
use App\Enums\HttpCodes;
use App\Exceptions\Employee\EmployeeRepositoryException;
use PDO;

class EmployeeRepositoryInRDB implements EmployeeRepository
{
  private \PDO $pdo;

  public function __construct(\PDO $pdo)
  {
    $this->pdo = $pdo;
  }

  /**
   * @return array{data: Employee[], count: int}
   */
  public function getAll(int $limit, int $offset)
  {
    try {
      $sql = <<<'SQL'
			  SELECT id, name, login, type FROM Employee ORDER BY id LIMIT :limit OFFSET :offset;
			SQL;

      $countSql = 'SELECT COUNT(*) as total_employees FROM Employee';

      $employees = [];

      $ps = $this->pdo->prepare($sql);
      $ps->setFetchMode(\PDO::FETCH_ASSOC);
      $ps->bindValue(':limit', $limit, \PDO::PARAM_INT);
      $ps->bindValue(':offset', $offset, \PDO::PARAM_INT);
      $ps->execute();

      /** @var array{id: int, name: string, login: string, type: string} $reg */
      foreach ($ps as $reg) {
        $employee = new Employee($reg['id'], $reg['name'], $reg['login'], EmployeeType::from($reg['type']));

        array_push($employees, $employee);
      }

      $stmt = $this->pdo->query($countSql);
      $count = (int) ($stmt ? $stmt->fetchColumn() : 0);

      return [
        'data' => $employees,
        'count' => $count
      ];
    } catch (\PDOException $exception) {
      throw new EmployeeRepositoryException('Erro ao buscar funcionários', HttpCodes::HTTP_SERVER_ERROR->value);
    }
  }

  public function getSaltByLogin(string $login): string
  {
    try {
      $sql = <<<'SQL'
			  SELECT salt FROM Employee WHERE login = :login;
			SQL;

      $ps = $this->pdo->prepare($sql);

      $ps->bindValue(':login', $login);
      $ps->execute();

      /** @var array{salt: string} | null */
      $row = $ps->fetch(PDO::FETCH_ASSOC);

      if ($ps->rowCount() <= 0 || $row === null) {
        throw new EmployeeRepositoryException('Usuário ou senha inválidos', HttpCodes::HTTP_BAD_REQUEST->value);
      }

      return $row['salt'];
    } catch (\PDOException $exception) {
      throw new EmployeeRepositoryException('Usuário ou senha inválidos', HttpCodes::HTTP_SERVER_ERROR->value);
    }
  }

  public function getByLoginAndPassword(string $login, string $password): Employee
  {
    try {
      $sql = <<<'SQL'
			  SELECT id, name, login, type FROM Employee WHERE password = :password;
			SQL;

      $ps = $this->pdo->prepare($sql);

      $ps->bindValue(':password', $password);
      $ps->execute();

      /** @var array{id: int, name: string, login: string, type: string} | null */
      $row = $ps->fetch(PDO::FETCH_ASSOC);

      if ($ps->rowCount() <= 0 || $row === null) {
        throw new EmployeeRepositoryException('Usuário ou senha inválidos', HttpCodes::HTTP_BAD_REQUEST->value);
      }

      return new Employee($row['id'], $row['name'], $row['login'], EmployeeType::from($row['type']));
    } catch (\PDOException $exception) {
      throw new EmployeeRepositoryException('Usuário ou senha inválidos', HttpCodes::HTTP_SERVER_ERROR->value);
    }
  }
}
