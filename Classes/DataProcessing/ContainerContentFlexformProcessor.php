<?php

// Copyright JAKOTA Design Group GmbH. All rights reserved.
declare(strict_types=1);

namespace JAKOTA\Typo3ToolBox\DataProcessing;

use TYPO3\CMS\Core\Service\FlexFormService;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;
use TYPO3\CMS\Frontend\ContentObject\DataProcessorInterface;

class ContainerContentFlexformProcessor implements DataProcessorInterface {
  /**
   * Warning This processor depends on Typo3 v11.5+.
   *
   * @param array<string, mixed> $contentObjectConfiguration
   * @param array<string, mixed> $processorConfiguration
   * @param array<string, mixed> $processedData
   *
   * @return array<string, mixed> */
  public function process(
    ContentObjectRenderer $cObj,
    array $contentObjectConfiguration,
    array $processorConfiguration,
    array $processedData
  ): array {
    if (empty($processorConfiguration['content'] ?? '') || !isset($processedData[$processorConfiguration['content']]) || !is_array($processedData[$processorConfiguration['content']])) {
      return $processedData;
    }

    $flexFormService = GeneralUtility::makeInstance(FlexFormService::class);

    foreach ($processedData[$processorConfiguration['content']] as &$content) {
      if (isset($content['pi_flexform'])) {
        $content[$processorConfiguration['as'] ?? 'flexform'] = $flexFormService->convertFlexFormContentToArray($content['pi_flexform']);
      }
    }

    return $processedData;
  }
}
