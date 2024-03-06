<?php

// Copyright JAKOTA Design Group GmbH. All rights reserved.
declare(strict_types=1);

namespace JAKOTA\Typo3ToolBox\Middleware;

use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Core\Context\Context;
use TYPO3\CMS\Core\Context\LanguageAspect;
use TYPO3\CMS\Core\Localization\LanguageService;
use TYPO3\CMS\Core\Localization\LanguageServiceFactory;
use TYPO3\CMS\Core\Site\Entity\Site;
use TYPO3\CMS\Core\Site\Entity\SiteLanguage;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Domain\Model\FileReference;
use TYPO3\CMS\Extbase\Mvc\Web\Routing\UriBuilder;

abstract class MiddlewareActionAbstract {
  protected LanguageService $languageService = null;

  protected LanguageServiceFactory $languageServiceFactory;

  /**
   * @var array<string, mixed>
   */
  protected array $pathParams = [];

  /**
   * @var array<string, mixed>
   */
  protected array $queryParams = [];

  protected ServerRequestInterface $request;

  protected Site $site;

  protected SiteLanguage $siteLanguage;

  protected UriBuilder $uriBuilder;

  /**
   * @param array<string, mixed> $pathParams
   *
   * @throws \InvalidArgumentException
   */
  public function __construct(ServerRequestInterface $request, $pathParams) {
    $this->request = $request;
    $this->pathParams = $pathParams;
    $this->queryParams = $this->request->getQueryParams();
    $this->site = $this->request->getAttribute('site');

    $langId = $this->queryParams['L'] ?? false;
    if (false != $langId) {
      $this->siteLanguage = $this->site->getLanguageById(intval($langId));
    } else {
      $this->siteLanguage = $this->request->getAttribute('language');
    }

    $this->languageServiceFactory = GeneralUtility::makeInstance(LanguageServiceFactory::class);
    $this->languageService = $this->languageServiceFactory->createFromSiteLanguage($this->siteLanguage);
    $GLOBALS['LANG'] = $this->languageService;

    // Set LanguageAspect for exbase
    $context = GeneralUtility::makeInstance(Context::class);
    $context->setAspect('language', new LanguageAspect($this->siteLanguage->getLanguageId()));

    $this->uriBuilder = GeneralUtility::makeInstance(UriBuilder::class);
  }

  protected function getAbsPath(?FileReference $file): string {
    if (null == $file) {
      return '';
    }

    return $this->siteLanguage->getBase()->getScheme().'://'.$this->siteLanguage->getBase()->getHost().'/'.$file->getOriginalResource()->getPublicUrl();
  }

  protected function getLocalizationFromKey(string $keyToTranslate, string $filePath): string {
    $translatedString = $this->languageService->sL('LLL:'.$filePath.':'.$keyToTranslate);

    if (!empty($translatedString)) {
      return $translatedString;
    }

    return $keyToTranslate;
  }
}
