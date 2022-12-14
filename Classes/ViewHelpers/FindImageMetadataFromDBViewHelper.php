<?php

// Copyright JAKOTA Design Group GmbH. All rights reserved.
declare(strict_types=1);

namespace JAKOTA\Typo3ToolBox\ViewHelpers;

use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Information\Typo3Version;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

/*
 * Use in Fluid:
 * {t3tb:findImageMetadataFromDB(uid:image.originalResource.properties.file,language:image.originalResource.properties.sys_language_uid)}
 */
class FindImageMetadataFromDBViewHelper extends AbstractViewHelper {
  /**
   * Initialize arguments.
   */
  public function initializeArguments(): void {
    $this->registerArgument('uid', 'string', 'uid');
    $this->registerArgument('language', 'string', 'Typ');
  }

  /**
   * @return array<string,mixed>|false
   */
  public function render() {
    $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable('sys_file_metadata');
    $queryBuilder = $queryBuilder
      ->select('*')
      ->from('sys_file_metadata')
      ->where($queryBuilder->expr()->eq('sys_file_metadata.file', $queryBuilder->createNamedParameter($this->arguments['uid'], \PDO::PARAM_STR)))
      ->andWhere($queryBuilder->expr()->eq('sys_language_uid', $queryBuilder->createNamedParameter($this->arguments['language'], \PDO::PARAM_STR)))
    ;

    $typo3Version = GeneralUtility::makeInstance(Typo3Version::class);
    if (version_compare($typo3Version->getVersion(), '11.5.0') >= 0) {
      return $queryBuilder->executeQuery()->fetchAssociative();
    }

    return $queryBuilder->execute()->fetchAll(\PDO::FETCH_ASSOC);
  }
}
