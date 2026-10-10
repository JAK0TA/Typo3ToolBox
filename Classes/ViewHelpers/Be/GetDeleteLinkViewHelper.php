<?php

// Copyright JAKOTA Design Group GmbH. All rights reserved.
declare(strict_types=1);

namespace JAKOTA\Typo3ToolBox\ViewHelpers\Be;

use TYPO3\CMS\Backend\Routing\Exception\RouteNotFoundException;
use TYPO3\CMS\Backend\Routing\UriBuilder;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

class GetDeleteLinkViewHelper extends AbstractViewHelper {
  /**
   * Initializes the arguments.
   */
  public function initializeArguments(): void {
    $this->registerArgument('tablename', 'string', 'Name of the database table', true);
    $this->registerArgument('uid', 'int', 'UID of the Record to edit.', true);
  }

  /**
   * Render link.
   *
   * @throws RouteNotFoundException
   */
  public function render(): string {
    $uriBuilder = GeneralUtility::makeInstance(UriBuilder::class);
    $uid = strval($this->arguments['uid']);
    $tableName = strval($this->arguments['tablename']);

    return (string) $uriBuilder->buildUriFromRoute('tce_db', [
      'cmd['.$tableName.']['.$uid.'][delete]' => 1,
      'redirect' => GeneralUtility::getIndpEnv('REQUEST_URI'),
    ]);
  }
}
