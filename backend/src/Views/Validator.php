<?php

namespace App\Views;

use App\Exceptions\Validator\ValidatorException;

class Validator
{
  /**
   * @var array<string>
   */
  private array $messages = [];
  /**
   * @var array{type: string,
   * length: int | null,
   * const: string | null,
   * required: bool,
   * min: int | null,
   * enum: array<string> | null,
   * checkPastDate: bool,
   * dateFormat: string,
   * itemsArray: array{properties: array<string, mixed>} | null
   * } $fieldProps
   */
  private array $fieldProps = [
    'type' => '',
    'length' => null,
    'const' => null,
    'required' => false,
    'min' => null,
    'enum' => null,
    'checkPastDate' => true,
    'dateFormat' => 'Y-m-d H:i:s',
    'itemsArray' => null,
  ];
  /**
   * @var mixed value
   */
  private mixed $value;
  /**
   * @var string
   */
  private string $fieldName;

  /**
   * @param array<string, array{
   *  type: string,
   *  length?: int,
   *  const?: string,
   *  required?: bool,
   *  min?: int,
   *  enum?: array<string>,
   *  checkPastDate?: bool,
   *  dateFormat?: string,
   *  itemsArray?: array{properties: array<string, mixed>} | null }> $fields
   * @param array<string, mixed>                                                                                               $payload
   *
   * @return array<string> $messages
   */
  public function validate(array $fields, array $payload): array
  {
    $this->messages = [];

    foreach ($fields as $key => $config) {
      $this->fieldProps['type'] = $config['type'];
      $this->fieldProps['itemsArray'] = $config['itemsArray'] ?? null;
      $this->fieldProps['length'] = $config['length'] ?? null;
      $this->fieldProps['const'] = $config['const'] ?? null;
      $this->fieldProps['required'] = $config['required'] ?? true;
      $this->fieldProps['min'] = $config['min'] ?? null;
      $this->fieldProps['enum'] = $config['enum'] ?? null;
      $this->fieldProps['checkPastDate'] = $config['checkPastDate'] ?? true;
      $this->fieldProps['dateFormat'] = $config['dateFormat'] ?? 'Y-m-d H:i:s';
      $this->fieldName = $key;

      if (isset($payload[$key])) {
        $this->value = $payload[$key];
      } else {
        if ($this->isRequired()) {
          $this->messages[] = "Campo {$this->fieldName} obrigatório";
        }

        continue;
      }

      $this->checkConst();
      $this->checkEnum();
      $this->checkValue();
    }

    return $this->messages;
  }

  private function isRequired(): bool
  {
    return $this->fieldProps['required'];
  }

  private function checkEnum(): void
  {
    $value = $this->value;
    $fieldName = $this->fieldName;
    $enum = $this->fieldProps['enum'];

    if ($enum && !in_array($value, $enum)) {
      $this->messages[] = "O campo {$fieldName} deve ter um dos seguintes valores: " . implode(', ', $enum);
    }
  }

  private function checkConst(): void
  {
    $value = $this->value;
    $fieldName = $this->fieldName;
    $const = $this->fieldProps['const'];

    if ($const && $value !== $const) {
      $this->messages[] = "O único valor permitido para o campo {$fieldName} é '{$const}'";
    }
  }

  private function checkValue(): void
  {
    $value = $this->value;
    $fieldName = $this->fieldName;
    $type = $this->fieldProps['type'];
    $length = $this->fieldProps['length'];
    $min = $this->fieldProps['min'];

    switch ($type) {
      case 'array': {
        if (!is_array($value)) {
          $this->messages[] = "O campo {$fieldName} deve ser um array";
        } elseif ($this->fieldProps['itemsArray']) {
          $itemsArray = $this->fieldProps['itemsArray'];
          /**
           * @var array<string, array{type: string, min?: int}> $properties
           */
          $properties = $itemsArray['properties'];
          $requiredKeys = array_keys($properties);

          /** @var array<string, mixed> $item */
          foreach ($value as $item) {
            $itemKeys = array_keys($item);
            $keysDiff = array_diff($requiredKeys, $itemKeys);

            if (!empty($keysDiff)) {
              $this->messages[] = "O campo {$fieldName} deve ser um array de objetos com as seguintes chaves: " . implode(', ', $requiredKeys);
            }

            $filterResult = array_filter($item, function ($key) use ($item, $properties) {
              if (isset($properties[$key])) {
                switch ($properties[$key]['type']) {
                  case 'numeric': {
                    return !is_numeric($item[$key]) || isset($properties[$key]['min']) && intval($item[$key]) < intval($properties[$key]['min']);
                  }
                  default: {
                    return false;
                  }
                }
              }

              return false;
            }, ARRAY_FILTER_USE_KEY);

            if (!empty($filterResult)) {
              $this->messages[] = 'As propriedades '
              . implode(', ', $requiredKeys) .
              ' devem ser do tipo ' .
              implode(', ', array_map(fn ($key) => $properties[$key]['type'], $requiredKeys)) .
              ' respectivamente e devem ser maiores que 0';
            }
          }
        }
        break;
      }
      case 'string':
        if (!is_string($value)) {
          $this->messages[] = "O campo {$fieldName} deve ser um texto";
        } elseif ($length && strlen($value) < $length) {
          $this->messages[] = "O campo {$fieldName} deve ter pelo menos {$length} caracteres";
        }
        break;
      case 'numeric':
        if (!is_numeric($value)) {
          $this->messages[] = "O campo {$fieldName} deve ser númerico";
        } elseif ($min && intval($value) < $min) {
          $this->messages[] = "O campo {$fieldName} deve ser no mínimo {$min}";
        }
        break;
      case 'Date':
        if (
          !is_string($value)
          || \DateTime::createFromFormat($this->fieldProps['dateFormat'], $value) === false
          || ($this->fieldProps['checkPastDate'] && new \DateTime($value) < new \DateTime())
        ) {
          $format = $this->fieldProps['dateFormat'] === 'Y-m-d H:i:s' ? 'YYYY-mm-dd H:i:s' : $this->fieldProps['dateFormat'];
          $this->messages[] = "O campo {$fieldName} deve ser uma data válida no seguinte formato {$format}";
        }
        break;
      default:
        throw new ValidatorException("Unknown type: {$type}");
    }
  }
}
