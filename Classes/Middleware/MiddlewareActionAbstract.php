<?php

// Copyright JAKOTA Design Group GmbH. All rights reserved.
declare(strict_types=1);

namespace JAKOTA\Typo3ToolBox\Middleware;

use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Core\Context\Context;
use TYPO3\CMS\Core\Context\LanguageAspect;
use TYPO3\CMS\Core\Context\UserAspect;
use TYPO3\CMS\Core\Localization\LanguageService;
use TYPO3\CMS\Core\Localization\LanguageServiceFactory;
use TYPO3\CMS\Core\Routing\PageArguments;
use TYPO3\CMS\Core\Site\Entity\Site;
use TYPO3\CMS\Core\Site\Entity\SiteLanguage;
use TYPO3\CMS\Core\TypoScript\TemplateService;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Domain\Model\FileReference;
use TYPO3\CMS\Extbase\Mvc\Web\Routing\UriBuilder;
use TYPO3\CMS\Frontend\Authentication\FrontendUserAuthentication;
use TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController;

abstract class MiddlewareActionAbstract extends ApiAbstract {
  protected string $languageFile = '';

  protected ?LanguageService $languageService = null;

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

  protected ?Site $site;

  protected ?SiteLanguage $siteLanguage;

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
      $this->siteLanguage = $this->site?->getLanguageById(intval($langId));
      $this->request = $this->request->withAttribute('language', $this->siteLanguage);
    } else {
      $this->siteLanguage = $this->request->getAttribute('language');
    }

    $this->languageServiceFactory = GeneralUtility::makeInstance(LanguageServiceFactory::class);

    // Set LanguageAspect
    $context = GeneralUtility::makeInstance(Context::class);
    $context->setAspect('language', new LanguageAspect($this->siteLanguage?->getLanguageId() ?? 0));

    // Set UserAspect
    $frontendUser = $this->request->getAttribute('frontend.user', GeneralUtility::makeInstance(FrontendUserAuthentication::class));
    $context->setAspect('frontend.user', new UserAspect($frontendUser));

    if (null !== $this->siteLanguage) {
      $this->languageService = $this->languageServiceFactory->createFromSiteLanguage($this->siteLanguage);
      $GLOBALS['LANG'] = $this->languageService;

      if (!isset($GLOBALS['TSFE'])) {
        $parsedBody = (array) $this->request->getParsedBody();

        $GLOBALS['TSFE'] = GeneralUtility::makeInstance(
          TypoScriptFrontendController::class,
          $context,
          $this->site,
          $this->siteLanguage,
          new PageArguments(
            intval($this->queryParams['id'] ?? $parsedBody['id'] ?? $this->site?->getRootPageId() ?? 0),
            strval($this->queryParams['type'] ?? $parsedBody['type'] ?? ''),
            [],
            $this->queryParams
          ),
          $frontendUser,
        );
        $GLOBALS['TSFE']->tmpl = GeneralUtility::makeInstance(TemplateService::class);
        $GLOBALS['TSFE']->determineId($this->request);
        $GLOBALS['TSFE']->getConfigArray();
        $GLOBALS['TSFE']->newCObj($this->request);
      }
    }

    $this->uriBuilder = GeneralUtility::makeInstance(UriBuilder::class);
  }

  protected function getAbsPath(?FileReference $file): string {
    if (null == $file) {
      return '';
    }
    if (null == $this->siteLanguage) {
      return '';
    }

    return $this->siteLanguage->getBase()->getScheme().'://'.$this->siteLanguage->getBase()->getHost().'/'.$file->getOriginalResource()->getPublicUrl();
  }

  protected function getLocalizationFromKey(string $keyToTranslate, string $languageFile = ''): string {
    if (empty($languageFile)) {
      $languageFile = $this->languageFile;
    }

    if (null === $this->siteLanguage || null === $this->languageService || empty($languageFile)) {
      return $keyToTranslate;
    }

    $translatedString = $this->languageService->sL('LLL:'.$languageFile.':'.$keyToTranslate);

    if (!empty($translatedString)) {
      return $translatedString;
    }

    return $keyToTranslate;
  }
}
