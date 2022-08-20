<?php

// Copyright JAKOTA Design Group GmbH. All rights reserved.
declare(strict_types=1);

namespace JAKOTA\Typo3ToolBox\Formatter;

use NumberFormatter as GlobalNumberFormatter;

class NumberFormatter {
  /**
   * @return null|string
   */
  public static function getAsString(int $number) {
    $formatter = new GlobalNumberFormatter('en', GlobalNumberFormatter::SPELLOUT);

    /** @var false|string $numberAsString */
    $numberAsString = $formatter->format($number);

    return is_bool($numberAsString) ? null : ucfirst($numberAsString);
  }
}
