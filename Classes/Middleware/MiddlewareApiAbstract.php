<?php

// Copyright JAKOTA Design Group GmbH. All rights reserved.
declare(strict_types=1);

namespace JAKOTA\Typo3ToolBox\Middleware;

use JAKOTA\Typo3ToolBox\Definition\ApiMethodDefinition;
use JAKOTA\Typo3ToolBox\Definition\ContentTypeDefinition;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\UriInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use TYPO3\CMS\Core\Imaging\ImageManipulation\CropVariantCollection;
use TYPO3\CMS\Core\Localization\LanguageService;
use TYPO3\CMS\Core\Localization\LanguageServiceFactory;
use TYPO3\CMS\Core\Routing\PageRouter;
use TYPO3\CMS\Core\Site\Entity\Site;
use TYPO3\CMS\Core\Site\Entity\SiteLanguage;
use TYPO3\CMS\Extbase\Domain\Model\FileReference;
use TYPO3\CMS\Extbase\Mvc\Web\Routing\UriBuilder;
use TYPO3\CMS\Extbase\Service\ImageService;
use TYPO3\CMS\Frontend\Controller\ErrorController;

/**
 * Add to RequestMiddlewares.php in your project.
 *
 *   'VENDOR/ext_name/middleware/frontend/ApiName' => [
 *     'target' => ApiName::class,
 *     'before' => [
 *       'typo3/cms-frontend/shortcut-and-mountpoint-redirect',
 *     ],
 *     'after' => [
 *       'typo3/cms-frontend/tsfe',
 *     ],
 *   ],
 *
 * Add file Classes/Middleware/ApiName.php in your project.
 *
 * class ApiName extends MiddlewareApiAbstract {
 *   protected function handleApiRequests(): void {
 *     $this->handleApiCall('ApiName', 'endpointName1', $this->handleEndpointName1(...));
 *     $this->handleApiCall('ApiName', 'endpointName2', $this->handleEndpointName2(...), ApiMethodDefinition::POST);
 *   }
 *   /**
 *    * @param array<string, mixed> $apiArguments
 *    * /
 *   private function handleEndpointName1(array $apiArguments): void {
 *
 *     $response = GeneralUtility::makeInstance(
 *       DoSomething1::class,
 *       $this,
 *     )->getFoo();
 *
 *     $this->createResponse($response, ContentTypeDefinition::JSON);
 *   }
 *    * @param array<string, mixed> $apiArguments
 *    * /
 *   private function handleEndpointName2(array $apiArguments): void {
 *     $response = 'Export failed';
 *
 *     if (!isset($apiArguments['var'])) {
 *       $this->createResponse($response, ContentTypeDefinition::JSON);
 *     }
 *
 *     $response = GeneralUtility::makeInstance(
 *       DoSomething2::class,
 *       $this,
 *     )->getBar(strval($apiArguments['var']));
 *
 *     $this->createResponse($response, ContentTypeDefinition::JSON);
 *   }
 * }
 *
 * Add file Classes/Middleware/EndpointName1/DoSomething1.php in your project.
 *
 * class ApiName  {
 *
 *   public function __construct(protected MiddlewareApiAbstract $middlewareApi) {
 *     $this->middlewareApi->languageFile = 'EXT:ext_name/Resources/Private/Language/locallang_db.xlf';
 *   }
 *
 *   public function getFoo(): string {
 *     return '{"json":stuff}';
 *   }
 * }
 */
abstract class MiddlewareApiAbstract implements MiddlewareInterface {
  public string $languageFile = '';

  /**
   * @var RequestHandlerInterface
   */
  protected $handler;

  protected ?LanguageService $languageService = null;

  /**
   * @var ServerRequestInterface
   */
  protected $request;

  /**
   * @var array<int|string, mixed>|object
   */
  protected $requestBody = [];

  /**
   * @var ?ResponseInterface
   */
  protected $response;

  protected ?Site $site;

  protected ?SiteLanguage $siteLanguage;

  /**
   * @var array<string, array<string, string>|string>
   */
  private array $apis = [];

