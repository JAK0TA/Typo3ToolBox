<?php

// Copyright JAKOTA Design Group GmbH. All rights reserved.
declare(strict_types=1);

namespace JAKOTA\Typo3ToolBox\Middleware;

use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use TYPO3\CMS\Core\Http\Response;
use TYPO3\CMS\Core\Information\Typo3Version;
use TYPO3\CMS\Core\Utility\GeneralUtility;

abstract class MiddlewareAbstract implements MiddlewareInterface {
  /**
   * @var RequestHandlerInterface
   */
  protected $handler;

  /**
   * @var array<string, array<string, string>>
   */
  protected $pathParams = [];

  /**
   * @var array<mixed>
   */
  protected $queryParams = [];

  /**
   * @var ServerRequestInterface
   */
  protected $request;

  /**
   * @var array<mixed>|object
   */
  protected $requestBody = [];

  /**
   * @var ?ResponseInterface
   */
  protected $response;

  /**
   * @var ResponseFactoryInterface
   */
  protected $responseFactory;

  public function checkRequest(string $path, callable $callable, string $method): void {
    if ($this->request->getMethod() == $method) {
      if ($this->isPath($path) || $this->isRequestTarget($path)) {
        $this->requestBody = $this->request->getParsedBody() ?? [];
        $this->queryParams = $this->request->getQueryParams();
        $this->response = $callable($this->queryParams, $this->pathParams[$path] ?? [], $this->requestBody);
      }
    }
  }

  public function createResponse(string $string, bool $jsonOutput = true): ResponseInterface {
    $typo3Version = GeneralUtility::makeInstance(Typo3Version::class);
    if (version_compare($typo3Version->getVersion(), '10.1.0') >= 0) {
      $response = $this->responseFactory->createResponse();
    } else {
      $response = new Response();
    }

    if ($jsonOutput) {
      $response = $response->withHeader('Content-Type', 'application/json; charset=utf-8');
    }

    $response->getBody()->write($string);

    return $response;
  }

  public function delete(string $path, callable $callable): void {
    $this->checkRequest($path, $callable, 'DELETE');
  }

  public function get(string $path, callable $callable): void {
    $this->checkRequest($path, $callable, 'GET');
  }

  public function handleRequests(): ResponseInterface {
    if (isset($this->response)) {
      return $this->response;
    }

    return $this->handler->handle($this->request);
  }

  public function initMiddleware(ServerRequestInterface $request, RequestHandlerInterface $handler): void {
    $this->request = $request;
    $this->handler = $handler;
  }

  public function post(string $path, callable $callable): void {
    $this->checkRequest($path, $callable, 'POST');
  }

  public function put(string $path, callable $callable): void {
    $this->checkRequest($path, $callable, 'PUT');
  }

  protected function addPathParam(string $path, string $key, string $val): void {
    $this->pathParams[$path] = array_merge_recursive($this->pathParams[$path] ?? [], [$key => $val]);
  }

  protected function isPath(string $expectedPath): bool {
    $path = $this->request->getUri()->getPath();

    if (0 == preg_match('/\{.*\}/', $expectedPath)) {
      return $path == $expectedPath ? true : false;
    }

    return $this->isPathWithVariables($expectedPath, $path);
  }

  protected function isPathWithVariables(string $expectedPath, string $path): bool {
    $pathFragments = preg_split('/\//', $path, 0, PREG_SPLIT_NO_EMPTY) ?: [];
    $expectedPathFragments = preg_split('/\//', $expectedPath, 0, PREG_SPLIT_NO_EMPTY) ?: [];

    $equal = true;
    if (sizeof($expectedPathFragments) == sizeof($pathFragments)) {
      for ($i = 0; $i < sizeof($expectedPathFragments); ++$i) {
        if (0 == preg_match('/\{([a-zA-Z0-9]*)\}/', $expectedPathFragments[$i])) {
          if ($expectedPathFragments[$i] != $pathFragments[$i]) {
            $equal = false;
          }
        } else {
          $this->addPathParam(
            $expectedPath,
            str_replace(['{', '}'], '', $expectedPathFragments[$i]),
            $pathFragments[$i]
          );
        }
      }
    } else {
      $equal = false;
    }

    return $equal;
  }

  protected function isRequestTarget(string $expectedPath): bool {
    $path = $this->request->getRequestTarget();

    if (0 == preg_match('/\{.*\}/', $expectedPath)) {
      return $path == $expectedPath ? true : false;
    }

    return $this->isPathWithVariables($expectedPath, $path);
  }
}
