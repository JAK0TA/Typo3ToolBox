<?php

// Copyright JAKOTA Design Group GmbH. All rights reserved.
declare(strict_types=1);

namespace JAKOTA\Typo3ToolBox\ViewHelpers;

use JAKOTA\Typo3ToolBox\Utility\TextUtility;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

class WordStatsViewHelper extends AbstractViewHelper {
  protected $escapeOutput = false;

  /**
   * Initialize arguments.
   */
  public function initializeArguments(): void {
    parent::initializeArguments();
    $this->registerArgument('as', 'string', 'name for Stats', false, '');
    $this->registerArgument('returnStatsAsArray', 'bool', 'Return stats array instead of rendered content', false, false);
  }

  /**
   * @return array<string, mixed>|string
   */
  public function render() {
    $templateVariableContainer = $renderingContext->getVariableProvider();
    $as = strval($this->arguments['as']);
    $returnStatsAsArray = $this->arguments['returnStatsAsArray'] ?? false;
    $templateVariableContainer->add($as, '');
    $output = $this->renderChildren();
    $templateVariableContainer->remove($as);

    $stats = TextUtility::calculateReadingTime($output);

    if ($returnStatsAsArray) {
      return (array) $stats;
    }

    $templateVariableContainer->add($as, $stats);
    $output = $this->renderChildren();
    $templateVariableContainer->remove($as);

    return $output;
  }
}
