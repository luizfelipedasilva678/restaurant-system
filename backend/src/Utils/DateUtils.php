<?php

declare(strict_types=1);

namespace App\Utils;

class DateUtils
{
  /**
   * @return "Monday" | "Tuesday" | "Wednesday" | "Thursday" | "Friday" | "Saturday" | "Sunday"
   */
  public static function getDayOfWeek(\DateTime $date = new \DateTime()): string
  {
    $days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
    $day = $date->format('w');

    return $days[$day];
  }

  public static function getTranslatedDayOfWeek(string $day): string
  {
    $mappedDays = [
      'Sunday' => 'Domingo',
      'Monday' => 'Segunda-feira',
      'Tuesday' => 'Terça-feira',
      'Wednesday' => 'Quarta-feira',
      'Thursday' => 'Quinta-feira',
      'Friday' => 'Sexta-feira',
      'Saturday' => 'Sábado',
    ];

    return $mappedDays[$day];
  }

  public static function getDateTimeFromString(string $date, string $time): \DateTime
  {
    return new \DateTime($date . ' ' . $time);
  }
}
