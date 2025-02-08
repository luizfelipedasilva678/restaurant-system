<?php

namespace App\Utils\Tests;

/**
 * @return bool
 */
function isOutOfWorkingHours(): bool
{
  $days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
  $day = date('w');
  $now = new \DateTime();

  if ($days[$day] === 'Monday' || $days[$day] === 'Tuesday' || $days[$day] === 'Wednesday') {
    return $now < new \DateTime('11:00:00') || $now > new \DateTime('15:00:00');
  }

  return $now < new \DateTime('11:00:00') || $now > new \DateTime('22:00:00');
}
