<?php

namespace App\Models\Item;

use App\Contracts\Item\ItemRepository;
use App\Enums\HttpCodes;
use App\Exceptions\Item\ItemRepositoryException;
use App\Models\Category\Category;
use PDOException;

class ItemRepositoryInRDB implements ItemRepository
{
  private \PDO $pdo;

  public function __construct(\PDO $pdo)
  {
    $this->pdo = $pdo;
  }

  /**
   * @return array{data: Item[], count: int}
   */
  public function getItems(
    int $limit,
    int $offset
  ): array {
    try {
      $sql = <<<SQL
        SELECT i.id, i.code, i.description, i.price, c.name as category, i.category_id as category_id
        FROM Item i
        INNER JOIN Category c ON i.category_id = c.id
        ORDER BY i.id LIMIT :limit OFFSET :offset
      SQL;

      $countSql = <<<COUNT_SQL
        SELECT COUNT(*) as total_items FROM Item
      COUNT_SQL;

      $stmt = $this->pdo->prepare($sql);
      $stmt->bindValue(':limit', $limit, \PDO::PARAM_INT);
      $stmt->bindValue(':offset', $offset, \PDO::PARAM_INT);
      $stmt->setFetchMode(\PDO::FETCH_ASSOC);
      $stmt->execute();

      $items = [];

      /**
       * @var array{id: int, code: string, category: string, category_id: int, description: string, price: float} $item
       */
      foreach ($stmt as $item) {
        array_push($items, new Item(
          $item['id'],
          $item['code'],
          new Category(
            $item['category_id'],
            $item['category']
          ),
          $item['description'],
          $item['price']
        ));
      }

      $stmt = $this->pdo->query($countSql);
      $count = (int) ($stmt ? $stmt->fetchColumn() : 0);

      return  [
        'data' => $items,
        'count' => $count
      ];
    } catch (PDOException $exception) {
      throw new ItemRepositoryException('Erro ao encontrar itens', HttpCodes::HTTP_SERVER_ERROR->value);
    }
  }
}
