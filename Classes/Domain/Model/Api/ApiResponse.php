<?php

// Copyright JAKOTA Design Group GmbH. All rights reserved.
declare(strict_types=1);

namespace JAKOTA\Typo3ToolBox\Domain\Model\Api;

class ApiResponse {
  public string $call = '';

  public mixed $data = null;

  public int $errorCode = 0;

  public string $errorMessage = '';

  public bool $success = false;

  public function __construct() {}
}