  public function __construct(
    protected ImageService $imageService,
    protected LanguageServiceFactory $languageServiceFactory,
    protected ResponseFactoryInterface $responseFactory,
    protected UriBuilder $uriBuilder,
    protected ErrorController $errorController
  ) {}

  /**
   * Function to build urls without using $this->uriBuilder.
   *
   * @param array<string, mixed> $additionalGetParams additional GET parameters
   * @param null|string          $extensionName       Extension name without underscores. Eg. 'myextension'
   * @param null|string          $pluginName          Plugin name with underscores. Eg. 'news_show' or 'records_list'
   * @param null|string          $actionName          Action name (the 'show' of 'news_show')
   * @param null|string          $controllerName      Name of the controller. Eg. 'News' or 'Record'
   * @param array<string, mixed> $actionArguments     Additional arguments needed for the Action. Eg. [newsId => 123, ...]
   */
  public function buildUri(int $pageId, array $additionalGetParams = [], ?string $extensionName = null, ?string $pluginName = null, ?string $actionName = null, ?string $controllerName = null, array $actionArguments = [], string $fragment = '', string $type = PageRouter::ABSOLUTE_URL): ?UriInterface {
    if (null === ($this->site?->getRouter() ?? null)) {
      return null;
    }

    $arguments = [
      ...$additionalGetParams,
      '_language' => $this->siteLanguage,
    ];

    if (null !== $extensionName && null !== $pluginName && null !== $actionName && null !== $controllerName) {
      $arguments = [
        strtolower("tx_{$extensionName}_{$pluginName}") => [
          'action' => $actionName,
          'controller' => $controllerName,
          ...$actionArguments,
        ],
        '_language' => $this->siteLanguage,
      ];
    }

    return $this->site->getRouter()->generateUri($pageId, $arguments, $fragment, $type);
  }

  /**
   * @param ContentTypeDefinition $contentType defaults to JSON content type if omitted
   */
  public function createResponse(string $string, ContentTypeDefinition $contentType = ContentTypeDefinition::DEFAULT, string $fileName = ''): void {
    $response = $this->responseFactory->createResponse();

    switch ($contentType) {
      case ContentTypeDefinition::DEFAULT:
      case ContentTypeDefinition::JSON:
        $response = $response->withHeader('Content-Type', 'application/json; charset=utf-8');

        break;

      case ContentTypeDefinition::FILE_PLAIN:
        $response = $response
          ->withHeader('Content-Type', 'text/plain; charset=utf-8')
          ->withAddedHeader('Content-Disposition', 'attachment; filename="'.$fileName.'"')
        ;

        break;

      case ContentTypeDefinition::IMAGE_JPEG:
        $response = $response->withHeader('Content-Type', 'image/jpeg');

        break;

      case ContentTypeDefinition::XML:
        $response = $response->withHeader('Content-Type', 'application/xml; charset=utf-8');

        break;

      default:
        break;
    }

    $response->getBody()->write($string);

    $this->response = $response;
  }

  public function getAbsPath(?FileReference $file): string {
    if (null == $file) {
      return '';
    }
    if (null == $this->siteLanguage) {
      return '';
    }

    return $this->siteLanguage->getBase()->getScheme().'://'.$this->siteLanguage->getBase()->getHost().'/'.$file->getOriginalResource()->getPublicUrl();
  }

