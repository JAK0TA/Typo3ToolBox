<?php

// Copyright JAKOTA Design Group GmbH. All rights reserved.
declare(strict_types=1);

namespace JAKOTA\Typo3ToolBox\ViewHelpers;

use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

class RelativeDateViewHelper extends AbstractViewHelper {
  /**
   * Initialize arguments.
   */
  public function initializeArguments(): void {
    parent::initializeArguments();
    $this->registerArgument('value', 'DateTime', 'datetime to format');
  }

  /**
   * @return null|string
   */
  public function render() {
    /** @var \DateTime $date */
    $date = $this->arguments['value'];
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
