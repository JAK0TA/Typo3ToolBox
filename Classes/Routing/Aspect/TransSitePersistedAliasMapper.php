<?php

// Copyright JAKOTA Design Group GmbH. All rights reserved.
declare(strict_types=1);

namespace JAKOTA\Typo3ToolBox\Routing\Aspect;

use Doctrine\DBAL\Connection;
use TYPO3\CMS\Core\Information\Typo3Version;
use TYPO3\CMS\Core\Routing\Aspect\PersistedAliasMapper;
use TYPO3\CMS\Core\Utility\GeneralUtility;

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
        $queryBuilder->createNamedParameter($value, \PDO::PARAM_STR)
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

    $typo3Version = GeneralUtility::makeInstance(Typo3Version::class);
    if (version_compare($typo3Version->getVersion(), '11.5.0') >= 0) {
      $results = $queryBuilder->executeQuery()->fetchAssociative();
    } else {
      $results = $queryBuilder->execute()->fetchAll();
    }

    // return first result record in case table is not language aware
    if (!$languageAware) {
      return $results[0] ?? null;
    }
    // post-process language fallbacks
    return $this->resolveLanguageFallback($results, $this->languageFieldName, $languageIds);
  }
}
