<?php

declare(strict_types=1);

namespace App\Models\Bill;

use App\Contracts\Bill\BillRepository;
use App\Enums\HttpCodes;
use App\Exceptions\Bill\BillRepositoryException;
use Exception;

class BillRepositoryInRDB implements BillRepository
{
  private \PDO $pdo;

  public function __construct(\PDO $pdo)
  {
    $this->pdo = $pdo;
  }

  /**
   * @return array<string, int>
   */
  public function getSalesByDay(string $startDate, string $endDate): array
  {
    try {
      $sql = <<<SQL
        SELECT
        DATE_FORMAT(b.creation_date, '%Y-%m-%d') as date,
        count(id) as sales
        FROM Bill b
        WHERE
        DATE_FORMAT(b.creation_date, '%Y-%m-%d') BETWEEN :startDate AND :endDate
        GROUP BY DATE_FORMAT(b.creation_date, "%Y-%M-%d")
      SQL;

      $stmt = $this->pdo->prepare($sql);
      $stmt = $this->pdo->prepare($sql);
      $stmt->bindValue(':startDate', $startDate);
      $stmt->bindValue(':endDate', $endDate);
      $stmt->execute();
      $stmt->execute();

      /**
       * @var array<string, int>
       */
      $result = $stmt->fetchAll(\PDO::FETCH_ASSOC);

      return $result;
    } catch (\PDOException $exception) {
      throw new BillRepositoryException('Erro ao encontrar vendas por dia', HttpCodes::HTTP_SERVER_ERROR->value);
    }
  }

  /**
   * @return array<string, int>
   */
  public function getSalesByCategory(string $startDate, string $endDate): array
  {
    try {
      $sql = <<<SQL
        SELECT
        c.name as category,
        count(c.id) as sales
        FROM Bill b
        INNER JOIN TableOrder torder
        ON b.table_order_id = torder.id
        INNER JOIN OrderItem oi
        ON torder.id = oi.table_order_id
        INNER JOIN Item i
        ON i.id = oi.item_id
        INNER JOIN Category c
        ON c.id = i.category_id
        WHERE
        DATE_FORMAT(b.creation_date, '%Y-%m-%d') BETWEEN :startDate AND :endDate
        GROUP BY c.name
      SQL;

      $stmt = $this->pdo->prepare($sql);
      $stmt = $this->pdo->prepare($sql);
      $stmt->bindValue(':startDate', $startDate);
      $stmt->bindValue(':endDate', $endDate);
      $stmt->execute();
      $stmt->execute();

      /**
       * @var array<string, int>
       */
      $result = $stmt->fetchAll(\PDO::FETCH_ASSOC);

      return $result;
    } catch (\PDOException $exception) {
      throw new BillRepositoryException('Erro ao encontrar vendas por categoria', HttpCodes::HTTP_SERVER_ERROR->value);
    }
  }

  /**
   * @return array<string, int>
   */
  public function getSalesByEmployee(string $startDate, string $endDate): array
  {
    try {
      $sql = <<<SQL
        SELECT
        e.name as employee,
        count(e.id) as sales
        FROM Bill b
        INNER JOIN Employee e
        ON b.employee_id = e.id
        WHERE
        DATE_FORMAT(b.creation_date, '%Y-%m-%d') BETWEEN :startDate AND :endDate
        GROUP BY e.name
      SQL;

      $stmt = $this->pdo->prepare($sql);
      $stmt = $this->pdo->prepare($sql);
      $stmt->bindValue(':startDate', $startDate);
      $stmt->bindValue(':endDate', $endDate);
      $stmt->execute();
      $stmt->execute();

      /**
       * @var array<string, int>
       */
      $result = $stmt->fetchAll(\PDO::FETCH_ASSOC);

      return $result;
    } catch (\PDOException $exception) {
      throw new BillRepositoryException('Erro ao encontrar vendas por funcionário', HttpCodes::HTTP_SERVER_ERROR->value);
    }
  }

  /**
   * @return array<string, int>
   */
  public function getSalesByPaymentMethod(string $startDate, string $endDate): array
  {
    try {
      $sql = <<<SQL
        SELECT
        pm.name as payment_method,
        count(pm.id) as sales
        FROM Bill b
        INNER JOIN PaymentMethod pm
        ON b.payment_method_id = pm.id
        WHERE
        DATE_FORMAT(b.creation_date, '%Y-%m-%d') BETWEEN :startDate AND :endDate
        GROUP BY pm.name
      SQL;

      $stmt = $this->pdo->prepare($sql);
      $stmt->bindValue(':startDate', $startDate);
      $stmt->bindValue(':endDate', $endDate);
      $stmt->execute();

      /**
       * @var array<string, int>
       */
      $result = $stmt->fetchAll(\PDO::FETCH_ASSOC);

      return $result;
    } catch (\PDOException $exception) {
      throw new BillRepositoryException('Erro ao encontrar vendas por forma de pagamento', HttpCodes::HTTP_SERVER_ERROR->value);
    }
  }

  public function create(Bill $bill): bool
  {
    try {
      $sql = <<<'SQL'
			  INSERT INTO Bill(total, discount, creation_date, employee_id, payment_method_id, table_order_id) VALUES (:total, :discount, :creationDate, :employeeId, :paymentMethodId, :tableOrderid);
			SQL;

      $stmt = $this->pdo->prepare($sql);

      $stmt->bindValue(':total', $bill->getTotal());
      $stmt->bindValue(':discount', $bill->getDiscount());
      $stmt->bindValue(':creationDate', $bill->getCreationDate()->format('Y-m-d H:i:s'));
      $stmt->bindValue(':employeeId', $bill->getEmployee()->getId());
      $stmt->bindValue(':paymentMethodId', $bill->getPayment()->getId());
      $stmt->bindValue(':tableOrderid', $bill->getOrder()->getId());

      $stmt->execute();

      if ($stmt->rowCount() <= 0) {
        return false;
      }

      return true;
    } catch (Exception $error) {
      throw new BillRepositoryException('Erro ao criar conta', HttpCodes::HTTP_SERVER_ERROR->value);
    }
  }
}
