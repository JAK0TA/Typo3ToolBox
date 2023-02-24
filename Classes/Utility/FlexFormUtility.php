<?php

// Copyright JAKOTA Design Group GmbH. All rights reserved.
declare(strict_types=1);

namespace JAKOTA\Typo3ToolBox\Utility;

use TYPO3\CMS\Core\Utility\MathUtility;

class FlexFormUtility {
  /**
   * @param array<string, mixed> $flexForm
   */
  public static function getFlexFormValue(array $flexForm, string $fieldName, string $sheet = 'sDEF', string $lang = 'lDEF', string $value = 'vDEF'): ?string {
    if (
      is_array($flexForm) && isset($flexForm['data'])
      && is_array($flexForm['data']) && isset($flexForm['data'][$sheet])
      && is_array($flexForm['data'][$sheet]) && isset($flexForm['data'][$sheet][$lang])
    ) {
      $sheetArray = $flexForm['data'][$sheet][$lang];
    } else {
      $sheetArray = '';
    }

    if (is_array($sheetArray)) {
      return self::getFlexFormValueFromSheetArray($sheetArray, \TYPO3\CMS\Core\Utility\GeneralUtility::trimExplode('/', $fieldName), $value);
    }

    return null;
  }

  /**
   * @param array<string, mixed>     $sheetArray
   * @param array<int|string, mixed> $fieldNameArr
   */
  public static function getFlexFormValueFromSheetArray(array $sheetArray, array $fieldNameArr, string $value): ?string {
    $tempArr = $sheetArray;
    foreach ($fieldNameArr as $v) {
      if (MathUtility::canBeInterpretedAsInteger($v)) {
        if (is_array($tempArr)) {
          $c = 0;
          foreach ($tempArr as $values) {
            if ($c == $v) {
              $tempArr = $values;

              break;
            }
            ++$c;
          }
        }
      } else {
        $tempArr = (array) ($tempArr[strval($v)] ?? []);
      }
    }

    return $tempArr[$value] ?? null;
  }

  /**
   * @param array<string, mixed> $flexForm
   *
   * @return array<string, mixed>
   */
  public static function setFlexFormValue(array $flexForm, string $key, string $value, string $sheet = 'sDEF'): ?array {
    if (
      !isset($flexForm['data']) || !is_array($flexForm['data'])
      || !isset($flexForm['data'][$sheet]) || !is_array($flexForm['data'][$sheet])
      || !isset($flexForm['data'][$sheet]['lDEF']) || !is_array($flexForm['data'][$sheet]['lDEF'])
      || !isset($flexForm['data'][$sheet]['lDEF'][$key]) || !is_array($flexForm['data'][$sheet]['lDEF'][$key])
      || !isset($flexForm['data'][$sheet]['lDEF'][$key]['vDEF'])
    ) {
      return null;
    }

    $flexForm['data'][$sheet]['lDEF'][$key]['vDEF'] = $value;

    return $flexForm;
  }
}
