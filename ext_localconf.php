<?php

// Copyright JAKOTA Design Group GmbH. All rights reserved.
declare(strict_types=1);

use JAKOTA\Typo3ToolBox\Enhancer\ApiEnhancer;

if (!defined('TYPO3')) {
  exit;
}

(function () {
  // @phpstan-ignore-next-line
  $GLOBALS['TYPO3_CONF_VARS']['SYS']['routing']['enhancers']['ApiEnhancer'] = ApiEnhancer::class;
})();
