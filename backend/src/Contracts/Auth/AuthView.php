<?php

declare(strict_types=1);

namespace App\Contracts\Auth;

interface AuthView
{
  /**
   * @param array<mixed>|object|string $data
   */
  public function respondWith($data): void;

  /**
   * @param array{id: int|string, name: string, login: string, userType: string} $userData
   */
  public function updateSession($userData): void;

  public function destroySession(): void;
}
