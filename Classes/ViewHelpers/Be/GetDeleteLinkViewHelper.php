<?php

// Copyright JAKOTA Design Group GmbH. All rights reserved.
declare(strict_types=1);

namespace JAKOTA\Newsletter\ViewHelpers;

use TYPO3\CMS\Backend\Routing\UriBuilder;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;
use TYPO3Fluid\Fluid\Core\ViewHelper\Traits\CompileWithRenderStatic;

class GetDeleteLinkViewHelper extends AbstractViewHelper {
  use CompileWithRenderStatic;

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
   * @param array<string, mixed> $arguments
   *
   * @throws \TYPO3\CMS\Backend\Routing\Exception\RouteNotFoundException
   */
  public static function renderStatic(array $arguments, \Closure $renderChildrenClosure, RenderingContextInterface $renderingContext): string {
    $uriBuilder = GeneralUtility::makeInstance(UriBuilder::class);
    $uid = strval($arguments['uid']);
    $tableName = strval($arguments['tablename']);

    return (string) $uriBuilder->buildUriFromRoute('tce_db', [
      'cmd['.$tableName.']['.$uid.'][delete]' => 1,
      'redirect' => GeneralUtility::getIndpEnv('REQUEST_URI'),
    ]);
  }
}
