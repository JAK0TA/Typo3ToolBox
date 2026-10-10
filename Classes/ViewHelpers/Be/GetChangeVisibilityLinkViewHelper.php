<?php

// Copyright JAKOTA Design Group GmbH. All rights reserved.
declare(strict_types=1);

namespace JAKOTA\Typo3ToolBox\ViewHelpers\Be;

use TYPO3\CMS\Backend\Routing\Exception\RouteNotFoundException;
use TYPO3\CMS\Backend\Routing\UriBuilder;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

class GetChangeVisibilityLinkViewHelper extends AbstractViewHelper {
  /**
   * Initializes the arguments.
   */
  public function initializeArguments(): void {
    $this->registerArgument('tablename', 'string', 'Name of the database table', true);
    $this->registerArgument('command', 'string', 'Hide or unhide a Record.', true);
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

    switch ($this->arguments['command']) {
      case 'unhide':
        $urlParameters = [
          'data['.$tableName.']['.$uid.'][disabled]' => 0,
          'redirect' => GeneralUtility::getIndpEnv('REQUEST_URI'),
        ];

        break;

      case 'hide':
        $urlParameters = [
          'data['.$tableName.']['.$uid.'][disabled]' => 1,
          'redirect' => GeneralUtility::getIndpEnv('REQUEST_URI'),
        ];

        break;

      default:
        throw new \InvalidArgumentException('Invalid command given to GetChangeVisibilityLinkViewHelper.', 1516708789);
    }

    return (string) $uriBuilder->buildUriFromRoute('tce_db', $urlParameters);
  }
}
