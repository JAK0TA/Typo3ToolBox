<?php

// Copyright JAKOTA Design Group GmbH. All rights reserved.
declare(strict_types=1);

namespace JAKOTA\Typo3ToolBox\ViewHelpers;

use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

class MeasureRendertimeViewHelper extends AbstractViewHelper {
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
  }

  /**
   * @param array<string, mixed> $arguments
   */
  public static function renderStatic(array $arguments, \Closure $renderChildrenClosure, RenderingContextInterface $renderingContext): string {
    $time_start = microtime(true);
    $output = $renderChildrenClosure();
    $time_end = microtime(true);
    $time = $time_end - $time_start;

    return '<div style="border:1px solid red;"><div style="background:red; color:white">'.$time.'s</div>'.$output.'</div>';
  }
}
