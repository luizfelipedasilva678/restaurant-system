<?php

declare(strict_types=1);

namespace App\Utils;

class Env
{
  public static function initEnv(string $file): void
  {
    $envs = parse_ini_file($file);

    if (!is_array($envs)) {
      return;
    }

    foreach ($envs as $key => $value) {
      if (!is_string($key) || !is_string($value)) {
        continue;
      }

      Env::set($key, $value);
    }
  }

  public static function get(string $key): string
  {
    return getenv($key) ?: '';
  }

  public static function has(string $key): bool
  {
    return false !== getenv($key);
  }

  public static function set(string $key, string $value): void
  {
    putenv("{$key}={$value}");
  }
}
