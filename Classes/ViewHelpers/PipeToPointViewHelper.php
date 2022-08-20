<?php

// Copyright JAKOTA Design Group GmbH. All rights reserved.
declare(strict_types=1);

namespace JAKOTA\Typo3ToolBox\ViewHelpers;

use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

class PipeToPointViewHelper extends AbstractViewHelper {
  /**
   * As this ViewHelper renders HTML, the output must not be escaped.
   *
   * @var bool
   */
  protected $escapeOutput = false;

  /**
   * Initialize arguments.
   */
  public function initializeArguments(): void {
    parent::initializeArguments();
    $this->registerArgument('text', 'string', 'Text', true);
  }

  public function render(): string {
    $text = $this->arguments['text'];

    return str_replace('|', '•', $text);
  }
}
