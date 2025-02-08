<?php

namespace Tests;

use App\Views\Sanitizer;

use function Kahlan\describe;
use function Kahlan\it;
use function Kahlan\expect;

define('MALICIOUS_SCRIPT', '<script>alert("Hello")</script>');
define('EXPECTED_RESULT', '&lt;script&gt;alert&quot;Hello&quot;&lt;/script&gt;');
define('EMAIL', 'a@b.com');

describe('Sanitizer', function () {
  $this->sanitizer = null;

  beforeAll(function () {
    $this->sanitizer = new Sanitizer();
  });

  it('should sanitize input correctly', function () {
    $input = [
      'name' => MALICIOUS_SCRIPT,
      'email' => EMAIL,
    ];

    expect($this->sanitizer->sanitize($input))->toEqual([
      'name' => EXPECTED_RESULT,
      'email' => EMAIL,
    ]);
  });

  it('should not sanitize non-string values', function () {
    $input = [
      'name' => MALICIOUS_SCRIPT,
      'email' => EMAIL,
      'age' => 25,
      'employee' => true
    ];

    expect($this->sanitizer->sanitize($input))->toEqual([
      'name' => EXPECTED_RESULT,
      'email' => EMAIL,
      'age' => 25,
      'employee' => true
    ]);
  });

  it('should not cause an error when encountering nested array', function () {
    $input = [
      'name' => MALICIOUS_SCRIPT,
      'email' => EMAIL,
      'nestedArray' => [
        'nestedValue1' => 'value1',
        'nestedValue2' => 'value2',
      ]
    ];

    expect($this->sanitizer->sanitize($input))->toEqual([
      'name' => EXPECTED_RESULT,
      'email' => EMAIL,
      'nestedArray' => [
        'nestedValue1' => 'value1',
        'nestedValue2' => 'value2',
      ]
    ]);
  });

  it('should remove special characters', function () {
    $input = [
      'name' => 't!#$e()stetes',
      'email' => EMAIL,
    ];

    expect($this->sanitizer->sanitize($input))->toEqual([
      'name' => 'testetes',
      'email' => EMAIL,
    ]);
  });

  it('should maintain all keys from the original array', function () {
    $input = [
      'name' => MALICIOUS_SCRIPT,
      'email' => EMAIL,
      'nestedArray' => [
        'nestedValue1' => 'value1',
        'nestedValue2' => 'value2',
      ]
    ];

    expect(array_keys($this->sanitizer->sanitize($input)))->toEqual(array_keys($input));
  });
});
