<?php

// Copyright JAKOTA Design Group GmbH. All rights reserved.
declare(strict_types=1);

namespace JAKOTA\Typo3ToolBox\Utility;

use TYPO3\CMS\Core\Imaging\ImageManipulation\CropVariantCollection;
use TYPO3\CMS\Core\MetaTag\MetaTagManagerRegistry;
use TYPO3\CMS\Core\Resource\FileReference;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Mvc\Request;
use TYPO3\CMS\Extbase\Service\ImageService;

/**
* Usage: 
* GeneralUtility::makeInstance(OgTagUtility::class)->registerTags($this->request, $name, $description, $objectImage);
*/
class OgTagUtility {
  public function registerTags(Request $request, string $title, string $description, ?FileReference $image, string $cardType = 'summary'): void {
    $metaTagManager = GeneralUtility::makeInstance(MetaTagManagerRegistry::class);

    $websiteTitle = $request->getAttribute('site')?->getAttribute('websiteTitle') ?? '';
    $objectImage = $this->resizeAndCropImage($image);
    $url = $request->getAttribute('normalizedParams')?->getRequestUrl() ?? '';

    $metaTagManager->getManagerForProperty('og:url')->addProperty('og:url', $url);
    $metaTagManager->getManagerForProperty('og:title')->addProperty('og:title', $title);
    $metaTagManager->getManagerForProperty('og:description')->addProperty('og:description', $description);
    $metaTagManager->getManagerForProperty('og:image')->addProperty('og:image', $objectImage['url'] ?? '');
    $metaTagManager->getManagerForProperty('og:image:width')->addProperty('og:image:width', $objectImage['width'] ?? '0');
    $metaTagManager->getManagerForProperty('og:image:height')->addProperty('og:image:height', $objectImage['height'] ?? '0');
    $metaTagManager->getManagerForProperty('og:site_name')->addProperty('og:site_name', $websiteTitle);
    $metaTagManager->getManagerForProperty('og:type')->addProperty('og:type', 'website');
  }

  /**
   * @return array<string, string>
   */
  private function resizeAndCropImage(?FileReference $image): array {
    if (null == $image) {
      return [];
    }
    $imageService = GeneralUtility::makeInstance(ImageService::class);
    $getImage = $imageService->getImage('', $image, false);
    $cropString = null;
    if ($getImage->hasProperty('crop') && $getImage->getProperty('crop')) {
      $cropString = $getImage->getProperty('crop');
    }

    $cropVariantCollection = CropVariantCollection::create(strval($cropString));
    $cropArea = $cropVariantCollection->getCropArea('default');

    $processedImage = $imageService->applyProcessingInstructions($getImage, [
      // optimal 1.91:1 ratio and resolution
      'width' => 1500,
      'height' => 786,
      'crop' => $cropArea->isEmpty() ? null : $cropArea->makeAbsoluteBasedOnFile($getImage),
    ]);
    $image = $imageService->getImageUri($processedImage, true);

    return [
      'url' => strval($image),
      'width' => strval($processedImage->getProperty('width')),
      'height' => strval($processedImage->getProperty('height')),
    ];
  }
}
