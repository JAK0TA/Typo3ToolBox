# TYPO3 DebuggerUtility for SQL queries

Debug your SQL queries made easy.

## Configuration

To use this extension, require it in [Composer](https://getcomposer.org/):

```Shell
composer require --dev jakota/debuggerutility
```

### Dump SQL Query for createQuery

```PHP
    $query = $this->createQuery();

    DebuggerUtility::debugQuery($query)
```

### Dump SQL Query for QueryBuilder

```PHP
    $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getConnectionForTable('table')->createQueryBuilder();
    $queryBuilder
      ->select('*')
      ->from('table')
      ->where(
        $queryBuilder->expr()->eq('uid', $queryBuilder->createNamedParameter(123)),
      )
      ->setMaxResults(1)
    ;

    DebuggerUtility::debugQuery($queryBuilder)
```
