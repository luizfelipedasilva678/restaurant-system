<?php

namespace Tests;

use App\Utils\Env;

use function Kahlan\beforeEach;
use function Kahlan\describe;
use function Kahlan\it;
use function Kahlan\expect;

describe('Env', function () {
  beforeEach(function () {
    Env::initEnv(__DIR__ . '/config/.env.test');
  });

  it('should set a new value correctly', function () {
    Env::set('NEW_KEY', 'test');

    expect(Env::get('NEW_KEY'))->toBe('test');
  });

  it('should return false when key does not exist', function () {
    expect(Env::has('NON_EXISTING_KEY'))->toBeFalsy();
  });

  it('should return true when key exists', function () {
    expect(Env::has('ENV'))->toBeTruthy();
  });

  it('should return an empty string when key does not exist', function () {
    expect(Env::get('NON_EXISTING_KEY'))->toBe('');
  });

  it('should get ENV value correctly', function () {
    expect(Env::get('ENV'))->toBe('test');
  });

  it('should get DATABASE_USER value correctly', function () {
    expect(Env::get('DATABASE_USER'))->toBe('test');
  });

  it('should get DATABASE_USER value correctly', function () {
    expect(Env::get('DATABASE_PASSWORD'))->toBe('test');
  });

  it('should get DATABASE_HOST value correctly', function () {
    expect(Env::get('DATABASE_HOST'))->toBe('localhost:3306');
  });

  it('should get DATABASE_NAME value correctly', function () {
    expect(Env::get('DATABASE_NAME'))->toBe('test');
  });

  it('should get HOST_URL value correctly', function () {
    expect(Env::get('HOST_URL'))->toBe('http://localhost:8000');
  });
});
