<?php

// Copyright JAKOTA Design Group GmbH. All rights reserved.
declare(strict_types=1);

namespace JAKOTA\Typo3ToolBox\ViewHelpers;

use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

class SubtractViewHelper extends AbstractViewHelper {
  /**
   * Initialize arguments.
   *
   * @throws \TYPO3Fluid\Fluid\Core\ViewHelper\Exception
   */
  public function initializeArguments(): void {
    $this->registerArgument('minuend', 'int', 'the number from which another number, the subtrahend, is to be subtracted', true);
    $this->registerArgument('subtrahend', 'int', 'the number to be subtracted from another number', true);
  }

  public function render(): int {
    $minuend = intval($this->arguments['minuend']);
    $subtrahend = intval($this->arguments['subtrahend']);

    return $minuend - $subtrahend;
  }
}
