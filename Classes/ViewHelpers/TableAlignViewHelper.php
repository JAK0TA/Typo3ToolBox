<?php

// Copyright JAKOTA Design Group GmbH. All rights reserved.
declare(strict_types=1);

namespace JAKOTA\Typo3ToolBox\ViewHelpers;

use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

class TableAlignViewHelper extends AbstractViewHelper {
  /**
   * Initialize arguments.
   *
   * @throws \TYPO3Fluid\Fluid\Core\ViewHelper\Exception
   */
  public function initializeArguments(): void {
    parent::initializeArguments();
    $this->registerArgument('value', 'string', 'string to format');
  }

  /**
   * Applies nl2br() on the specified value.
   *
   * @param array<string, string> $arguments
   */
  public static function renderStatic(array $arguments, \Closure $renderChildrenClosure, RenderingContextInterface $renderingContext): string {
    $value = $renderChildrenClosure();
    $re = '/^\[([A-Z])\]/m';

    preg_match_all($re, $value, $matches, PREG_SET_ORDER, 0);
    $value = preg_replace('/^\[([A-Z])\]/', '', $value);
    if (1 == count($matches)) {
      switch ($matches[0][1]) {
        case 'L':
          $value = '<div class="text-left">'.$value.'</div>';

          break;

        case 'R':
          $value = '<div class="text-right">'.$value.'</div>';

          break;

        case 'C':
          $value = '<div class="text-center">'.$value.'</div>';

          break;
      }
    }

    return $value;
  }
}
