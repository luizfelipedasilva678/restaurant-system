<?php

namespace App\Models\PaymentMethod;

use App\Contracts\PaymentMethod\PaymentMethodRepository;
use App\Enums\HttpCodes;
use App\Exceptions\PaymentMethod\PaymentMethodRepositoryException;
use Exception;
use PDO;

class PaymentMethodRepositoryInRDB implements PaymentMethodRepository
{
  private PDO $pdo;

  public function __construct(PDO $pdo)
  {
    $this->pdo = $pdo;
  }

  /**
   * @return array<PaymentMethod>
   */
  public function getAll()
  {
    try {
      /**
       * @var array<PaymentMethod> $paymentsMethod;
       */
      $paymentsMethod = [];

      $sql = <<<'SQL'
        SELECT id, name FROM PaymentMethod;
      SQL;

      $stmt = $this->pdo->prepare($sql);

      $stmt->execute();

      /** @var array{id: int, name: string} $reg */
      foreach ($stmt as $reg) {
        $paymentsMethod[] = new PaymentMethod($reg['id'], $reg['name']);
      }

      return $paymentsMethod;
    } catch (Exception $error) {
      throw new PaymentMethodRepositoryException('Erro ao buscar metodos de pagamento', HttpCodes::HTTP_SERVER_ERROR->value);
    }
  }
}
