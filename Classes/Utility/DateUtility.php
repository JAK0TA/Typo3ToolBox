<?php

// Copyright JAKOTA Design Group GmbH. All rights reserved.
declare(strict_types=1);

namespace JAKOTA\Typo3ToolBox\Utility;

class DateUtility {
  /**
   * @return null|string
   */
  public static function calculateRelativeDate(\DateTime $date) {
    $timestamp = $date->getTimestamp();
    $currentTime = time();

    $strTime = ['Sekunde', 'Minute', 'Stunde', 'Tag', 'Monat', 'Jahr'];
    $length = [60, 60, 24, 30, 12, 10];

    if ($currentTime >= $timestamp) {
      $diff = time() - $timestamp;
      for ($i = 0; $diff >= $length[$i] && $i < count($length) - 1; ++$i) {
        $diff = $diff / $length[$i];
      }
      $diff = round($diff);
      $out = 'vor '.$diff.' '.$strTime[$i];
      if ($diff > 1) {
        if ('e' == $strTime[$i][-1]) {
          $out = $out.'n';
        } else {
          $out = $out.'en';
        }
      }

      return $out;
    }

    return null;
  }
}
