<?php

// Copyright JAKOTA Design Group GmbH. All rights reserved.
declare(strict_types=1);

namespace JAKOTA\Typo3ToolBox\Utility;

class TextUtility {
  public static function calculateReadingTime(string $text): \stdClass {
    $secondsPerWordBad = 0.55;
    $secondsPerWordAverage = 0.25;
    $secondsPerWordGood = 0.15;

    $wordCount = str_word_count(strip_tags($text));
    $stats = new \stdClass();
    $stats->words = $wordCount;
    $stats->secondsBad = $wordCount * $secondsPerWordBad;
    $stats->secondsAverage = $wordCount * $secondsPerWordAverage;
    $stats->secondsGood = $wordCount * $secondsPerWordGood;

    if ($stats->secondsBad < 1) {
      $stats->formatBad = '0 sek';
    } else {
      $min = $stats->secondsBad / 60;
      if ($min < 1) {
        $stats->formatBad = round($stats->secondsBad).' sek';
      } else {
        $stats->formatBad = round($min).' min';
      }
    }

    if ($stats->secondsAverage < 1) {
      $stats->formatAverage = '0 sek';
    } else {
      $min = $stats->secondsAverage / 60;
      if ($min < 1) {
        $stats->formatAverage = round($stats->secondsAverage).' sek';
      } else {
        $stats->formatAverage = round($min).' min';
      }
    }

    if ($stats->secondsGood < 1) {
      $stats->formatGood = '0 sek';
    } else {
      $min = $stats->secondsGood / 60;
      if ($min < 1) {
        $stats->formatGood = round($stats->secondsGood).' sek';
      } else {
        $stats->formatGood = round($min).' min';
      }
    }

    return $stats;
  }

  /**
   * Trims a multiline string to a specified number of lines.
   *
   * This function takes a string and an optional line limit. It then trims the
   * string so that it contains no more than the specified number of lines. If the
   * string has fewer lines than the limit, it remains unchanged. The function ensures
   * that the output always ends with a newline character.
   *
   * @param string $string the multiline string to be trimmed
   * @param int    $lines  Optional. The maximum number of lines to keep in the string.
   *                       Defaults to 100.
   *
   * @return string the trimmed string, containing no more than the specified number of lines
   */
  public static function keepLines(string $string, int $lines = 100): string {
    $string = explode("\n", $string);
    array_splice($string, $lines);

    return implode("\n", $string)."\n";
  }
}
