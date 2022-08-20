<?php

declare(strict_types=1);

$config = [];

if (!interface_exists(Stringable::class)) {
  // compatbility with PHP <8
  $config['parameters']['ignoreErrors'][] = [
    'message' => '#Call to an undefined method [a-zA-Z0-9\\_]+::executeQuery\(\).#',
    'path' => '../Classes/ViewHelpers/FindImageMetadataFromDBViewHelper.php',
    'count' => 1,
  ];
  $config['parameters']['ignoreErrors'][] = [
    'message' => '#Call to an undefined method [a-zA-Z0-9\\_]+::executeQuery\(\).#',
    'path' => '../Classes/ViewHelpers/FocusPointFromDbViewHelper.php',
    'count' => 1,
  ];
  $config['parameters']['ignoreErrors'][] = [
    'message' => '#Call to an undefined method [a-zA-Z0-9\\_]+::getOffsetLeft\(\).#',
    'path' => '../Classes/ViewHelpers/FocusPointFromDbViewHelper.php',
    'count' => 1,
  ];
  $config['parameters']['ignoreErrors'][] = [
    'message' => '#Call to an undefined method [a-zA-Z0-9\\_]+::getOffsetTop\(\).#',
    'path' => '../Classes/ViewHelpers/FocusPointFromDbViewHelper.php',
    'count' => 1,
  ];
  $config['parameters']['ignoreErrors'][] = [
    'message' => '#Call to an undefined method [a-zA-Z0-9\\_]+::getHeight\(\).#',
    'path' => '../Classes/ViewHelpers/FocusPointFromDbViewHelper.php',
    'count' => 1,
  ];
  $config['parameters']['ignoreErrors'][] = [
    'message' => '#Call to an undefined method [a-zA-Z0-9\\_]+::getWidth\(\).#',
    'path' => '../Classes/ViewHelpers/FocusPointFromDbViewHelper.php',
    'count' => 1,
  ];
  $config['parameters']['ignoreErrors'][] = [
    'message' => '#Call to an undefined method [a-zA-Z0-9\\_]+::getOffsetLeft\(\).#',
    'path' => '../Classes/ViewHelpers/FocusPointViewHelper.php',
    'count' => 1,
  ];
  $config['parameters']['ignoreErrors'][] = [
    'message' => '#Call to an undefined method [a-zA-Z0-9\\_]+::getOffsetTop\(\).#',
    'path' => '../Classes/ViewHelpers/FocusPointViewHelper.php',
    'count' => 1,
  ];
  $config['parameters']['ignoreErrors'][] = [
    'message' => '#Call to an undefined method [a-zA-Z0-9\\_]+::getHeight\(\).#',
    'path' => '../Classes/ViewHelpers/FocusPointViewHelper.php',
    'count' => 1,
  ];
  $config['parameters']['ignoreErrors'][] = [
    'message' => '#Call to an undefined method [a-zA-Z0-9\\_]+::getWidth\(\).#',
    'path' => '../Classes/ViewHelpers/FocusPointViewHelper.php',
    'count' => 1,
  ];
}

if (interface_exists(Stringable::class)) {
  // compatbility with PHP >=8
  $config['parameters']['ignoreErrors'][] = [
    'message' => '#Cannot call method [a-zA-Z0-9\\_]+::fetchAll\(\) on Doctrine\\DBAL\\Result|int.#',
    'path' => '../Classes/ViewHelpers/FindImageMetadataFromDBViewHelper.php',
    'count' => 1,
  ];
  $config['parameters']['ignoreErrors'][] = [
    'message' => '#Cannot call method [a-zA-Z0-9\\_]+::fetchAll\(\) on Doctrine\\DBAL\\Result|int.#',
    'path' => '../Classes/ViewHelpers/FocusPointFromDbViewHelper.php',
    'count' => 1,
  ];
}

return $config;
