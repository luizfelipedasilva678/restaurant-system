<?php

namespace Tests;

use App\Models\Index\IndexModel;

use function Kahlan\describe;
use function Kahlan\expect;
use function Kahlan\it;

describe('IndexModel', function () {
  it('getData', function () {
    $indexModel = new IndexModel();

    expect(str_contains($indexModel->getData()['employees'], '/api/v1/employees'))->toBe(true);
    expect(str_contains($indexModel->getData()['tables'], '/api/v1/tables'))->toBe(true);
    expect(str_contains($indexModel->getData()['reservations'], '/api/v1/reservations'))->toBe(true);
  });
});
