<?php

// Copyright JAKOTA Design Group GmbH. All rights reserved.
declare(strict_types=1);

namespace JAKOTA\Typo3ToolBox\EventListener;

use Symfony\Component\Finder\Finder;
use TYPO3\CMS\Core\Attribute\AsEventListener;
use TYPO3\CMS\Core\Configuration\Event\SiteConfigurationLoadedEvent;
use TYPO3\CMS\Core\Configuration\Loader\YamlFileLoader;
use TYPO3\CMS\Core\Package\PackageManager;

#[AsEventListener]
final readonly class SiteConfigurationLoadedEventListener {
  public function __construct(
    private PackageManager $packageManager,
    private YamlFileLoader $yamlFileLoader,
  ) {}

  public function __invoke(SiteConfigurationLoadedEvent $event): void {
    $configuration = $event->getConfiguration();
    foreach ($this->packageManager->getActivePackages() as $package) {
      $extra = $package->getValueFromComposerManifest('extra');
      if (!is_object($extra)) {
        continue;
      }

      if (!property_exists($extra, 'importRoutes') || true !== $extra->importRoutes) {
        continue;
      }

      $routesPath = $package->getPackagePath().'Configuration/Routes/';
      if (!\is_dir($routesPath)) {
        continue;
      }

      $finder = new Finder();
      $finder
        ->files()
        ->name('*.yaml')
        ->in($routesPath)
      ;

      if (!$finder->hasResults()) {
        continue;
      }

      foreach ($finder as $file) {
        $routeConfiguration = $this->yamlFileLoader->load($file->getRealPath());
        $configuration = \array_merge_recursive(
          $configuration,
          $routeConfiguration,
        );
      }
    }

    $event->setConfiguration($configuration);
  }
}
