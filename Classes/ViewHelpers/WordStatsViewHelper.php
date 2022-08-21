<?php

// Copyright JAKOTA Design Group GmbH. All rights reserved.
declare(strict_types=1);

namespace JAKOTA\Typo3ToolBox\ViewHelpers;

use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

class WordStatsViewHelper extends AbstractViewHelper {
  protected $escapeOutput = false;

  /**
   * Initialize arguments.
   */
  public function initializeArguments(): void {
    parent::initializeArguments();
    $this->registerArgument('as', 'string', 'name for Stats');
  }

  /**
   * @param array<string, string> $arguments
   */
  public static function renderStatic(array $arguments, \Closure $renderChildrenClosure, RenderingContextInterface $renderingContext): string {
    $templateVariableContainer = $renderingContext->getVariableProvider();
    $as = $arguments['as'];
    $templateVariableContainer->add($as, '');
    $output = $renderChildrenClosure();
    $templateVariableContainer->remove($as);

    $secondsPerWordBad = 0.55;
    $secondsPerWordAverage = 0.25;
    $secondsPerWordGood = 0.15;

    $wordCount = str_word_count(strip_tags($output));
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
      $min = $stats->formatGood / 60;
      if ($min < 1) {
        $stats->formatGood = round($stats->secondsGood).' sek';
      } else {
        $stats->formatGood = round($min).' min';
      }
    }

    $templateVariableContainer->add($as, $stats);
    $output = $renderChildrenClosure();
    $templateVariableContainer->remove($as);

    return $output;
  }
}