  public function getLocalizationFromKey(string $keyToTranslate, string $languageFile = ''): string {
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

  public function getRequest(): ServerRequestInterface {
    return $this->request;
  }

  /**
   * @return array<int|string, mixed>|object
   */
  public function getRequestBody(): array|object {
    return $this->requestBody;
  }

  public function getSite(): ?Site {
    return $this->site;
  }

  public function getSiteLanguage(): ?SiteLanguage {
    return $this->siteLanguage;
  }

  public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface {
    $this->request = $request;
    $this->handler = $handler;
    $this->site = $this->request->getAttribute('site');

    $contentType = $this->request->getHeaderLine('Content-Type');
    if (false !== stripos($contentType, 'application/json')) {
      /** @var array<int|string, mixed> $parsedBody */
      $parsedBody = json_decode($this->request->getBody()->getContents(), true) ?: [];
      $this->requestBody = $parsedBody;
    } else {
      $this->requestBody = $this->request->getParsedBody() ?? [];
    }

    $langId = $this->request->getQueryParams()['L'] ?? false;
    if (false !== $langId) {
      $this->siteLanguage = $this->site?->getLanguageById(intval($langId));
      $this->request = $this->request->withAttribute('language', $this->siteLanguage);
    } else {
      $this->siteLanguage = $this->request->getAttribute('language');
    }
    if (null !== $this->siteLanguage) {
      $this->languageService = $this->languageServiceFactory->createFromSiteLanguage($this->siteLanguage);
    }

    $this->handleApiRequests();

    if (isset($this->response)) {
      return $this->response;
    }

    return $this->handler->handle($this->request);
  }

  public function resizeAndCropImage(?FileReference $image, string $width = '2480', string $height = '770', string $cropArea = 'default', bool $absUrl = false): ?string {
    if (null == $image) {
      return null;
    }

    if ('svg' == strtolower($image->getOriginalResource()->getExtension())) {
      if ($absUrl) {
        if (null == $this->siteLanguage) {
          return '/'.$image->getOriginalResource()->getPublicUrl();
        }

        return $this->siteLanguage->getBase()->getScheme().'://'.$this->siteLanguage->getBase()->getHost().'/'.$image->getOriginalResource()->getPublicUrl();
      }

      return '/'.$image->getOriginalResource()->getPublicUrl();
    }

    $getImage = $this->imageService->getImage('', $image, false);

    $cropString = null;
    if ($getImage->hasProperty('crop') && $getImage->getProperty('crop')) {
      $cropString = $getImage->getProperty('crop');
    }

    $cropVariantCollection = CropVariantCollection::create(strval($cropString));
    $cropArea = $cropVariantCollection->getCropArea($cropArea);

    $processedImage = $this->imageService->applyProcessingInstructions($getImage, [
      'width' => $width,
      'height' => $height,
      'crop' => $cropArea->isEmpty() ? null : $cropArea->makeAbsoluteBasedOnFile($getImage),
    ]);

    if ($absUrl) {
      if (null == $this->siteLanguage) {
        return '/'.$processedImage->getPublicUrl();
      }

      return $this->siteLanguage->getBase()->getScheme().'://'.$this->siteLanguage->getBase()->getHost().'/'.$processedImage->getPublicUrl();
    }

    return '/'.$processedImage->getPublicUrl();
  }

  public function responseNotAuthorized(string $message = 'Not authorized'): void {
    header('HTTP/'.$this->request->getProtocolVersion().' 401 Unauthorized');
    $this->response = $this->responseFactory->createResponse(401, $message);
  }

  public function responseServerError(string $message): void {
    header('Error: '.$message);
    $this->response = $this->responseFactory->createResponse(500, 'Internal Server Error');
  }

  /**
   * @param (callable(array<string, mixed> $pathParams): void) $callable
   */
  protected function handleApiCall(string $apiName, string $apiEndpoint, callable $callable, ApiMethodDefinition $method = ApiMethodDefinition::GET): void {
    if (false !== $apiArguments = $this->getApiArguments($apiName, $apiEndpoint, $method)) {
      $callable($apiArguments);
    }
  }

  protected function handleApiRequests(): void {}

  /**
   * @return array<string, string>|false
   */
  private function getApiArguments(string $apiName, string $apiEndpoint, ApiMethodDefinition $method = ApiMethodDefinition::GET): array|false {
    if (empty($this->apis)) {
      $this->apis = $this->request->getAttribute('routing')?->getStaticArguments() ?? [];
    }
    if (!isset($this->apis[$apiName])) {
      return false;
    }

    if ($method->name != $this->request->getMethod()) {
      return false;
    }

    if (empty($this->apis[$apiName]['endpoint'] ?? '')) {
      $this->responseServerError('Missing endpoint in route config!');

      return false;
    }

    if ($apiEndpoint != $this->apis[$apiName]['endpoint']) {
      return false;
    }

    return $this->apis[$apiName];
  }
}
