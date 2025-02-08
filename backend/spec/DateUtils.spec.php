<?php

namespace Tests;

use App\Utils\DateUtils;

use function Kahlan\describe;
use function Kahlan\expect;
use function Kahlan\it;

describe('DateUtils', function () {
  it('should return that the day of week is monday', function () {
    $date = new \DateTime();
    $date->modify('next monday');

    expect(DateUtils::getDayOfWeek($date))->toBe('Monday');
  });

  it('should return that the day of week is tuesday', function () {
    $date = new \DateTime();
    $date->modify('next tuesday');

    expect(DateUtils::getDayOfWeek($date))->toBe('Tuesday');
  });

  it('should return that the day of week is wednesday', function () {
    $date = new \DateTime();
    $date->modify('next wednesday');

    expect(DateUtils::getDayOfWeek($date))->toBe('Wednesday');
  });

  it('should return that the day of week is thursday', function () {
    $date = new \DateTime();
    $date->modify('next thursday');

    expect(DateUtils::getDayOfWeek($date))->toBe('Thursday');
  });

  it('should return that the day of week is friday', function () {
    $date = new \DateTime();
    $date->modify('next friday');

    expect(DateUtils::getDayOfWeek($date))->toBe('Friday');
  });

  it('should return that the day of week is saturday', function () {
    $date = new \DateTime();
    $date->modify('next saturday');

    expect(DateUtils::getDayOfWeek($date))->toBe('Saturday');
  });

  it('should return that the day of week is sunday', function () {
    $date = new \DateTime();
    $date->modify('next sunday');

    expect(DateUtils::getDayOfWeek($date))->toBe('Sunday');
  });

  it('should translate monday correctly', function () {
    expect(DateUtils::getTranslatedDayOfWeek('Monday'))->toBe('Segunda-feira');
  });

  it('should translate tuesday correctly', function () {
    expect(DateUtils::getTranslatedDayOfWeek('Tuesday'))->toBe('Terça-feira');
  });

  it('should translate wednesday correctly', function () {
    expect(DateUtils::getTranslatedDayOfWeek('Wednesday'))->toBe('Quarta-feira');
  });

  it('should translate thursday correctly', function () {
    expect(DateUtils::getTranslatedDayOfWeek('Thursday'))->toBe('Quinta-feira');
  });

  it('should translate friday correctly', function () {
    expect(DateUtils::getTranslatedDayOfWeek('Friday'))->toBe('Sexta-feira');
  });

  it('should translate saturday correctly', function () {
    expect(DateUtils::getTranslatedDayOfWeek('Saturday'))->toBe('Sábado');
  });

  it('should translate sunday correctly', function () {
    expect(DateUtils::getTranslatedDayOfWeek('Sunday'))->toBe('Domingo');
  });

  it('should create date time from string correctly', function () {
    $date = new \DateTime();

    $dateTime = DateUtils::getDateTimeFromString($date->format('Y-m-d'), '11:00:00');
    expect($dateTime)->toBeAnInstanceOf('\DateTime');
    expect($dateTime->format('H:i:s'))->toBe('11:00:00');
    expect($dateTime->format('Y-m-d'))->toBe($date->format('Y-m-d'));
  });
});
