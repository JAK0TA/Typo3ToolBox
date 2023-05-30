<?php

// Copyright JAKOTA Design Group GmbH. All rights reserved.
declare(strict_types=1);

namespace JAKOTA\Typo3ToolBox\Utility;

use TYPO3\CMS\Core\Imaging\ImageManipulation\CropVariantCollection;
use TYPO3\CMS\Core\Information\Typo3Version;
use TYPO3\CMS\Core\Resource\FileInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class FocusPointUtility {
  public static function calculateFocusPoint(string $type, float $xCrop, float $yCrop, float $height, float $width): float {
    switch ($type) {
      case 'left':
        return round(($xCrop + $width / 2) * 100, -1);

      case 'top':
        return round(($yCrop + $height / 2) * 100, -1);

      default:
        return 0.0;
    }
  }

  public static function getFocusPoint(string $croppingConfiguration, string $type, string $cropVariant = 'default'): float {
    $typo3Version = GeneralUtility::makeInstance(Typo3Version::class);

    if (version_compare($typo3Version->getVersion(), '11.5.0') >= 0) {
      $cropVariantCollection = CropVariantCollection::create($croppingConfiguration);
      $focusArea = $cropVariantCollection->getFocusArea($cropVariant);
      $xCrop = $focusArea->getOffsetLeft();
      $yCrop = $focusArea->getOffsetTop();
      $height = $focusArea->getHeight();
      $width = $focusArea->getWidth();
    } else {
      $cropJson = (object) json_decode($croppingConfiguration);
      $xCrop = floatval($cropJson->{$cropVariant}->focusArea->x);
      $yCrop = floatval($cropJson->{$cropVariant}->focusArea->y);
      $height = floatval($cropJson->{$cropVariant}->focusArea->height);
      $width = floatval($cropJson->{$cropVariant}->focusArea->width);
    }

    return self::calculateFocusPoint($type, $xCrop, $yCrop, $height, $width);
  }

  public static function getFocusPointFromFile(FileInterface $fileObject, string $type, string $cropVariant = 'default'): float {
    if (!$fileObject->hasProperty('crop') || empty($fileObject->getProperty('crop'))) {
      return 0.0;
    }

    return self::getFocusPoint($fileObject->getProperty('crop'), $type, $cropVariant);
  }
}
