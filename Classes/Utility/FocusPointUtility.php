<?php

// Copyright JAKOTA Design Group GmbH. All rights reserved.
declare(strict_types=1);

namespace JAKOTA\Typo3ToolBox\Utility;

use TYPO3\CMS\Core\Imaging\ImageManipulation\Area;

class FocusPointUtility {
  public static function getFocusPoint(string $type, Area $focusArea): float {
    $xCrop = $focusArea->getOffsetLeft();
    $yCrop = $focusArea->getOffsetTop();
    $height = $focusArea->getHeight();
    $width = $focusArea->getWidth();

    switch ($type) {
      case 'left':
        return round(($xCrop + $width / 2) * 100, -1);

      case 'top':
        return round(($yCrop + $height / 2) * 100, -1);

      default:
        return 0;
    }
  }
}
