<?php

// Copyright JAKOTA Design Group GmbH. All rights reserved.
declare(strict_types=1);

namespace JAKOTA\Typo3ToolBox\Utility;

use Doctrine\DBAL\DBALException;
use Doctrine\DBAL\Driver\Exception as DriverException;
use Doctrine\DBAL\Exception;
use TYPO3\CMS\Core\Cache\Exception\NoSuchCacheException;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Information\Typo3Version;
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
   * @throws DBALException
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

    $typo3Version = GeneralUtility::makeInstance(Typo3Version::class);
    if (version_compare($typo3Version->getVersion(), '11.5.0') >= 0) {
      $results = $queryBuilder->executeQuery()->fetchAssociative();
    } else {
      $results = $queryBuilder->execute()->fetch();
    }

    if (!$results) {
      return null;
    }

    return GeneralUtility::makeInstance(FlexFormService::class)
      ->convertFlexFormContentToArray($results['pi_flexform'])
    ;
  }

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
      return self::getFlexFormValueFromSheetArray($sheetArray, GeneralUtility::trimExplode('/', $fieldName), $value);
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
        if (is_array($tempArr)) {
          $tempArr = (array) ($tempArr[strval($v)] ?? []);
        } else {
          $tempArr = [];
        }
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
