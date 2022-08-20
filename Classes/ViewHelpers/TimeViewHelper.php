<?php

// Copyright JAKOTA Design Group GmbH. All rights reserved.
declare(strict_types=1);

namespace JAKOTA\Typo3ToolBox\ViewHelpers;

use Exception;
use TYPO3\CMS\Core\Context\Context;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

class TimeViewHelper extends AbstractViewHelper {
  /**
   * Initialize arguments.
   */
  public function initializeArguments(): void {
    $this->registerArgument('date', '\DateTime', 'Either an object implementing DateTimeInterface');
    $this->registerArgument('format', 'string', 'Format String which is taken to format the Date/Time', false, '');
  }

  /**
   * @param array<string, \DateTime|string> $arguments
   *
   * @throws Exception
   */
  public static function renderStatic(array $arguments, \Closure $renderChildrenClosure, RenderingContextInterface $renderingContext): string {
    $format = $arguments['format'];
    if ('' === $format) {
      $format = $GLOBALS['TYPO3_CONF_VARS']['SYS']['ddmmyy'] ?: 'Y-m-d';
    }

    /** @var \Datetime */
    $date = $renderChildrenClosure();

    $languageAspect = GeneralUtility::makeInstance(Context::class)->getAspect('language');
    $languageUid = $languageAspect->getId();
    if (0 != $languageUid) {
      $format = str_replace('H', 'g', $format);
      $format .= str_ends_with($format, 'a') ? '' : 'a';
    }

    return $date->format($format);
  }
}
