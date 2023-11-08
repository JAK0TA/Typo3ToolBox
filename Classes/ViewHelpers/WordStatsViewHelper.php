<?php

// Copyright JAKOTA Design Group GmbH. All rights reserved.
declare(strict_types=1);

namespace JAKOTA\Typo3ToolBox\ViewHelpers;

use JAKOTA\Typo3ToolBox\Utility\TextUtility;
use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;
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
   * @param array<string, bool|string> $arguments
   *
   * @return array|string
   */
  public static function renderStatic(array $arguments, \Closure $renderChildrenClosure, RenderingContextInterface $renderingContext) {
    $templateVariableContainer = $renderingContext->getVariableProvider();
    $as = $arguments['as'];
    $returnStatsAsArray = $arguments['returnStatsAsArray'] ?? false;
    $templateVariableContainer->add($as, '');
    $output = $renderChildrenClosure();
    $templateVariableContainer->remove($as);

    $stats = TextUtility::calculateReadingTime($output);

    if ($returnStatsAsArray) {
      return (array) $stats;
    }

    $templateVariableContainer->add($as, $stats);
    $output = $renderChildrenClosure();
    $templateVariableContainer->remove($as);

    return $output;
  }
}
