<?php

// Copyright JAKOTA Design Group GmbH. All rights reserved.
declare(strict_types=1);

namespace JAKOTA\Typo3ToolBox\Utility;

use TYPO3\CMS\Core\Http\ServerRequest;
use TYPO3\CMS\Core\Site\Entity\Site;
use TYPO3\CMS\Core\TypoScript\FrontendTypoScriptFactory;
use TYPO3\CMS\Core\TypoScript\IncludeTree\SysTemplateRepository;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\RootlineUtility;
use TYPO3\CMS\Extbase\Configuration\BackendConfigurationManager;

class TypoScriptUtility {
  public function __construct(
    private BackendConfigurationManager $backendConfigurationManager,
    private FrontendTypoScriptFactory $frontendTypoScriptFactory,
    private SysTemplateRepository $sysTemplateRepository
  ) {}

  public function addTypoScriptSetupToRequest(ServerRequest $request, Site $site): ServerRequest {
    $rootLine = GeneralUtility::makeInstance(RootlineUtility::class, $site->getRootPageId())->get();

    $rootLineForSysTemplates = $rootLine;
    if ($site instanceof Site && $site->isTypoScriptRoot()) {
      $rootLineForSysTemplates = [];
      foreach ($rootLine as $index => $rootlinePage) {
        $rootLineForSysTemplates[$index] = $rootlinePage;
        if ((int) ($rootlinePage['uid'] ?? 0) === $site->getRootPageId()) {
          break;
        }
      }
    }
    $sysTemplateRows = $this->sysTemplateRepository->getSysTemplateRowsByRootline($rootLineForSysTemplates, $request);
    ksort($rootLine);

    $expressionMatcherVariables = [
      'request' => $request,
      'pageId' => $site->getRootPageId(),
      'page' => !empty($rootLine) ? $rootLine[array_key_first($rootLine)] : [],
      'fullRootLine' => $rootLine,
      'site' => $site,
    ];

    $typoScript = $this->frontendTypoScriptFactory->createSettingsAndSetupConditions($site, $sysTemplateRows, $expressionMatcherVariables, null);
    $typoScript = $this->frontendTypoScriptFactory->createSetupConfigOrFullSetup(true, $typoScript, $site, $sysTemplateRows, $expressionMatcherVariables, '0', null, null);

    $frontendTypoScript = $request->getAttribute('frontend.typoscript');
    if (null === $frontendTypoScript || !$frontendTypoScript->hasSetup()) {
      $request = $request->withAttribute('frontend.typoscript', $typoScript);
      $GLOBALS['TYPO3_REQUEST'] = $request;
    }

    return $request;
  }
}
