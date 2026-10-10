<?php

// Copyright JAKOTA Design Group GmbH. All rights reserved.
declare(strict_types=1);

namespace JAKOTA\Typo3ToolBox\ViewHelpers;

use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\VersionNumberUtility;
use TYPO3\CMS\Fluid\Core\Rendering\RenderingContext;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;
use TYPO3Fluid\Fluid\Core\ViewHelper\Exception;

class RenderContentViewHelper extends AbstractViewHelper {
  /**
   * As this ViewHelper renders HTML, the output must not be escaped.
   *
   * @var bool
   */
  protected $escapeOutput = false;

  /**
   * Initialize arguments.
   *
   * @throws Exception
   */
  public function initializeArguments(): void {
    $this->registerArgument('uids', 'string', 'Content Uids', true);
  }

  public function render(): string {
    $elements = '';

    if (!$this->renderingContext instanceof RenderingContext) {
      return '';
    }

    $request = $this->renderingContext->getAttribute(ServerRequestInterface::class);

    /** @var null|ContentObjectRenderer $contentObject */
    $contentObject = $request->getAttribute('currentContentObject');

    if (null === $contentObject && version_compare(VersionNumberUtility::getCurrentTypo3Version(), '14', '<')) {
      $frontendController = $request->getAttribute('frontend.controller');
      // @phpstan-ignore-next-line
      $contentObject = $frontendController?->cObj ?? null;
    }

    if (!$contentObject) {
      /** @var ContentObjectRenderer $contentObject */
      $contentObject = GeneralUtility::makeInstance(ContentObjectRenderer::class);
    }

    foreach (GeneralUtility::intExplode(',', strval($this->arguments['uids'] ?? '')) as $uid) {
      $conf = [
        'tables' => 'tt_content',
        'source' => $uid,
        'dontCheckPid' => 1,
      ];

      $html = $contentObject->cObjGetSingle('RECORDS', $conf);

      $elements .= $html;
    }

    return $elements;
  }
}
