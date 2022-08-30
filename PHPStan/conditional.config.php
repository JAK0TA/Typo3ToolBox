<?php

declare(strict_types=1);

use TYPO3\CMS\Core\Information\Typo3Version;

$config = [];

$config['parameters']['ignoreErrors'][] = [
  'message' => '#Cannot call method fetchAll\(\) on Doctrine\\DBAL\\Result|int.#',
  'path' => '../Classes/Routing/Aspect/TransSitePersistedAliasMapper.php',
  'count' => 1,
];
$config['parameters']['ignoreErrors'][] = [
  'message' => '#Cannot call method fetchAll\(\) on Doctrine\\DBAL\\Result|int.#',
  'path' => '../Classes/Utility/QueryUtility.php',
  'count' => 1,
];
$config['parameters']['ignoreErrors'][] = [
  'message' => '#Cannot call method fetchAll\(\) on Doctrdddine\\DBAL\\Result|int.#',
  'path' => '../Classes/ViewHelpers/FindImageMetadataFromDBViewHelper.php',
  'count' => 1,
];
$config['parameters']['ignoreErrors'][] = [
  'message' => '#Cannot call method fetchAll\(\) on Doctrine\\DBAL\\Result|int.#',
  'path' => '../Classes/ViewHelpers/FocusPointFromDbViewHelper.php',
  'count' => 1,
];

$typo3Version = new Typo3Version();
if (version_compare($typo3Version->getVersion(), '11.5.0', '<')) {
  $config['parameters']['ignoreErrors'][] = [
    'message' => '#Call to an undefined method TYPO3\\\\CMS\\\\Core\\\\Database\\\\Query\\\\QueryBuilder::executeQuery\(\).#',
    'path' => '../Classes/Routing/Aspect/TransSitePersistedAliasMapper.php',
    'count' => 1,
  ];
  $config['parameters']['ignoreErrors'][] = [
    'message' => '#Call to an undefined method TYPO3\\\\CMS\\\\Core\\\\Database\\\\Query\\\\QueryBuilder::executeQuery\(\).#',
    'path' => '../Classes/Utility/QueryUtility.php',
    'count' => 1,
  ];
  $config['parameters']['ignoreErrors'][] = [
    'message' => '#Call to an undefined method TYPO3\\\\CMS\\\\Core\\\\Database\\\\Query\\\\QueryBuilder::executeQuery\(\).#',
    'path' => '../Classes/ViewHelpers/FindImageMetadataFromDBViewHelper.php',
    'count' => 1,
  ];
  $config['parameters']['ignoreErrors'][] = [
    'message' => '#Call to an undefined method TYPO3\\\\CMS\\\\Core\\\\Database\\\\Query\\\\QueryBuilder::executeQuery\(\).#',
    'path' => '../Classes/ViewHelpers/FocusPointFromDbViewHelper.php',
    'count' => 1,
  ];
}

if (version_compare($typo3Version->getVersion(), '10.4.0', '<')) {
  $config['parameters']['ignoreErrors'][] = [
    'message' => '#Call to an undefined method TYPO3\\\\CMS\\\\Core\\\\Imaging\\\\ImageManipulation\\\\Area::getOffsetLeft\(\).#',
    'path' => '../Classes/ViewHelpers/FocusPointFromDbViewHelper.php',
    'count' => 1,
  ];
  $config['parameters']['ignoreErrors'][] = [
    'message' => '#Call to an undefined method TYPO3\\\\CMS\\\\Core\\\\Imaging\\\\ImageManipulation\\\\Area::getOffsetTop\(\).#',
    'path' => '../Classes/ViewHelpers/FocusPointFromDbViewHelper.php',
    'count' => 1,
  ];
  $config['parameters']['ignoreErrors'][] = [
    'message' => '#Call to an undefined method TYPO3\\\\CMS\\\\Core\\\\Imaging\\\\ImageManipulation\\\\Area::getHeight\(\).#',
    'path' => '../Classes/ViewHelpers/FocusPointFromDbViewHelper.php',
    'count' => 1,
  ];
  $config['parameters']['ignoreErrors'][] = [
    'message' => '#Call to an undefined method TYPO3\\\\CMS\\\\Core\\\\Imaging\\\\ImageManipulation\\\\Area::getWidth\(\).#',
    'path' => '../Classes/ViewHelpers/FocusPointFromDbViewHelper.php',
    'count' => 1,
  ];
  $config['parameters']['ignoreErrors'][] = [
    'message' => '#Call to an undefined method TYPO3\\\\CMS\\\\Core\\\\Imaging\\\\ImageManipulation\\\\Area::getOffsetLeft\(\).#',
    'path' => '../Classes/ViewHelpers/FocusPointViewHelper.php',
    'count' => 1,
  ];
  $config['parameters']['ignoreErrors'][] = [
    'message' => '#Call to an undefined method TYPO3\\\\CMS\\\\Core\\\\Imaging\\\\ImageManipulation\\\\Area::getOffsetTop\(\).#',
    'path' => '../Classes/ViewHelpers/FocusPointViewHelper.php',
    'count' => 1,
  ];
  $config['parameters']['ignoreErrors'][] = [
    'message' => '#Call to an undefined method TYPO3\\\\CMS\\\\Core\\\\Imaging\\\\ImageManipulation\\\\Area::getHeight\(\).#',
    'path' => '../Classes/ViewHelpers/FocusPointViewHelper.php',
    'count' => 1,
  ];
  $config['parameters']['ignoreErrors'][] = [
    'message' => '#Call to an undefined method TYPO3\\\\CMS\\\\Core\\\\Imaging\\\\ImageManipulation\\\\Area::getWidth\(\).#',
    'path' => '../Classes/ViewHelpers/FocusPointViewHelper.php',
    'count' => 1,
  ];
}

return $config;
