<?php

// Copyright JAKOTA Design Group GmbH. All rights reserved.
declare(strict_types=1);

namespace JAKOTA\Typo3ToolBox\ViewHelpers;

use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

class CacheViewHelper extends AbstractViewHelper {
  /**
   * Initialize arguments.
   */
  public function initializeArguments(): void {
    parent::initializeArguments();
    $this->registerArgument('id', 'mixed', 'Could be all, will be used as cacheHash', true);
  }

  public function render(): string {
    $cacheLifetime = 3600; // 60*60
    $success = false;
    $cacheHash = md5(serialize($this->arguments['id']));

    $output = apc_fetch($cacheHash, $success);
    if (!$success) {
      $output = $this->renderChildren();
      apc_store($cacheHash, $output, $cacheLifetime);
    }

    return strval($output);
  }
}
