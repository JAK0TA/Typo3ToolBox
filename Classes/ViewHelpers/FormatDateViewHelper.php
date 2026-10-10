<?php

// Copyright JAKOTA Design Group GmbH. All rights reserved.
declare(strict_types=1);

namespace JAKOTA\Typo3ToolBox\ViewHelpers;

use JAKOTA\Typo3ToolBox\Utility\DateUtility;
use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Core\Localization\Locale;
use TYPO3\CMS\Fluid\Core\Rendering\RenderingContext;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

class FormatDateViewHelper extends AbstractViewHelper {
  public function initializeArguments(): void {
    $this->registerArgument('pattern', 'string', 'The format pattern', false);
  }

  public function render(): string {
    $pattern = strval($this->arguments['pattern']);

    $date = $this->renderChildren();
    if (null === $date) {
      return '';
    }

    if (!$this->renderingContext instanceof RenderingContext) {
      return '';
    }

    $request = $this->renderingContext->getAttribute(ServerRequestInterface::class);
    $siteLanguage = $request->getAttribute('language');
    if (null !== $siteLanguage) {
      $locale = $siteLanguage->getLocale();
    } else {
      $locale = new Locale();
    }

    if (is_string($date)) {
      $date = trim($date);
    }

    return DateUtility::formatDate(strval($date), $pattern, $locale);
  }
}
