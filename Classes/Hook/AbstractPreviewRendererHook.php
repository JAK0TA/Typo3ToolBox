<?php

// Copyright JAKOTA Design Group GmbH. All rights reserved.
declare(strict_types=1);

namespace JAKOTA\Typo3ToolBox\Hook;

use TYPO3\CMS\Backend\View\PageLayoutView;
use TYPO3\CMS\Backend\View\PageLayoutViewDrawItemHookInterface;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Resource\FileReference;
use TYPO3\CMS\Core\Resource\FileRepository;
use TYPO3\CMS\Core\Service\FlexFormService;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3\CMS\Extbase\Utility\DebuggerUtility;
use TYPO3\CMS\Fluid\View\StandaloneView;

abstract class AbstractPreviewRendererHook implements PageLayoutViewDrawItemHookInterface {
  protected string $contentTable = 'tt_content';

  protected string $cType = '';

  /** @var array<string, mixed> */
  protected array $data = [];

  protected string $extensionKey = '';

  protected string $fieldnameImage = 'assets';

  protected string $fieldnameVideo = 'video';

  protected string $fluidStylePath = 'FluidStyledContent/';

  protected bool $isSlider = false;

  protected string $previewTemplatePath = 'PreviewTemplates/';

  protected string $resourcePath = '';

  public function __construct() {
    if (empty($this->cType)) {
      DebuggerUtility::var_dump('Property cType must not be empty', '1586703436');
    }

    if (empty($this->extensionKey)) {
      DebuggerUtility::var_dump('Property extensionKey must not be empty', '1586703436');
    }

    $this->resourcePath = 'EXT:'.$this->extensionKey.'/Resources/Private/';
  }

  /**
   * Preprocesses the preview rendering of a content element.
   * overwrites function preProcess of PageLayoutViewDrawItemHookInterface.
   *
   * @param PageLayoutView       $parentObject  Calling parent object
   * @param bool                 $drawItem      Whether to draw the item using the default functionality
   * @param string               $headerContent Header content
   * @param string               $itemContent   Item content
   * @param array<string, mixed> $row           Record row of tt_content
   */
  public function preProcess(
    PageLayoutView &$parentObject,
    &$drawItem,
    &$headerContent,
    &$itemContent,
    &$row
  ): void {
    $drawItem = boolval($drawItem);
    $headerContent = strval($headerContent);
    $itemContent = strval($itemContent);

    $this->data = &$row;
    if ($this->isCtypeMatching() && $this->checkTemplateFile()) {
      $drawItem = false;
      $headerContent = $this->getHeaderContent();
      $itemContent .= $this->getBodytext();
    }
  }

  protected function checkTemplateFile(): bool {
    if (false === is_file($this->getTemplateFile())) {
      DebuggerUtility::var_dump('Expected template file for preview rendering for CType '.$this->cType.' is missing', '1586703260');
    }

    return true;
  }

  protected function getBodytext(): string {
    $standaloneView = GeneralUtility::makeInstance(ObjectManager::class)->get(StandaloneView::class);

    $standaloneView->setTemplatePathAndFilename($this->getTemplateFile());
    $standaloneView->setPartialRootPaths(
      [
        "{$this->resourcePath}{$this->previewTemplatePath}Partials/",
        "{$this->resourcePath}{$this->fluidStylePath}Partials/",
      ],
    );

    if ($this->isSlider) {
      $flexform = $this->getFlexForm();

      /** @var int[] */
      $slidesId = array_map('intval', explode(',', strval($flexform['slides'])));

      $contentData = $this->getContentData($slidesId);
      foreach ($slidesId as $slideId) {
        $images[$slideId] = $this->getFiles($this->fieldnameImage, $slideId);
        $videos[$slideId] = $this->getFiles($this->fieldnameVideo, $slideId);
      }
      $standaloneView->assignMultiple([
        'data' => $this->data,
        'flexForm' => $flexform,
        'slideContent' => $contentData,
        'slideImages' => $images,
        'slideVideos' => $videos,
        'extKey' => $this->extensionKey,
      ]);
    } else {
      $uid = intval($this->data['uid']);
      $images = $this->getFiles($this->fieldnameImage, $uid);
      $videos = $this->getFiles($this->fieldnameVideo, $uid);

      $standaloneView->assignMultiple([
        'data' => $this->data,
        'flexForm' => $this->getFlexForm(),
        'firstImage' => count($images) > 0 ? $images[0] : null,
        'images' => $images,
        'videos' => $videos,
        'extKey' => $this->extensionKey,
      ]);
    }

    return $standaloneView->render();
  }

  /**
   * @param array<int, int> $slideIds slideIds
   *
   * @return array<int, array<string, mixed>|false>
   */
  protected function getContentData(array $slideIds): array {
    $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable($this->contentTable);

    /** @var array<int, array<string, mixed>|false> */
    $returnArray = [];

    foreach ($slideIds as $slideId) {
      $returnArray[$slideId] = $queryBuilder
        ->select('*')
        ->from($this->contentTable)
        ->where('uid='.$slideId)
        ->setMaxResults(1)
        ->executeQuery()
        ->fetchAssociative()
      ;
    }

    return $returnArray;
  }

  /**
   * @return array<int, FileReference>
   */
  protected function getFiles(string $fieldname, int $uid): array {
    /** @var array<int, FileReference> */
    $references = [];

    $fileRepository = GeneralUtility::makeInstance(ObjectManager::class)
      ->get(FileRepository::class)
    ;

    $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable('sys_file_reference');

    $identifiers = $queryBuilder
      ->select('uid')
      ->from('sys_file_reference')
      ->where('uid_foreign='.$uid.' and tablenames="'.$this->contentTable.'" and fieldname="'.$fieldname.'"')
      ->executeQuery()
      ->fetchFirstColumn()
    ;

    foreach ($identifiers as $identifier) {
      $identifier = intval($identifier);
      if ($identifier > 0) {
        $reference = $fileRepository->findFileReferenceByUid($identifier);
        if (!is_bool($reference)) {
          $references[] = $reference;
        }
      }
    }

    return $references;
  }

  /**
   * @return array<string, mixed>
   */
  protected function getFlexForm(): array {
    $flexFormService = GeneralUtility::makeInstance(ObjectManager::class)
      ->get(FlexFormService::class)
    ;

    return $flexFormService->convertFlexFormContentToArray(strval($this->data['pi_flexform']));
  }

  protected function getHeaderContent(): string {
    return '<div id="element-tt_content-'.intval($this->data['uid'])
            .'" class="t3-ctype-identifier " data-ctype="'.$this->cType.'"></div>';
  }

  /**
   * Get absolute path to template file.
   */
  protected function getTemplateFile(): string {
    return GeneralUtility::getFileAbsFileName(
      $this->resourcePath.$this->previewTemplatePath.$this->data['CType'].'.html'
    );
  }

  protected function isCtypeMatching(): bool {
    return $this->data['CType'] === $this->cType;
  }
}
