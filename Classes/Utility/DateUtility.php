<?php

// Copyright JAKOTA Design Group GmbH. All rights reserved.
declare(strict_types=1);

namespace JAKOTA\Typo3ToolBox\Utility;

use TYPO3\CMS\Core\Localization\Locale;

class DateUtility {
  /**
   * @return string
   */
  public static function formatDate(int|string $date, string $pattern = 'MM/DD/YYYY', ?Locale $locale = null) {
    $formatter = new \IntlDateFormatter(
      ($locale ?? new Locale())->__toString(),
      \IntlDateFormatter::NONE,
      \IntlDateFormatter::NONE,
    );

    $formatter->setPattern($pattern);

    return $formatter->format($date) ?: '';
  }

  /**
   * @return null|string
   */
  public static function calculateRelativeDate(int $timestamp) {
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
      
      // Add appropriate pluralization for German
      if ($diff > 1) {
        if (substr($strTime[$i], -1) === 'e') {
          $out .= 'n';
        } else {
          $out .= 'en';
        }
      }

      return $out;
    }

    return null;
  }
}
