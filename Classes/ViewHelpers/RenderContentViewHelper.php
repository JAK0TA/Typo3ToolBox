<?php

// Copyright JAKOTA Design Group GmbH. All rights reserved.
declare(strict_types=1);

namespace JAKOTA\Typo3ToolBox\ViewHelpers;

use TYPO3\CMS\Core\Information\Typo3Version;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Fluid\Core\Rendering\RenderingContext;
use TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController;
use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;
use TYPO3Fluid\Fluid\Core\ViewHelper\Traits\CompileWithRenderStatic;

class RenderContentViewHelper extends AbstractViewHelper {
  use CompileWithRenderStatic;

  /**
   * As this ViewHelper renders HTML, the output must not be escaped.
   *
   * @var bool
   */
  protected $escapeOutput = false;

  /**
   * Initialize arguments.
   *
   * @throws \TYPO3Fluid\Fluid\Core\ViewHelper\Exception
   */
  public function initializeArguments(): void {
    $this->registerArgument('uids', 'string', 'Content Uids', true);
  }

  /**
   * @param array<string, mixed> $arguments
   */
  public static function renderStatic(array $arguments, \Closure $renderChildrenClosure, RenderingContextInterface $renderingContext): string {
    $elements = '';

    $typo3Version = GeneralUtility::makeInstance(Typo3Version::class);
    if (version_compare($typo3Version->getVersion(), '11.5.0') >= 0) {
      /** @var RenderingContext $renderingContext */
      $request = $renderingContext->getRequest();

      $frontendController = $request->getAttribute('frontend.controller');
    } else {
      /** @var TypoScriptFrontendController $frontendController */
      $frontendController = $GLOBALS['TSFE'];
    }
    if (null === $frontendController) {
      return '';
    }

    foreach (GeneralUtility::intExplode(',', strval($arguments['uids'] ?? '')) as $uid) {
      if (0 < ($frontendController->recordRegister['tt_content:'.$uid] ?? 0)) {
        continue;
      }
      $conf = [
        'tables' => 'tt_content',
        'source' => $uid,
        'dontCheckPid' => 1,
      ];
      $parent = $frontendController->currentRecord;
      // If the currentRecord is set, we register, that this record has invoked this function.
      // It's should not be allowed to do this again then!!
      if (!empty($parent) && isset($frontendController->recordRegister[$parent])) {
        ++$frontendController->recordRegister[$parent];
      }
      $html = $frontendController->cObj->cObjGetSingle('RECORDS', $conf);

      $frontendController->currentRecord = $parent;
      if (!empty($parent) && isset($frontendController->recordRegister[$parent])) {
        --$frontendController->recordRegister[$parent];
      }

      $elements .= $html;
    }

    return $elements;
  }
}
