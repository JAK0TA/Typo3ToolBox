<?php

// Copyright JAKOTA Design Group GmbH. All rights reserved.
declare(strict_types=1);

namespace JAKOTA\Typo3ToolBox\ViewHelpers;

use JAKOTA\Typo3ToolBox\Utility\FocusPointUtility;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Imaging\ImageManipulation\CropVariantCollection;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

class FocusPointFromDbViewHelper extends AbstractViewHelper {
  /**
   * Initialize arguments.
   */
  public function initializeArguments(): void {
    $this->registerArgument('uid', 'string', 'uid');
    $this->registerArgument('cropVariant', 'string', 'Crop variant', false, 'default');
    $this->registerArgument('type', 'string', 'Typ');
  }

  public function render(): float {
    $uid = $this->arguments['uid'];
    $cropVariant = $this->arguments['cropVariant'];
    $type = $this->arguments['type'];

    $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable('sys_file_reference');
    $crop = $queryBuilder
      ->select('crop')
      ->from('sys_file_reference')
      ->where($queryBuilder->expr()->eq('sys_file_reference.uid', $uid))
      ->executeQuery()
      ->fetchOne()
    ;
    if (is_bool($crop)) {
      return 0.0;
    }

    $cropVariantCollection = CropVariantCollection::create(strval($crop));
    $cropArea = $cropVariantCollection->getFocusArea(strval($cropVariant));

    return FocusPointUtility::getFocusPoint($type, $cropArea);
  }
}
