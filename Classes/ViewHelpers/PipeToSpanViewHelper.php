<?php

// Copyright JAKOTA Design Group GmbH. All rights reserved.
declare(strict_types=1);

namespace JAKOTA\Typo3ToolBox\ViewHelpers;

use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

class PipeToSpanViewHelper extends AbstractViewHelper {
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
    $this->registerArgument('direction', 'string', 'Direction', false, 'end');
    $this->registerArgument('class', 'string', 'Class name', false, 'font-italic');
  }

  public function render(): string {
    $text = strval($this->arguments['text']);
    $direction = strval($this->arguments['direction']);
    $class = strval($this->arguments['class']);

    $pos = strpos($text, '|');
    if (false !== $pos) {
      if ('end' == $direction) {
        $text = substr($text, 0, $pos).'<span class="'.$class.'">'.substr($text, $pos + 1).'</span>';
      } else {
        $text = '<span class="'.$class.'">'.substr($text, 0, $pos).'</span>'.substr($text, $pos + 1);
      }
    }

    return $text;
  }
}
