<?php

// Copyright JAKOTA Design Group GmbH. All rights reserved.
declare(strict_types=1);

namespace JAKOTA\Typo3ToolBox\Enhancer;

use TYPO3\CMS\Core\Routing\Enhancer\AbstractEnhancer;
use TYPO3\CMS\Core\Routing\Enhancer\InflatableEnhancerInterface;
use TYPO3\CMS\Core\Routing\Enhancer\ResultingInterface;
use TYPO3\CMS\Core\Routing\Enhancer\RoutingEnhancerInterface;
use TYPO3\CMS\Core\Routing\PageArguments;
use TYPO3\CMS\Core\Routing\Route;
use TYPO3\CMS\Core\Routing\RouteCollection;

/**
 * routeEnhancers
 *   ApiName:
 *     type: ApiEnhancer
 *     api: ApiName
 *     routes:
 *       - { routePath: '/api/path/to/endpoint1/{version}/', endpoint: 'endpointName1', _arguments: { 'version': 'version' } }.
 *       - { routePath: '/api/path/to/endpoint2/{version}/{var}/', endpoint: 'endpointName2', _arguments: { 'var': 'var', 'version': 'version' } }.
 */
class ApiEnhancer extends AbstractEnhancer implements RoutingEnhancerInterface, InflatableEnhancerInterface, ResultingInterface {
  protected string $api = '';

  /**
   * @var array<string, mixed>
   */
  protected array $routesOfApi = [];

  /**
   * @param array<string, mixed> $configuration
   */
  public function __construct(protected array $configuration) {
    $this->routesOfApi = is_array($this->configuration['routes'] ?? []) ? $this->configuration['routes'] ?? [] : [];
    $this->api = strval($this->configuration['api'] ?? '');
  }

  /**
   * @param array<string, mixed> $results
   * @param array<string, mixed> $remainingQueryParameters
   */
  public function buildResult(Route $route, array $results, array $remainingQueryParameters = []): PageArguments {
    // determine those parameters that have been processed
    $parameters = array_intersect_key(
      $results,
      array_flip($route->compile()->getPathVariables())
    );

    // strip of those that where not processed (internals like _route, etc.)
    $internals = array_diff_key($results, $parameters);

    // all route arguments
    $routeArguments = $this->inflateParameters($parameters, $internals);
    if (is_array($routeArguments[$this->api])) {
      $routeArguments[$this->api]['endpoint'] = $route->getOption('endpoint') ?? '';
    }

    $page = (array) $route->getOption('_page');

    $pageId = intval(($page['t3ver_oid'] ?? 0) > 0 ? $page['t3ver_oid'] : $page['uid']);
    $pageId = intval(($page['l10n_parent'] ?? 0) > 0 ? $page['l10n_parent'] : $pageId);
    // See PageSlugCandidateProvider where this is added.
    if ($page['MPvar'] ?? '') {
      $routeArguments['MP'] = $page['MPvar'];
    }

    $type = $this->resolveType($route, $remainingQueryParameters);

    return new PageArguments($pageId, $type, [], $routeArguments, $remainingQueryParameters);
  }

  /**
   * @param array<string, mixed> $parameters
   */
  public function enhanceForGeneration(RouteCollection $collection, array $parameters): void {}

  public function enhanceForMatching(RouteCollection $collection): void {
    $i = 0;

    /** @var Route $defaultPageRoute */
    $defaultPageRoute = $collection->get('default');
    foreach ($this->routesOfApi as $configuration) {
      if (!is_array($configuration)) {
        continue;
      }
      $route = $this->getVariant($defaultPageRoute, $configuration);
      $collection->add($this->api.'_'.$i++, $route);
    }
  }

  /**
   * A route has matched the controller/action combination, so ensure that these properties
   * are set to tx_blogexample_pi1[controller] and tx_blogexample_pi1[action].
   *
   * @param array<string, mixed> $parameters Actual parameter payload to be used
   * @param array<string, mixed> $internals  Internal instructions (_route, _controller, ...)
   *
   * @return array<string, mixed>
   */
  public function inflateParameters(array $parameters, array $internals = []): array {
    $parameters = $this->getVariableProcessor()
      ->inflateNamespaceParameters($parameters, $this->api)
    ;
    $parameters[$this->api] = $parameters[$this->api] ?? [];

    return $parameters;
  }

  /**
   * @param array<mixed> $configuration
   */
  protected function getVariant(Route $defaultPageRoute, array $configuration): Route {
    $arguments = $configuration['_arguments'] ?? [];
    if (!is_array($arguments)) {
      $arguments = [];
    }
    unset($configuration['_arguments']);

    $variableProcessor = $this->getVariableProcessor();
    $routePath = $this->modifyRoutePath(strval($configuration['routePath']));
    $routePath = $variableProcessor->deflateRoutePath($routePath, $this->api, $arguments);
    unset($configuration['routePath']);
    $options = array_merge($defaultPageRoute->getOptions(), ['_enhancer' => $this, 'utf8' => true, '_arguments' => $arguments, 'endpoint' => $configuration['endpoint'] ?? '']);
    $route = new Route(rtrim($defaultPageRoute->getPath(), '/').'/'.ltrim($routePath, '/'), [], [], $options);

    $configurationDefaults = $this->configuration['defaults'] ?? [];
    if (!is_array($configurationDefaults)) {
      $configurationDefaults = [];
    }
    $defaults = array_merge_recursive(
      $defaultPageRoute->getDefaults(),
      $variableProcessor->deflateKeys($configurationDefaults, $this->api, $arguments)
    );
    // only keep `defaults` that are actually used in `routePath`
    $defaults = $this->filterValuesByPathVariables(
      $route,
      $defaults
    );

    $route->setDefaults($defaults);
    $this->applyRouteAspects($route, $this->aspects, $this->api);

    return $route;
  }
}
