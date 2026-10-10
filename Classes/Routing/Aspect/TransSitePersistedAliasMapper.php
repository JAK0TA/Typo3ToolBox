<?php

// Copyright JAKOTA Design Group GmbH. All rights reserved.
declare(strict_types=1);

namespace JAKOTA\Typo3ToolBox\Routing\Aspect;

use TYPO3\CMS\Core\Database\Connection;
use TYPO3\CMS\Core\Routing\Aspect\PersistedAliasMapper;

class TransSitePersistedAliasMapper extends PersistedAliasMapper {
  /**
   * @return null|array<string, mixed>
   */
  protected function findByRouteFieldValue(string $value): ?array {
    $languageAware = null !== $this->languageFieldName && null !== $this->languageParentFieldName;

    $queryBuilder = $this->createQueryBuilder();
    $constraints = [
      $queryBuilder->expr()->eq(
        $this->routeFieldName,
        $queryBuilder->createNamedParameter($value, Connection::PARAM_STR)
      ),
    ];

    $languageIds = null;
    if ($languageAware) {
      $languageIds = $this->resolveAllRelevantLanguageIds();
      $constraints[] = $queryBuilder->expr()->in(
        $this->languageFieldName ?? '',
        $queryBuilder->createNamedParameter($languageIds, Connection::PARAM_INT_ARRAY)
      );
    }

    $results = $queryBuilder
      ->select(...$this->persistenceFieldNames)
      ->where(...$constraints)
    ;

    $results = $queryBuilder->executeQuery()->fetchAllAssociative();

    // return first result record in case table is not language aware
    if (!$languageAware) {
      return $results[0] ?? null;
    }

    // post-process language fallbacks
    return $this->resolveLanguageFallback($results, $this->languageFieldName, $languageIds);
  }
}
