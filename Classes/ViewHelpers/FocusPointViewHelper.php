<?php

// Copyright JAKOTA Design Group GmbH. All rights reserved.
declare(strict_types=1);

namespace JAKOTA\Typo3ToolBox\ViewHelpers;

use JAKOTA\Typo3ToolBox\Utility\FocusPointUtility;
use TYPO3\CMS\Core\Imaging\ImageManipulation\CropVariantCollection;
use TYPO3\CMS\Core\Information\Typo3Version;
use TYPO3\CMS\Core\Utility\GeneralUtility;
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

    $typo3Version = GeneralUtility::makeInstance(Typo3Version::class);
    if (version_compare($typo3Version->getVersion(), '11.5.0') >= 0) {
      $cropVariantCollection = CropVariantCollection::create(strval($crop));
      $focusArea = $cropVariantCollection->getFocusArea(strval($cropVariant));
      $xCrop = $focusArea->getOffsetLeft();
      $yCrop = $focusArea->getOffsetTop();
      $height = $focusArea->getHeight();
      $width = $focusArea->getWidth();
    } else {
      $cropJson = (object) json_decode($crop);
      $xCrop = floatval($cropJson->{'$cropVariant'}->focusArea->x);
      $yCrop = floatval($cropJson->{'$cropVariant'}->focusArea->y);
      $height = floatval($cropJson->{'$cropVariant'}->focusArea->height);
      $width = floatval($cropJson->{'$cropVariant'}->focusArea->width);
    }

    return FocusPointUtility::getFocusPoint($type, $xCrop, $yCrop, $height, $width);
  }
}
