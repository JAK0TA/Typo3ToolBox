<?php

// Copyright JAKOTA Design Group GmbH. All rights reserved.
declare(strict_types=1);

namespace JAKOTA\Typo3ToolBox\ViewHelpers;

use JAKOTA\Typo3ToolBox\Utility\FocusPointUtility;
use TYPO3\CMS\Core\Imaging\ImageManipulation\CropVariantCollection;
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
    $crop = $this->arguments['crop'];
    $cropVariant = $this->arguments['cropVariant'];
    $type = $this->arguments['type'];
    if (null == $crop) {
      return 0;
    }

    $cropVariantCollection = CropVariantCollection::create(strval($crop));
    $cropArea = $cropVariantCollection->getFocusArea(strval($cropVariant));

    return FocusPointUtility::getFocusPoint($type, $cropArea);
  }
}
