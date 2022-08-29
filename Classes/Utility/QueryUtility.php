<?php

// Copyright JAKOTA Design Group GmbH. All rights reserved.
declare(strict_types=1);

namespace JAKOTA\Typo3ToolBox\Utility;

use TYPO3\CMS\Core\Information\Typo3Version;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3\CMS\Extbase\Persistence\Generic\Storage\Typo3DbQueryParser;
use TYPO3\CMS\Extbase\Persistence\QueryInterface;

class QueryUtility {
  /**
   * @template T of object
   *
   * @param class-string<T>|string $className name of the class for the data mapper
   * @param QueryInterface         $query
   * @param string                 $fieldName The fieldName to order by. Will be quoted according to database platform automatically.
   * @param string                 $order     The ordering direction. No automatic quoting/escaping.
   *
   * @return array|QueryResultInterface<T>
   */
  public static function queryOrderBy($className, $query, $fieldName, $order = null) {
    $typo3Version = GeneralUtility::makeInstance(Typo3Version::class);

    if (version_compare($typo3Version->getVersion(), '10.4.0') >= 0) {
      $queryParser = GeneralUtility::makeInstance(Typo3DbQueryParser::class);
      $dataMapper = GeneralUtility::makeInstance(DataMapper::class);
    } else {
      $objectManager = GeneralUtility::makeInstance(ObjectManager::class);
      $queryParser = $objectManager->get(Typo3DbQueryParser::class);
      $dataMapper = $objectManager->get(DataMapper::class);
    }

    // Translate to QueryBuilder
    $queryBuilder = $queryParser->convertQueryToDoctrineQueryBuilder($query);

    // Sneak in the correct order by
    $queryBuilder = $queryBuilder->orderBy($fieldName, $order);

    if (version_compare($typo3Version->getVersion(), '11.5.0') >= 0) {
      $results = $queryBuilder->executeQuery()->fetchAllAssociative();
    } else {
      $results = $queryBuilder->execute()->fetchAll();
    }

    // Map the result to News model
    return $dataMapper->map($className, $results);
  }
}
