<?php

// Copyright JAKOTA Design Group GmbH. All rights reserved.
declare(strict_types=1);

namespace JAKOTA\Typo3ToolBox\Utility;

use Doctrine\DBAL\Driver\Exception as DriverException;
use Doctrine\DBAL\Exception;
use TYPO3\CMS\Core\Cache\Exception\NoSuchCacheException;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Service\FlexFormService;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\MathUtility;

class FlexFormUtility {
  /**
   * @return null|array<string, array<string, mixed>>
   *
   * @throws \InvalidArgumentException
   * @throws \UnexpectedValueException
   * @throws Exception
   * @throws DriverException
   * @throws NoSuchCacheException
   */
  public static function getFlexFormSettingsFromContentElement(int $contentElementUid): ?array {
    $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable('tt_content');
    $queryBuilder = $queryBuilder
      ->select('pi_flexform')
      ->from('tt_content')
      ->where(
        $queryBuilder->expr()->eq('uid', $contentElementUid)
      )
    ;

    $results = $queryBuilder->executeQuery()->fetchAssociative();

    if (!$results) {
      return null;
    }

    return GeneralUtility::makeInstance(FlexFormService::class)
      ->convertFlexFormContentToArray(strval($results['pi_flexform']))
    ;
  }

  /**
   * @param array<string, mixed> $flexForm
   */
  public static function getFlexFormValue(array $flexForm, string $fieldName, string $sheet = 'sDEF', string $lang = 'lDEF', string $value = 'vDEF'): ?string {
    $sheetArray = '';

    if (
      isset($flexForm['data'])
      && is_array($flexForm['data']) && isset($flexForm['data'][$sheet])
      && is_array($flexForm['data'][$sheet]) && isset($flexForm['data'][$sheet][$lang])
    ) {
      $sheetArray = $flexForm['data'][$sheet][$lang];
    }

    if (is_array($sheetArray)) {
      // TODO: Check if GeneralUtility::trimExplode('/', $fieldName) is possible
      return self::getFlexFormValueFromSheetArray($sheetArray, GeneralUtility::trimExplode('/', $fieldName), $value);
    }

    return null;
  }

  /**
   * @param array<mixed, mixed>      $sheetArray
   * @param array<int|string, mixed> $fieldNameArr
   */
  public static function getFlexFormValueFromSheetArray(array $sheetArray, array $fieldNameArr, string $value): ?string {
    $tempArr = $sheetArray;
    foreach ($fieldNameArr as $v) {
      if (MathUtility::canBeInterpretedAsInteger($v)) {
        $c = 0;
        foreach ($tempArr as $values) {
          if ($c == $v) {
            $tempArr = (array) $values;

            break;
          }
          ++$c;
        }
      } else {
        $tempArr = (array) ($tempArr[strval($v)] ?? []);
      }
    }
    if (!isset($tempArr[$value])) {
      return null;
    }

    return strval($tempArr[$value]);
  }

  /**
   * Checks if a specific option is enabled.
   *
   * @param int<0, 255> $bitmask
   */
  public static function isOptionEnabled(int $bitmask, int $index): bool {
    return ($bitmask & (1 << $index)) !== 0;
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
