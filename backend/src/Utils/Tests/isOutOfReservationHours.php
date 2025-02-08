<?php

namespace App\Utils\Tests;

/**
 * @return bool
 */
function isOutOfReservationHours(): bool
{
  $days =  ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
  $day = date('w');
  $now = new \DateTime();

  if ($days[$day] === 'Thursday' || $days[$day] === 'Friday' || $days[$day] === 'Saturday' || $days[$day] === 'Sunday') {
    return $now < new \DateTime('11:00:00') || $now > new \DateTime('20:00:00');
  }

  return true;
}
