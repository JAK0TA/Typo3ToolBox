<?php

// Copyright JAKOTA Design Group GmbH. All rights reserved.
declare(strict_types=1);

namespace JAKOTA\Typo3ToolBox\ViewHelpers;

use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

class SanitizeNumberViewHelper extends AbstractViewHelper {
  public function initializeArguments(): void {
    $this->registerArgument('number', 'string', 'phone number');
  }

  public function render(): string {
    $regex = '/(?<!^)\+|[^\d+]+/';

    return preg_replace($regex, '', strval($this->arguments['number'])) ?? '';
  }
}
