<?php

// Copyright JAKOTA Design Group GmbH. All rights reserved.
declare(strict_types=1);

namespace JAKOTA\Typo3ToolBox\Utility;

class FocusPointUtility {
  public static function getFocusPoint(string $type, float $xCrop, float $yCrop, float $height, float $width): float {
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
