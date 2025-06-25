<?php

// Copyright JAKOTA Design Group GmbH. All rights reserved.
declare(strict_types=1);

namespace JAKOTA\Typo3ToolBox\ViewHelpers;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

class BaseUrlViewHelper extends AbstractViewHelper {
  public function initializeArguments(): void {
    parent::initializeArguments();
  }

  public function render(): string {
    return GeneralUtility::getIndpEnv('TYPO3_SITE_URL');
  }
}
