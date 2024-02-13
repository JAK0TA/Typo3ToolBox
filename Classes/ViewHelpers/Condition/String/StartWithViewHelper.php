<?php

// Copyright JAKOTA Design Group GmbH. All rights reserved.
declare(strict_types=1);

namespace JAKOTA\Typo3ToolBox\ViewHelpers\Condition\String;

use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractConditionViewHelper;

class StartWithViewHelper extends AbstractConditionViewHelper {
  /**
   * Initialize arguments.
   */
  public function initializeArguments(): void {
    parent::initializeArguments();

    $this->registerArgument('haystack', 'string', 'The string to search in.', true);
    $this->registerArgument('needle', 'string', 'The substring to search for in the haystack.', true);
  }

  /**
   * @param array<string, mixed> $arguments
   */
  protected static function evaluateCondition($arguments = null) {
    $haystack = strval($arguments['haystack'] ?? '');
    $needle = strval($arguments['needle'] ?? '');

    return substr($haystack, 0, strlen($needle)) === $needle;
  }
}
