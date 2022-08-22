<?php

// Copyright JAKOTA Design Group GmbH. All rights reserved.
declare(strict_types=1);

namespace JAKOTA\Typo3ToolBox\ViewHelpers;

use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

class ConvertCurrencyCodeViewHelper extends AbstractViewHelper {
  public function initializeArguments(): void {
    $this->registerArgument('currencyCode', 'string', 'Currency Code', true);
  }

  public function render(): string {
    /** @var string $currencyCode */
    $currencyCode = $this->arguments['currencyCode'] ?? '';

    switch ($currencyCode) {
      case 'GBP':
        return '£';

      case 'CAD':
        return 'C$';

      case 'AUS':
        return '$A';

      case 'USD':
        return '$';

      default:
        return '€';
    }
  }
}
