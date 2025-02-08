<?php

namespace Tests;

use App\Views\Validator;
use App\Exceptions\Validator\ValidatorException;

use function Kahlan\describe;
use function Kahlan\it;
use function Kahlan\expect;

describe('Validator', function () {
  $this->validator = null;

  beforeEach(function () {
    $this->validator = new Validator();
  });

  it('should return errors when data is not an array', function () {
    $messages = $this->validator->validate(
      [
        'items' => [
          'type' => 'array',
          'itemsArray' => [
            'type' => 'object',
            'properties' => [
              'itemId' => [
                'type' => 'numeric'
              ],
              'quantity' => [
                'type' => 'numeric'
              ]
            ]
          ]
        ]
      ],
      [
        'items' => 2
      ]
    );

    expect(count($messages))->toBeGreaterThan(0);
  });

  it('should validate arrays correctly', function () {
    $messages = $this->validator->validate(
      [
        'items' => [
          'type' => 'array',
          'itemsArray' => [
            'type' => 'object',
            'properties' => [
              'itemId' => [
                'type' => 'numeric'
              ],
              'quantity' => [
                'type' => 'numeric'
              ]
            ]
          ]
        ]
      ],
      [
        'items' => [
          [
            'itemId' => '2',
            'quantity' => '2',
          ]
        ]
      ]
    );

    expect($messages)->toBe([]);
  });

  it('should not the date format and if date is past', function () {
    $messages = $this->validator->validate(
      [
        'date' => [
          'type' => 'Date',
          'checkPastDate' => true,
          'dateFormat' => 'Y-m-d',
        ]
      ],
      [
        'date' => '2020-01-01'
      ]
    );

    expect($messages)->toBe([
      'O campo date deve ser uma data válida no seguinte formato Y-m-d'
    ]);
  });

  it('should not check if date is past', function () {
    $messages = $this->validator->validate(
      [
        'date' => [
          'type' => 'Date',
          'checkPastDate' => false,
          'dateFormat' => 'Y-m-d',
        ]
      ],
      [
        'date' => '2020-01-01'
      ]
    );

    expect($messages)->toBe([]);
  });

  it('should not return an error if no required value is not being sent', function () {
    $messages = $this->validator->validate(
      [
        'name' => [
          'type' => 'string',
          'required' => false,
        ]
      ],
      []
    );

    expect($messages)->toBe([]);
  });

  it('should ignore length when type is not string', function () {
    $messages = $this->validator->validate(
      [
        'salary' => [
          'type' => 'numeric',
          'length' => 5,
        ]
      ],
      [
        'salary' => 500
      ]
    );

    expect($messages)->toBe([]);
  });

  it('should validate length correctly', function () {
    $messages = $this->validator->validate(
      [
        'name' => [
          'type' => 'string',
          'length' => 5,
        ]
      ],
      [
        'name' => 'mar'
      ]
    );

    expect($messages)->toBe(['O campo name deve ter pelo menos 5 caracteres']);
  });

  it('should validate min correctly', function () {
    $messages = $this->validator->validate(
      [
        'salary' => [
          'type' => 'numeric',
          'min' => 1000,
        ]
      ],
      [
        'salary' => 500
      ]
    );

    expect($messages)->toBe(['O campo salary deve ser no mínimo 1000']);
  });

  it('should not accept other values given that is a const', function () {
    $messages = $this->validator->validate(
      [
        'email' => [
          'type' => 'string',
          'const' => 'l2yTt@example.com',
        ]
      ],
      [
        'email' => 'test@gmail.com'
      ]
    );

    expect($messages)->toBe(['O único valor permitido para o campo email é \'l2yTt@example.com\'']);
  });

  it('should accept numbers in enum', function () {
    $messages = $this->validator->validate(
      [
        'enabled' => [
          'type' => 'numeric',
          'required' => true,
          'enum' => [1, 0]
        ]
      ],
      [
        'enabled' => 1
      ]
    );

    expect($messages)->toBe([]);
  });

  it('should return an error when value is not in enum', function () {
    $messages = $this->validator->validate(
      [
        'enabled' => [
          'type' => 'string',
          'required' => true,
          'enum' => ['true', 'false']
        ]
      ],
      [
        'enabled' => 'unknown'
      ]
    );

    expect($messages)->toBe(['O campo enabled deve ter um dos seguintes valores: true, false']);
  });

  it('should validate enum values correctly', function () {
    $messages = $this->validator->validate(
      [
        'enabled' => [
          'type' => 'string',
          'required' => true,
          'enum' => ['true', 'false']
        ]
      ],
      [
        'enabled' => 'true'
      ]
    );

    expect($messages)->toBe([]);
  });

  it('should cause an error when a unknown type is given', function () {
    $closure  = function () {
      $this->validator->validate(
        [
          'email' => [
            'type' => 'unknown',
            'required' => true
          ]
        ],
        [
          'email' => 'l2yTt@example.com'
        ]
      );
    };

    expect($closure)->toThrow(new ValidatorException());
  });

  it('should return an error when a past date is given', function () {
    $messages = $this->validator->validate(
      [
        'date' => [
          'type' => 'Date',
          'required' => true
        ]
      ],
      [
        'date' => '2020-01-01'
      ]
    );

    expect($messages)->toBe(['O campo date deve ser uma data válida no seguinte formato YYYY-mm-dd H:i:s']);
  });

  it('should return an empty error message', function () {
    $messages = $this->validator->validate(
      [
        'email' => [
          'type' => 'string',
          'required' => true
        ]
      ],
      [
        'email' => 'l2yTt@example.com'
      ]
    );

    expect($messages)->toBe([]);
  });

  it('should return an error message given that a required field is missing', function () {
    $messages = $this->validator->validate(
      [
        'email' => [
          'type' => 'string',
          'required' => true
        ]
      ],
      []
    );

    expect($messages)->toBe(['Campo email obrigatório']);
  });

  it('should return an error message given that the field type is invalid', function () {
    $messages = $this->validator->validate(
      [
        'email' => [
          'type' => 'string',
        ]
      ],
      [
        'email' => 1213
      ]
    );

    expect($messages)->toBe(['O campo email deve ser um texto']);
  });

  it('should return and error message given that a required field is missing', function () {
    $messages = $this->validator->validate(
      [
        'email' => [
          'type' => 'string',
        ]
      ],
      []
    );

    expect($messages)->toBe(['Campo email obrigatório']);
  });
});
