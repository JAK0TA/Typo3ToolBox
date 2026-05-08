<?php

// Copyright JAKOTA Design Group GmbH. All rights reserved.
declare(strict_types=1);

namespace JAKOTA\Typo3ToolBox\Utility;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Persistence\Generic\Mapper\DataMapper;
use TYPO3\CMS\Extbase\Persistence\Generic\Storage\Typo3DbQueryParser;
use TYPO3\CMS\Extbase\Persistence\QueryInterface;

class QueryUtility {
  /**
   * @template T of object
   *
   * @param QueryInterface<T> $query
   * @param string            $fieldName The fieldName to order by. Will be quoted according to database platform automatically.
   * @param string            $order     The ordering direction. No automatic quoting/escaping.
   *
   * @return T[]
   */
  public static function queryOrderBy($query, $fieldName, $order = null) {
    $queryParser = GeneralUtility::makeInstance(Typo3DbQueryParser::class);
    $dataMapper = GeneralUtility::makeInstance(DataMapper::class);

    // Translate to QueryBuilder
    $queryBuilder = $queryParser->convertQueryToDoctrineQueryBuilder($query);

    // Sneak in the correct order by
    $queryBuilder = $queryBuilder->orderBy($fieldName, $order);

    $results = $queryBuilder->executeQuery()->fetchAllAssociative();

    // Map the result to class model
    return $dataMapper->map($query->getType(), $results);
  }
}
