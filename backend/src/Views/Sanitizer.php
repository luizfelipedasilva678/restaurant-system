<?php

namespace App\Views;

define('PATTERN', '/[!#$%^&*()_+={}\[\];|~`]/i');

class Sanitizer
{
  /**
   * @param array<string, mixed> $input
   *
   * @return array<string, mixed>
   */
  public function sanitize($input): array
  {
    /** @var array<string, mixed> $sanitized */
    $sanitized = [];
    foreach ((array) $input as $key => $value) {
      if (!is_string($value)) {
        $sanitized[$key] = $value;
        continue;
      }

      $sanitized[$key] = htmlspecialchars(preg_replace(PATTERN, '', $value) ?: $value);
    }

    return $sanitized;
  }
}
