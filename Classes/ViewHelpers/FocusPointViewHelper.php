<?php

// Copyright JAKOTA Design Group GmbH. All rights reserved.
declare(strict_types=1);

namespace JAKOTA\Typo3ToolBox\ViewHelpers;

use JAKOTA\Typo3ToolBox\Utility\FocusPointUtility;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

class FocusPointViewHelper extends AbstractViewHelper {
  /**
   * Initialize arguments.
   */
  public function initializeArguments(): void {
    $this->registerArgument('crop', 'string', 'Focuspoint');
    $this->registerArgument('cropVariant', 'string', 'Crop variant', false, 'default');
    $this->registerArgument('type', 'string', 'Typ');
  }

  public function render(): float {
    $crop = strval($this->arguments['crop'] ?? '');
    $cropVariant = strval($this->arguments['cropVariant']);
    $type = strval($this->arguments['type'] ?? '');
    if (empty($crop)) {
      return 0;
    }

    return FocusPointUtility::getFocusPoint(strval($crop), $type, strval($cropVariant));
  }
}
