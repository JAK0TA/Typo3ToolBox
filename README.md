# Typo3 ToolBox

ToolBox of Utility functions and ViewHelpers.

## Configuration

To use this extension, require it in [Composer](https://getcomposer.org/):

```Shell
composer require --dev jakota/typo3toolbox
```

## Utility functions

### DebuggerUtility for SQL queries

Debug your SQL queries made easy.

**Dump SQL Query for createQuery**

```PHP
    $query = $this->createQuery();

    DebuggerUtility::debugQuery($query)
```

**Dump SQL Query for QueryBuilder**

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

## ViewHelpers

### Find image metadata from DB

```Code
{t3tb:findImageMetadataFromDB(uid:image.originalResource.properties.file,language:image.originalResource.properties.sys_language_uid)}
```

TBD
